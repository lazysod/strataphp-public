<?php
namespace App\Modules\User\Controllers;

use App\TokenManager;
use App\DB;
use App\User;

// modules/user/controllers/UserLoginController.php
// Refactored as a class for router compatibility

/**
 * User Login Controller
 * 
 * Handles user authentication and login functionality
 * Provides secure login with CSRF protection and session management
 */
class UserLoginController
{
    /**
     * Handle user login requests
     * 
     * Processes both GET (display form) and POST (authenticate) requests
     * Includes CSRF token validation and proper error handling
     * 
     * @return void
     */
    public function index()
    {
        try {
            include_once dirname(__DIR__, 3) . '/app/start.php';
            $config = include dirname(__DIR__, 3) . '/app/config.php';
            if (empty($config['modules']['user'])) {
                header('Location: /');
                exit;
            }
        $error = '';
        $success = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tm = new TokenManager();
            $result = $tm->verify($_POST['token'] ?? '');
            if ($result['status'] !== 'success') {
                $error = 'Invalid CSRF token. Please refresh and try again.';
            } else {
                $db = new DB($config);
                $user = new User($db, $config);
                $loginInfo = [
                    'email' => trim($_POST['email'] ?? ''),
                    'pwd' => $_POST['password'] ?? '',
                    'remember' => isset($_POST['remember']) ? 1 : 0,
                ];
                $result = $user->login($loginInfo);
                if ($result['status'] === 'success') {
                    // Redirect to configured page after successful login
                    $redirect = $config['login_redirect'] ?? '/';
                    header('Location: ' . $redirect);
                    exit;
                } else {
                    $error = 'Login failed: ' . htmlspecialchars($result['message']);
                }
            }
        }
        include __DIR__ . '/../views/login.php';
        } catch (\Exception $e) {
            error_log('User login error: ' . $e->getMessage());
            $error = 'An unexpected error occurred. Please try again.';
            include __DIR__ . '/../views/login.php';
        }
    }
}
