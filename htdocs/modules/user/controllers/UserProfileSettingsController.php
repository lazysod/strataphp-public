<?php
// UserProfileSettingsController: handles /user/profile_settings
namespace App\Modules\User\Controllers;

use App\DB;
use App\User;
use App\App;

/**
 * User Profile Settings Controller
 *
 * Handles user profile settings and avatar upload.
 * Includes error handling and documentation comments.
 */
class UserProfileSettingsController
{
    /**
     * Display and manage user profile settings
     * @return void
     */
    public function index()
    {
        global $config;
        require_once dirname(__DIR__, 4) . '/bootstrap.php';
        $sessionPrefix = $config['session_prefix'] ?? 'app_';
        try {
            if (!isset($_SESSION[$sessionPrefix . 'user_id']) || $_SESSION[$sessionPrefix . 'user_id'] < 1) {
                header('Location: /user/login');
                exit;
            }
            $db = new DB($config);
            $userModel = new User($db, $config);
            $userId = $_SESSION[$sessionPrefix . 'user_id'];
            $profile_id = $_SESSION[$sessionPrefix . 'active_profile'] ?? null;
            $success = '';
            $error = '';
            // Handle avatar upload
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
                $allowedTypes = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/webp' => 'webp'];
                $fileType = mime_content_type($_FILES['avatar']['tmp_name']);
                if (isset($allowedTypes[$fileType])) {
                    $ext = $allowedTypes[$fileType];
                    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/app/uploads/img/profile/' . $profile_id . '/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
                    // Remove all files in avatar directory
                    $files = glob($uploadDir . '*');
                    if ($files) {
                        foreach ($files as $oldFile) {
                            if (is_file($oldFile)) @unlink($oldFile);
                        }
                    }
                    $fileName = time() . '.' . $ext;
                    $destPath = $uploadDir . $fileName;
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destPath)) {
                        $avatarDbPath = $profile_id . '/' . $fileName;
                        $_SESSION[$sessionPrefix . 'profile']['profile_image'] = $avatarDbPath;
                        // Update users table immediately
                        // $db->query("UPDATE users SET avatar = ? WHERE id = ?", [$avatarDbPath, $userId]);
                        $db->query("UPDATE profile SET profile_image = ? WHERE profile_id = ?", [$avatarDbPath, $_SESSION[$sessionPrefix . 'active_profile'] ?? null]);
                        $success = 'Avatar updated successfully!';
                    } else {
                        $error = 'Failed to save avatar.';
                    }
                } else {
                    $error = 'Invalid avatar file type.';
                }
            }
            // $profile = $userModel->get_profile($userId);
            $profile = $userModel->get_selected_profile($_SESSION[$sessionPrefix . 'active_profile'] ?? null);
            $profile_image = $userModel->get_profile_image($profile['id'] ?? null);
            if (!$profile || !is_array($profile)) {
                $profile = [
                'bio' => '',
                'pride_logo' => 0,
                'verified' => 0,
                'profile_name' => '',
                'locked' => 0,
            ];
        }
        $userCLass = new User(new DB($config['db']), $config);
        $profile_list = $userCLass->get_profiles($_SESSION[$sessionPrefix . 'user_id']);
        // App::dump($profile, 'User Profile Data');
        include dirname(__DIR__) . '/views/profile_settings.php';
    }
}
