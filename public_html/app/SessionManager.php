<?php
namespace App;
/**
 * Security requirements for this class:
 * 1. Session tokens MUST be hashed with sha256 before DB storage
 * 2. All cookies MUST use Secure, HttpOnly, SameSite=Lax
 * 3. Device ID MUST be random, not derived from User-Agent or IP
 * 4. validateSession() MUST check expires_at > NOW()
 * 5. Use hash_equals() for all token comparisons
 * 6. Get client IP safely behind proxies
 */
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
        $device_id = $this->getOrCreateDeviceId();
        $device_type = $this->detectDeviceType();
        $ip_address = $this->getClientIp();
        session_regenerate_id(true); // Prevent session fixation
        $session_token = bin2hex(random_bytes(32));
        $session_token_hash = hash('sha256', $session_token);

        $now = date('Y-m-d H:i:s');
        $expiryDays = isset($this->config['session_expiry_days']) ? (int)$this->config['session_expiry_days'] : 1;
        $expires_at = date('Y-m-d H:i:s', strtotime("+{$expiryDays} days"));

        // Insert session into DB (now with ip_address and expires_at)
        $sql = "INSERT INTO user_sessions (user_id, device_id, device_type, ip_address, session_token, revoked, last_seen, created_at, expires_at) VALUES (?, ?, ?, ?, ?, 0, ?, ?, ?)";
        $this->db->query($sql, [$user_id, $device_id, $device_type, $ip_address, $session_token_hash, $now, $now, $expires_at]);
        $session_id = $this->db->insertId();

        // Set session_id in $_SESSION for tracking current session
        $sessionPrefix = $this->config['session_prefix'] ?? 'app_';
        $_SESSION[$sessionPrefix . 'session_id'] = $session_id;

        // Only set persistent cookies if both 'remember' and cookie consent are true
        $cookieConsent = isset($_COOKIE['cookie_consent']) && $_COOKIE['cookie_consent'] === '1';
        if ($persistent && $cookieConsent) {
            $expire = time() + (60 * 60 * 24 * 10); // 10 days
        } else {
            $expire = 0; // Session cookie
        }
        $prefix = $this->config['session_prefix'] ?? 'app_';
        $cookieOpts = [
            'expires' => $expire,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax',
        ];
        setcookie($prefix . 'session_token', $session_token, $cookieOpts);
        setcookie($prefix . 'device_id', $device_id, $cookieOpts);
    }

    /**
     * Reuse device_id from cookie if valid, else generate new
     */
    private function getOrCreateDeviceId(): string
    {
        $prefix = $this->config['session_prefix'] ?? 'app_';
        $cookieName = $prefix . 'device_id';
        if (!empty($_COOKIE[$cookieName]) && preg_match('/^[a-f0-9]{32}$/', $_COOKIE[$cookieName])) {
            return $_COOKIE[$cookieName];
        }
        return bin2hex(random_bytes(16));
    }

    public function validateSession()
    {
        $prefix = $this->config['session_prefix'] ?? 'app_';
        if (!isset($_COOKIE[$prefix . 'session_token']) || !isset($_COOKIE[$prefix . 'device_id'])) {
            return false;
        }
        $session_token = $_COOKIE[$prefix . 'session_token'];
        $device_id = $_COOKIE[$prefix . 'device_id'];
        $session_token_hash = hash('sha256', $session_token);
        $sql = "SELECT * FROM user_sessions WHERE session_token = ? AND device_id = ? AND revoked = 0";
        $rows = $this->db->fetchAll($sql, [$session_token_hash, $device_id]);
        if (count($rows) < 1) {
            return false;
        }
        $session = $rows[0];
        // Check expiry
        if (isset($session['expires_at']) && strtotime($session['expires_at']) < time()) {
            return false;
        }
        // Update last_seen if >5min (idempotent)
        $sql = "UPDATE user_sessions SET last_seen = NOW() WHERE id = ? AND last_seen < NOW() - INTERVAL 5 MINUTE";
        $this->db->query($sql, [$session['id']]);
        return $session['user_id'];
    }

    public function revokeSession($session_id)
    {
        $sql = "UPDATE user_sessions SET revoked = 1 WHERE id = ?";
        $this->db->query($sql, [$session_id]);
    }

    // getDeviceId() is now replaced by random device ID in createSession

    private function detectDeviceType()
    {
        $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
        if (strpos($ua, 'iphone') !== false) {
            return 'iPhone';
        }
        if (strpos($ua, 'android') !== false && strpos($ua, 'mobile') !== false) {
            return 'Android Phone';
        }
        if (strpos($ua, 'android') !== false) {
            return 'Android Tablet';
        }
        if (strpos($ua, 'ipad') !== false) {
            return 'iPad';
        }
        if (strpos($ua, 'windows') !== false || strpos($ua, 'macintosh') !== false) {
            if (strpos($ua, 'tablet') !== false) {
                return 'Tablet';
            }
            return 'Desktop/Laptop';
        }
        return 'Unknown';
    }

    private function getClientIp(): string
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim(end($ips)); // last = client, first = spoofable
            if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
        }
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }

    /**
     * Destroy session, revoke in DB, nuke cookies, and destroy PHP session
     */
    public function destroySession(): void
    {
        $prefix = $this->config['session_prefix'] ?? 'app_';
        $sessionId = $_SESSION[$prefix . 'session_id'] ?? null;
        if ($sessionId) {
            $this->revokeSession($sessionId);
        }
        // Nuke cookies
        $cookieOpts = ['expires' => time()-3600, 'path' => '/', 'secure' => true, 'httponly' => true, 'samesite' => 'Lax'];
        setcookie($prefix . 'session_token', '', $cookieOpts);
        setcookie($prefix . 'device_id', '', $cookieOpts);
        session_destroy();
    }
}
