<?php

// modules/user/controllers/UserLoginController.php
// Refactored as a class for router compatibility

class UserLoginController
{
    public function index()
    {
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
    }
}
