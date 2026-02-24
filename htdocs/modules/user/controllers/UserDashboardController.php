<?php
namespace App\Modules\User\Controllers;

use App\DB;
use App\App;
use App\User;

/**
 * User Dashboard Controller
 *
 * Manages user dashboard viewing and editing functionality.
 * Handles dashboard updates, password changes, and user data management.
 */
class UserDashboardController
{
    /**
     * Handle user profile requests
     *
     * Displays user profile and processes profile update requests.
     * Includes validation, security checks, and error handling.
     *
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
            // TODO: Load dashboard menu config and pass to view
            $userClass = new User(new DB($config), $config);
            $profile_list = $userClass->get_profiles($_SESSION[$sessionPrefix . 'user_id']);
            include dirname(__DIR__, 3) . '/views/user/dashboard_menu.php';
        } catch (\Throwable $e) {
            // Log error or show error page
            error_log('UserDashboardController error: ' . $e->getMessage());
            http_response_code(500);
            echo '<h1>Dashboard Error</h1>';
        }
    }
}