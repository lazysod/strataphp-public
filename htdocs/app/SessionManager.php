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
        $device_id = $this->getDeviceId();
        $device_type = $this->detectDeviceType();
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $session_token = bin2hex(random_bytes(32));
        $now = date('Y-m-d H:i:s');

        // Insert session into DB (now with ip_address)
        $sql = "INSERT INTO user_sessions (user_id, device_id, device_type, ip_address, session_token, revoked, last_seen, created_at) VALUES (?, ?, ?, ?, ?, 0, ?, ?)";
        $this->db->query($sql, [$user_id, $device_id, $device_type, $ip_address, $session_token, $now, $now]);

        // Only set persistent cookies if both 'remember' and cookie consent are true
        $cookieConsent = isset($_COOKIE['cookie_consent']) && $_COOKIE['cookie_consent'] === '1';
        if ($persistent && $cookieConsent) {
            $expire = time() + (60 * 60 * 24 * 10); // 10 days
        } else {
            $expire = 0; // Session cookie
        }
        setcookie(PREFIX . 'session_token', $session_token, $expire, '/');
        setcookie(PREFIX . 'device_id', $device_id, $expire, '/');
    }

    public function validateSession()
    {
        if (!isset($_COOKIE[PREFIX . 'session_token']) || !isset($_COOKIE [PREFIX . 'device_id'])) {
            return false;
        }
        $session_token = $_COOKIE[PREFIX . 'session_token'];
        $device_id = $_COOKIE[PREFIX . 'device_id'];
        $sql = "SELECT * FROM user_sessions WHERE session_token = ? AND device_id = ? AND revoked = 0";
        $rows = $this->db->fetchAll($sql, [$session_token, $device_id]);
        if (count($rows) < 1) {
            return false;
        }
        $session = $rows[0];
        // Update last_seen if >5min
        $last_seen = strtotime($session['last_seen']);
        if (time() - $last_seen > 300) {
            $now = date('Y-m-d H:i:s');
            $sql = "UPDATE user_sessions SET last_seen = ? WHERE id = ?";
            $this->db->query($sql, [$now, $session['id']]);
        }
        return $session['user_id'];
    }

    public function revokeSession($session_id)
    {
        $sql = "UPDATE user_sessions SET revoked = 1 WHERE id = ?";
        $this->db->query($sql, [$session_id]);
    }

    private function getDeviceId()
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        return hash('sha256', $ua . $ip);
    }

    private function detectDeviceType()
    {
        $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
        if (strpos($ua, 'iphone') !== false) return 'iPhone';
        if (strpos($ua, 'android') !== false && strpos($ua, 'mobile') !== false) return 'Android Phone';
        if (strpos($ua, 'android') !== false) return 'Android Tablet';
        if (strpos($ua, 'ipad') !== false) return 'iPad';
        if (strpos($ua, 'windows') !== false || strpos($ua, 'macintosh') !== false) {
            if (strpos($ua, 'tablet') !== false) return 'Tablet';
            return 'Desktop/Laptop';
        }
        return 'Unknown';
    }
}
