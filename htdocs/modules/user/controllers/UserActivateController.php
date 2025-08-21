<?php
// modules/user/controllers/UserActivateController.php
class UserActivateController
{
    public function index()
    {
        include_once dirname(__DIR__, 3) . '/app/start.php';
        $config = include dirname(__DIR__, 3) . '/app/config.php';
        $success = '';
        $error = '';
        $key = $_GET['key'] ?? '';
        if (!$key) {
            $error = 'Invalid activation link.';
            include __DIR__ . '/../views/activate.php';
            return;
        }
        $db = new DB($config);
        $sql = "SELECT * FROM user_activation WHERE activation_key = ?";
        $rows = $db->fetchAll($sql, [$key]);
        if (count($rows) === 0) {
            $error = 'Invalid or expired activation link.';
            include __DIR__ . '/../views/activate.php';
            return;
        }
        $activation = $rows[0];
        $expiry = $activation['expiry_date'] ?? '';
        $expiry_ts = $expiry ? strtotime($expiry) : false;
        if (!$expiry_ts || $expiry_ts < time()) {
            $error = 'This activation link has expired or is invalid.';
            include __DIR__ . '/../views/activate.php';
            return;
        }
        // Activate user
        $db->query('UPDATE users SET active = 1 WHERE id = ?', [$activation['user_id']]);
        $db->query('DELETE FROM user_activation WHERE id = ?', [$activation['id']]);
        $success = 'Your account has been activated! You can now <a href="/user/login">login</a>.';
        include __DIR__ . '/../views/activate.php';
    }
}
