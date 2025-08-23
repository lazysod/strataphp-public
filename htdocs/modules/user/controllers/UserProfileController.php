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
            } elseif ( empty($_POST['email'])) {
                $error = 'Please fill in all required fields.';
            } else {
                // Proceed with update
                if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid email address.';
                }
            }
            // Avatar upload
            $avatarPath = '';
            if ($error == '' && isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/webp' => 'webp'];
                $fileType = mime_content_type($_FILES['avatar']['tmp_name']);
                if (isset($allowedTypes[$fileType])) {
                    $ext = $allowedTypes[$fileType];
                    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/storage/uploads/users/' . $userId . '/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
                    // Remove existing avatar files
                    foreach (['png', 'jpg', 'jpeg', 'webp'] as $oldExt) {
                        $oldFile = $uploadDir . 'avatar.' . $oldExt;
                        if (file_exists($oldFile)) @unlink($oldFile);
                    }
                    $fileName = 'avatar.' . $ext;
                    $destPath = $uploadDir . $fileName;
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destPath)) {
                        $avatarPath = '/storage/uploads/users/' . $userId . '/' . $fileName;
                    } else {
                        $error = 'Failed to save avatar.';
                    }
                } else {
                    $error = 'Invalid avatar file type.';
                }
            }
            if ($error == '') {
                $updateInfo = [
                    'id' => $userId,
                    'display_name' => trim($_POST['display_name'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'pwd' => $_POST['pwd'] ?? '',
                    'pwd2' => $_POST['pwd2'] ?? '',
                    'avatar' => $avatarPath,
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
