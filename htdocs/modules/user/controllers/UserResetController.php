<?php

class UserResetController
{
    public function index()
    {
        include_once dirname(__DIR__, 3) . '/app/start.php';
        $config = include dirname(__DIR__, 3) . '/app/config.php';
        $error = '';
        $success = '';
        // Validate token and get user_id
        $db = new DB($config);
        $token = $_GET['token'] ?? '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['reset_token'] ?? $token;
        }
        if (empty($token)) {
            $error = 'Invalid or missing token.';
        } else {
            $sql = "SELECT user_id, expiry_date FROM reset WHERE `key` = ?";
            $rows = $db->fetchAll($sql, [$token]);
            if (count($rows) === 0) {
                $error = 'Invalid or expired token.';
            } else {
                $userId = $rows[0]['user_id'];
                $expiry = $rows[0]['expiry_date'];
                if (strtotime($expiry) < time()) {
                    $error = 'This reset link has expired.';
                } else {
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $tm = new TokenManager();
                        $result = $tm->verify($_POST['token'] ?? '');
                        if ($result['status'] !== 'success') {
                            $error = 'Invalid CSRF token. Please refresh and try again.';
                        } else {
                            $pwd = $_POST['pwd'] ?? '';
                            $pwd2 = $_POST['pwd2'] ?? '';
                            if ($pwd !== $pwd2) {
                                $error = 'Passwords do not match.';
                            } elseif (strlen($pwd) < 6) {
                                $error = 'Password must be at least 6 characters.';
                            } else {
                                // Update password in DB, invalidate token
                                $success = 'Your password has been reset.';
                                $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
                                $sql = "UPDATE users SET pwd = ? WHERE id = ?";
                                $db->query($sql, [$hashedPwd, $userId]);
                                // Invalidate the token
                                $sql = "DELETE FROM reset WHERE `key` = ?";
                                $db->query($sql, [$token]);
                            }
                        }
                    }
                }
            }
        }
        include __DIR__ . '/../views/reset.php';
    }
}
