<?php
namespace App;

class SessionManager
{
    private $db;
    private $config;

    public function __construct($db, $config)
    {
        $this->db = $db;
        $this->config = $config;
    }

    public function createSession($user_id, $persistent = false)
    {
        // 1. Regenerate BEFORE we write anything. If this fails, we bail before login.
        // if (session_status() === PHP_SESSION_ACTIVE) {
        //     // session_regenerate_id(true);
        // }
        $device_id = $this->getOrCreateDeviceId();
        $device_type = $this->detectDeviceType();
        $ip_address = $this->getClientIp();

        $session_token = bin2hex(random_bytes(32));
        $session_token_hash = hash('sha256', $session_token);

        $now = date('Y-m-d H:i:s');
        $expiryDays = isset($this->config['session_expiry_days'])? (int)$this->config['session_expiry_days'] : 1;
        $expires_at = date('Y-m-d H:i:s', strtotime("+{$expiryDays} days"));

        $sql = "INSERT INTO user_sessions (user_id, device_id, device_type, ip_address, session_token, revoked, last_seen, created_at, expires_at) VALUES (?,?,?,?,?, 0,?,?,?)";
        $this->db->query($sql, [$user_id, $device_id, $device_type, $ip_address, $session_token_hash, $now, $now, $expires_at]);
        $session_id = $this->db->insertId();

        $sessionPrefix = $this->config['session_prefix']?? 'app_';
        $_SESSION[$sessionPrefix. 'session_id'] = $session_id;
        // 
        $logger = Logger::getInstance();
        $logger->debug('SessionManager: createSession called', ['user_id' => $user_id]);
        $logger->info('Session created!', ['session_id' => $session_id]);
        $logger->error('DB insert failed', ['error' => json_encode($this->db->errorInfo())]);

        // Only set cookies if headers not sent. If they are, log it and skip.
        if (!headers_sent()) {
            $cookieConsent = isset($_COOKIE['cookie_consent']) && $_COOKIE['cookie_consent'] === '1';
            if ($persistent && $cookieConsent) {
                $expire = time() + (60 * 60 * 24 * 10); // 10 days
            } else {
                $expire = 0; // Session cookie
            }
            $prefix = $this->config['session_prefix']?? 'app_';
            $cookieOpts = [
                'expires' => $expire,
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax',
            ];
            setcookie($prefix. 'session_token', $session_token, $cookieOpts);
            setcookie($prefix. 'device_id', $device_id, $cookieOpts);
        } else {
            $logger = Logger::getInstance();
            $logger->warning('SessionManager: headers already sent, cannot set cookies');
        }
    }

    private function getOrCreateDeviceId(): string
    {
        $prefix = $this->config['session_prefix']?? 'app_';
        $cookieName = $prefix. 'device_id';
        if (!empty($_COOKIE[$cookieName]) && preg_match('/^[a-f0-9]{32}$/', $_COOKIE[$cookieName])) {
            return $_COOKIE[$cookieName];
        }
        return bin2hex(random_bytes(16));
    }

    public function validateSession()
    {
        $prefix = $this->config['session_prefix']?? 'app_';
        if (!isset($_COOKIE[$prefix. 'session_token']) ||!isset($_COOKIE[$prefix. 'device_id'])) {
            return false;
        }
        $session_token = $_COOKIE[$prefix. 'session_token'];
        $device_id = $_COOKIE[$prefix. 'device_id'];
        $session_token_hash = hash('sha256', $session_token);
        $sql = "SELECT * FROM user_sessions WHERE session_token =? AND device_id =? AND revoked = 0";
        $rows = $this->db->fetchAll($sql, [$session_token_hash, $device_id]);
        if (count($rows) < 1) {
            return false;
        }
        $session = $rows[0];
        if (isset($session['expires_at']) && strtotime($session['expires_at']) < time()) {
            return false;
        }
        $sql = "UPDATE user_sessions SET last_seen = NOW() WHERE id =? AND last_seen < NOW() - INTERVAL 5 MINUTE";
        $this->db->query($sql, [$session['id']]);
        return $session['user_id'];
    }

    public function revokeSession($session_id)
    {
        $sql = "UPDATE user_sessions SET revoked = 1 WHERE id =?";
        $this->db->query($sql, [$session_id]);
    }

    private function detectDeviceType()
    {
        $ua = strtolower($_SERVER['HTTP_USER_AGENT']?? '');
        if (strpos($ua, 'iphone')!== false) return 'iPhone';
        if (strpos($ua, 'android')!== false && strpos($ua, 'mobile')!== false) return 'Android Phone';
        if (strpos($ua, 'android')!== false) return 'Android Tablet';
        if (strpos($ua, 'ipad')!== false) return 'iPad';
        if (strpos($ua, 'windows')!== false || strpos($ua, 'macintosh')!== false) {
            if (strpos($ua, 'tablet')!== false) return 'Tablet';
            return 'Desktop/Laptop';
        }
        return 'Unknown';
    }

    private function getClientIp(): string
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim(end($ips));
            if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
        }
        return $_SERVER['REMOTE_ADDR']?? '';
    }

    public function destroySession(): void
    {
        $prefix = $this->config['session_prefix']?? 'app_';
        $sessionId = $_SESSION[$prefix. 'session_id']?? null;
        if ($sessionId) {
            $this->revokeSession($sessionId);
        }
        if (!headers_sent()) {
            $cookieOpts = ['expires' => time()-3600, 'path' => '/', 'secure' => isset($_SERVER['HTTPS']), 'httponly' => true, 'samesite' => 'Lax'];
            setcookie($prefix. 'session_token', '', $cookieOpts);
            setcookie($prefix. 'device_id', '', $cookieOpts);
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            session_destroy();
        }
    }
}