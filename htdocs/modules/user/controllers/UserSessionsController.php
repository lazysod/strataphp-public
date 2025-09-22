<?php
namespace App\Modules\User\Controllers;

use App\DB;

class UserSessionsController
{
    public function index()
    {
        include_once dirname(__DIR__, 3) . '/app/start.php';
        $config = include dirname(__DIR__, 3) . '/app/config.php';
        $db = new DB($config);
        $user_id = $_SESSION[PREFIX . 'user_id'] ?? null;
        if (!$user_id) {
            header('Location: /user/login');
            exit;
        }
    // Only show latest active session per device (not revoked)
    $sessions = $db->fetchAll("SELECT * FROM user_sessions WHERE user_id = ? AND revoked = 0 AND id IN (SELECT MAX(id) FROM user_sessions WHERE user_id = ? AND revoked = 0 GROUP BY device_id)", [$user_id, $user_id]);
        include __DIR__ . '/../views/sessions.php';
    }
    public function revoke()
    {
        include_once dirname(__DIR__, 3) . '/app/start.php';
        $config = include dirname(__DIR__, 3) . '/app/config.php';
        $db = new DB($config);
        $user_id = $_SESSION[PREFIX . 'user_id'] ?? null;
        if (!$user_id) {
            header('Location: /user/login');
            exit;
        }
        
        // Get session_id from POST data
        $session_id = $_POST['session_id'] ?? null;
        if (!$session_id) {
            header('Location: /user/sessions');
            exit;
        }
        
        // Revoke session
        $db->query("UPDATE user_sessions SET revoked = 1 WHERE id = ? AND user_id = ?", [$session_id, $user_id]);
        header('Location: /user/sessions');
        exit;
    }

    // Allow user to update device name for current session
    public function updateDevice()
    {
        include_once dirname(__DIR__, 3) . '/app/start.php';
        $config = include dirname(__DIR__, 3) . '/app/config.php';
        $db = new DB($config);
        $user_id = $_SESSION[PREFIX . 'user_id'] ?? null;
        $session_id = $_POST['session_id'] ?? null;
        $device_info = trim($_POST['device_info'] ?? '');
        if (!$user_id || !$session_id || $device_info === '') {
            header('Location: /user/sessions');
            exit;
        }
        // Only allow update for current session
        if ($session_id == ($_SESSION[PREFIX . 'session_id'] ?? null)) {
            $db->query("UPDATE user_sessions SET device_info = ? WHERE id = ? AND user_id = ?", [$device_info, $session_id, $user_id]);
        }
        header('Location: /user/sessions');
        exit;
    }
}
