<?php

// modules/user/controllers/UserRegisterController.php
// Refactored as a class for router compatibility
class UserRegisterController
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
                $userInfo = [
                    'email' => trim($_POST['email'] ?? ''),
                    'pwd' => $_POST['password'] ?? '',
                    'confirm_pwd' => $_POST['confirm_password'] ?? '',
                    'first_name' => trim($_POST['first_name'] ?? ''),
                    'second_name' => trim($_POST['second_name'] ?? ''),
                ];
                $result = $user->register($userInfo);
                if ($result['status'] === 'success') {
                    $success = $result['message'];
                } else {
                    $error = $result['message'];
                }
            }
        }
        include __DIR__ . '/../views/register.php';
    }
}
