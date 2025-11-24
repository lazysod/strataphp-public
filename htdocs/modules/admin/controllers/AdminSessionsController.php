<?php
namespace App\Modules\Admin\Controllers;

/**
 * Admin Sessions Controller
 * 
 * Manages admin user sessions including viewing active sessions,
 * revoking sessions, and updating device information
 */
class AdminSessionsController
{
    /**
     * Display active admin sessions
     * 
     * @return void
     */
    public function index()
    {
        try {
            include_once __DIR__ . '/../../../app/start.php';
            $config = include __DIR__ . '/../../../app/config.php';
            $sessionPrefix = $config['session_prefix'] ?? ($config['prefix'] ?? 'app_');
            $db = new \App\DB($config);
            $admin_id = $_SESSION[$sessionPrefix . 'admin'] ?? null;
            if (!$admin_id) {
                header('Location: /admin/login');
                exit;
            }
        // Only show latest active session per device (not revoked) for admin in user_sessions
        $sessions = $db->fetchAll("SELECT * FROM user_sessions WHERE user_id = ? AND revoked = 0 AND id IN (SELECT MAX(id) FROM user_sessions WHERE user_id = ? AND revoked = 0 GROUP BY device_id)", [$admin_id, $admin_id]);
            include __DIR__ . '/../views/sessions.php';
        } catch (\Exception $e) {
            http_response_code(500);
            echo '<h1>Error loading sessions</h1>';
        }
    }
    /**
     * Revoke an admin session
     * 
     * @return void
     */
    public function revoke()
    {
        try {
            include_once __DIR__ . '/../../../app/start.php';
            $config = include __DIR__ . '/../../../app/config.php';
            $sessionPrefix = $config['session_prefix'] ?? ($config['prefix'] ?? 'app_');
            $db = new \App\DB($config);
            $admin_id = $_SESSION[$sessionPrefix . 'admin'] ?? null;
            $session_id = $_POST['session_id'] ?? null;
            if (!$admin_id || !$session_id) {
                header('Location: /admin/sessions');
                exit;
            }
            // Revoke session in user_sessions
            $db->query("UPDATE user_sessions SET revoked = 1 WHERE id = ? AND user_id = ?", [$session_id, $admin_id]);
            header('Location: /admin/sessions');
            exit;
        } catch (\Exception $e) {
            header('Location: /admin/sessions');
            exit;
        }
    }

    /**
     * Allow admin to update device name for current session
     * 
     * @return void
     */
    public function updateDevice()
    {
        try {
            include_once __DIR__ . '/../../../app/start.php';
            $config = include __DIR__ . '/../../../app/config.php';
            $sessionPrefix = $config['session_prefix'] ?? ($config['prefix'] ?? 'app_');
            $db = new \App\DB($config);
            $admin_id = $_SESSION[$sessionPrefix . 'admin'] ?? null;
            $session_id = $_POST['session_id'] ?? null;
            $device_info = trim($_POST['device_info'] ?? '');
            if (!$admin_id || !$session_id || $device_info === '') {
                header('Location: /admin/sessions');
                exit;
            }
            // Only allow update for current session
            if ($session_id == ($_SESSION[$sessionPrefix . 'session_id'] ?? null)) {
                $db->query("UPDATE user_sessions SET device_info = ? WHERE id = ? AND user_id = ?", [$device_info, $session_id, $admin_id]);
            }
            header('Location: /admin/sessions');
            exit;
        } catch (\Exception $e) {
            header('Location: /admin/sessions');
            exit;
        }
    }
}
