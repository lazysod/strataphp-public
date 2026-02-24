
<?php
namespace App\Modules\User\Controllers;

use App\DB;
use App\User;
use App\Themes\UserTheme;

/**
 * Profile Preview Controller
 *
 * Handles user profile preview functionality.
 * Includes error handling and PSR-4 namespace.
 */
class ProfilePreviewController
{
    /**
     * Show the profile preview page for the logged-in user
     * @return void
     */
    public function show()
    {
        global $config;
        require_once dirname(__DIR__, 4) . '/bootstrap.php';
        $sessionPrefix = $config['session_prefix'] ?? 'app_';
        try {
            if (empty($_SESSION[$sessionPrefix . 'user_id'])) {
                header('Location: /user/login');
                exit;
            }
            $db = new DB($config);
            $userModel = new User($db, $config);
            $themeService = new UserTheme($db, $config);
            $userId = $_SESSION[$sessionPrefix . 'user_id'];
            $profile = $userModel->get_profile($userId);
            $themes = $themeService->get_all();
            // Handle theme preview
            $previewTheme = $_POST['preview_theme'] ?? $profile['theme_path'] ?? 'default';
            foreach ($themes as $theme) {
                if ($theme['theme_path'] === $previewTheme) {
                    $themeData = $theme;
                    break;
                }
            }
            if (!isset($themeData)) {
                $themeData = $themeService->get_default_theme();
            }
            // Render preview page
            require dirname(__DIR__, 3) . '/views/user/profile_preview.php';
        } catch (\Throwable $e) {
            error_log('ProfilePreviewController error: ' . $e->getMessage());
            http_response_code(500);
            echo '<h1>Profile Preview Error</h1>';
        }
    }
}
