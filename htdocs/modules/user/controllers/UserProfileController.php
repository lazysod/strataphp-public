<?php
// User profile controller for updating user details
class UserProfileController
{
    public function index()
    {
        include_once dirname(__DIR__, 3) . '/app/start.php';
        $config = include dirname(__DIR__, 3) . '/app/config.php';
        if (empty($config['modules']['user'])) {
            header('Location: /');
            exit;
        }
        if (empty($_SESSION[PREFIX . 'user_id'])) {
            header('Location: /user/login');
            exit;
        }
        $error = '';
        $success = '';
        $db = new DB($config);
        $userModel = new User($db, $config);
        $userId = $_SESSION[PREFIX . 'user_id'];
        // Fetch current user info
        $sql = "SELECT * FROM users WHERE id = ?";
        $rows = $db->fetchAll($sql, [$userId]);
        $user = $rows[0] ?? [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['pwd']) && $_POST['pwd'] != $_POST['pwd2']) {
                $error = 'Passwords do not match.';
            } elseif (empty($_POST['first_name']) || empty($_POST['second_name']) || empty($_POST['email'])) {
                $error = 'Please fill in all required fields.';
            } else {
                // Proceed with update
                if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid email address.';
                }
            }
            if ($error == '') {
                $updateInfo = [
                    'id' => $userId,
                    'first_name' => trim($_POST['first_name'] ?? ''),
                    'second_name' => trim($_POST['second_name'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'pwd' => $_POST['pwd'] ?? '',
                    'pwd2' => $_POST['pwd2'] ?? '',
                ];
                $result = $userModel->update($updateInfo);
                if ($result['status'] === 'success') {
                    $success = $result['message'];
                    // Refresh user info
                    $rows = $db->fetchAll($sql, [$userId]);
                    $user = $rows[0] ?? [];
                } else {
                    $error = $result['message'];
                }
            }
        }
        include __DIR__ . '/../views/profile.php';
    }
}
