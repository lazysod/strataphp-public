<?php
namespace App\Modules\User\Controllers;
use App\TokenManager;
use App\DB;
use App\User;
use App\Token;

// Refactored as a class for router compatibility
/**
 * User Registration Controller
 * 
 * Handles new user registration with validation and security
 * Includes email verification, password validation, and CSRF protection
 */
class UserRegisterController
{
    /**
     * Handle user registration requests
     * 
     * Processes both GET (display form) and POST (register user) requests
     * Validates input data and creates new user accounts
     * 
     * @return void
     */
    public function index()
    {
        try {
            include_once dirname(__DIR__, 3) . '/app/start.php';
            $config = include dirname(__DIR__, 3) . '/app/config.php';
            
            // Check if user is already logged in
            $prefix = $config['session_prefix'] ?? 'app_';
            if (isset($_SESSION[$prefix . 'user_id'])) {
                // Use CmsHelper for smart redirect based on CMS availability
                $isAdmin = isset($_SESSION[$prefix . 'admin']) && $_SESSION[$prefix . 'admin'] > 0;
                $redirect = CmsHelper::getLoggedInRedirect($isAdmin);
                header('Location: ' . $redirect);
                exit;
            }
            if (isset($config['registration_enabled']) && !$config['registration_enabled']) {
                $error = 'User registration is currently disabled.';
                $success = '';
                // Use CMS-themed registration page
                $cmsRegisterView = dirname(__DIR__, 2) . '/cms/views/user/register.php';
                if (file_exists($cmsRegisterView)) {
                    include $cmsRegisterView;
                } else {
                    include __DIR__ . '/../views/register.php';
                }
                return;
            }
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
                $userInfo = [
                    'email' => trim($_POST['email'] ?? ''),
                    'pwd' => $_POST['password'] ?? '',
                    'confirm_pwd' => $_POST['confirm_password'] ?? '',
                    'first_name' => trim($_POST['first_name'] ?? ''),
                    'second_name' => trim($_POST['second_name'] ?? ''),
                ];
                if (!empty($_POST['display_name'])) {
                    $userInfo['display_name'] = trim($_POST['display_name']);
                }
                $result = $user->register($userInfo);
                if ($result['status'] === 'success') {
                    $success = $result['message'];
                } else {
                    $error = $result['message'];
                }
            }
        }
        // Use CMS-themed registration page
        $cmsRegisterView = dirname(__DIR__, 2) . '/cms/views/user/register.php';
        if (file_exists($cmsRegisterView)) {
            include $cmsRegisterView;
        } else {
            include __DIR__ . '/../views/register.php';
        }
        } catch (\Exception $e) {
            error_log('User registration error: ' . $e->getMessage());
            $error = 'An unexpected error occurred during registration. Please try again.';
            $success = '';
            $token = '';
            // Use CMS-themed registration page
            $cmsRegisterView = dirname(__DIR__, 2) . '/cms/views/user/register.php';
            if (file_exists($cmsRegisterView)) {
                include $cmsRegisterView;
            } else {
                include __DIR__ . '/../views/register.php';
            }
        }
    }
}
