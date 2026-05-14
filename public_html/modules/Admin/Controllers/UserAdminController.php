<?php
namespace App\Modules\Admin\Controllers;
require_once dirname(__DIR__, 4) . '/public_html/bootstrap.php';
use App\DB;
use App\User;
use App\App;
use App\TokenManager;

/**
 * Admin User Management Controller
 *
 * Handles CRUD operations for user management in the admin interface
 */
class UserAdminController
{
    private const USER_SETTINGS_REDIRECT = '/admin/users/settings';

    /**
     * List/search users with pagination
     *
     * @return void
     */
    public function index()
    {
        try {
            // Pagination logic
            global $config;
            // Use autoloader for User.php, and instantiate DB directly
            $db = new DB($config);
            $userModel = new User($db, $config);
            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $perPage = 20;
            $offset = ($page - 1) * $perPage;
            $totalUsers = $userModel->countAll();
            $users = $userModel->getPaginated($perPage, $offset);
            $totalPages = ceil($totalUsers / $perPage);
            include __DIR__ . '/../users/views/list.php';
        } catch (\Exception $e) {
            http_response_code(500);
            echo '<h1>Error loading users</h1>';
        }
    }

    /**
     * Add new user form and processing
     *
     * @return void
     */
    public function add()
    {
        try {
            global $config;
            // ...existing code...
            $db = new DB($config);
            $userModel = new User($db, $config);
            $error = '';
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // display_name removed
                $first_name = trim($_POST['first_name'] ?? '');
                $second_name = trim($_POST['second_name'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $role = $_POST['role'] ?? 'user';
                $password = $_POST['password'] ?? '';
                if ($first_name === '' || $second_name === '') {
                    $error = 'First name and second name are required.';
                } else {
                    $is_admin = ($role === 'admin') ? 1 : 0;
                    $userModel->createUser(
                        [
                        'first_name' => $first_name,
                        'second_name' => $second_name,
                        'email' => $email,
                        'is_admin' => $is_admin,
                        'pwd' => $password
                        ]
                    );
                    header('Location: /admin/users');
                    exit;
                }
            }
            include __DIR__ . '/../users/views/add.php';
        } catch (\Exception $e) {
            $error = 'Error creating user. Please try again.';
            include __DIR__ . '/../users/views/add.php';
        }
    }

    /**
     * Edit user form and processing
     *
     * @param string $id User ID
     * @return void
     */
    public function edit($id)
    {
        try {
            global $config;
            // ...existing code...
            $db = new DB($config);
            $userModel = new User($db, $config);
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = trim($_POST['email'] ?? '');
                $role = $_POST['role'] ?? 'user';
                $status = $_POST['status'] ?? 'active';
                // Update user in DB
                $user = $userModel->getById($id);
                if (!$user) {
                    http_response_code(404);
                    echo '<h1>User not found</h1>';
                    exit;
                }
                $is_admin = ($role === 'admin') ? 1 : 0;
                $active = ($status === 'active') ? 1 : 0;
                $dead_switch = (int)($_POST['dead_switch'] ?? (($active === 0) ? 1 : 0));
                $dead_switch = ($dead_switch === 1) ? 1 : 0;
                $updateData = [
                    'first_name' => trim($_POST['first_name'] ?? ''),
                    'second_name' => trim($_POST['second_name'] ?? ''),
                    'email' => $email,
                    'is_admin' => $is_admin,
                    'active' => $active,
                    'display_name' => trim($_POST['display_name'] ?? ''),
                    'dead_switch' => $dead_switch
                ];

                $newPassword = trim($_POST['password'] ?? '');
                if ($newPassword !== '') {
                    // Hash password before saving
                    $updateData['pwd'] = password_hash($newPassword, PASSWORD_DEFAULT);
                }
                $userModel->updateUser($id, $updateData);
                header('Location: /admin/users');
                exit;
            } else {
                $user = $userModel->getById($id);
                if (!$user) {
                    http_response_code(404);
                    echo '<h1>User not found</h1>';
                    exit;
                }
                include __DIR__ . '/../users/views/edit.php';
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo '<h1>Error editing user</h1>';
        }
    }

    /**
     * Suspend a user account
     *
     * @param string $id User ID
     * @return void
     */
    public function suspend($id)
    {
        try {
            global $config;
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $_SESSION['error'] = 'Invalid request method.';
                header('Location: /admin/users');
                exit;
            }

            $tm = new TokenManager($config);
            $verify = $tm->verify($_POST['token'] ?? '');
            if (($verify['status'] ?? 'fail') !== 'success') {
                $_SESSION['error'] = 'Invalid CSRF token.';
                header('Location: /admin/users');
                exit;
            }

            $currentUserId = $this->resolveCurrentUserId($config);
            if ($currentUserId !== null && $currentUserId === (int)$id) {
                $_SESSION['error'] = 'You cannot suspend your own account.';
                header('Location: /admin/users');
                exit;
            }

            $db = new DB($config);
            $userModel = new User($db, $config);
            $user = $userModel->getById($id);
            if (!$user) {
                http_response_code(404);
                echo '<h1>User not found</h1>';
                exit;
            }
            $userModel->suspend($id); // Call the suspend method on the model
            header('Location: /admin/users');
            exit;
        } catch (\Exception $e) {
            http_response_code(500);
            echo '<h1>Error suspending user</h1>';
        }
    }

    /**
     * Unsuspend a user account
     *
     * @param string $id User ID
     * @return void
     */
    public function unsuspend($id)
    {
        try {
            global $config;
            // ...existing code...
            $db = new DB($config);
            $userModel = new User($db, $config);
            $user = $userModel->getById($id);
            if (!$user) {
                http_response_code(404);
                echo '<h1>User not found</h1>';
                exit;
            }
            $userModel->unsuspend($id); // Call the unsuspend method on the model
            header('Location: /admin/users');
            exit;
        } catch (\Exception $e) {
            http_response_code(500);
            echo '<h1>Error unsuspending user</h1>';
        }
    }

    /**
     * Activate a user account
     *
     * @param string $id User ID
     * @return void
     */
    public function activate($id)
    {
        try {
            global $config;
            $db = new DB($config);
            $userModel = new User($db, $config);
            $user = $userModel->getById($id);
            if (!$user) {
                http_response_code(404);
                echo '<h1>User not found</h1>';
                exit;
            }
            $userModel->activate($id);
            header('Location: /admin/users');
            exit;
        } catch (\Exception $e) {
            http_response_code(500);
            echo '<h1>Error activating user</h1>';
        }
    }

    /**
     * Delete a user account
     *
     * @param string $id User ID
     * @return void
     */
    public function delete($id)
    {
        try {
            global $config;
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $_SESSION['error'] = 'Invalid request method.';
                header('Location: /admin/users');
                exit;
            }

            $tm = new TokenManager($config);
            $verify = $tm->verify($_POST['token'] ?? '');
            if (($verify['status'] ?? 'fail') !== 'success') {
                $_SESSION['error'] = 'Invalid CSRF token.';
                header('Location: /admin/users');
                exit;
            }

            $currentUserId = $this->resolveCurrentUserId($config);
            if ($currentUserId !== null && $currentUserId === (int)$id) {
                $_SESSION['error'] = 'You cannot delete your own account.';
                header('Location: /admin/users');
                exit;
            }
            // ...existing code...
            $db = new DB($config);
            $userModel = new User($db, $config);
            $userModel->deleteUser($id);
            header('Location: /admin/users');
            exit;
        } catch (\Exception $e) {
            http_response_code(500);
            echo '<h1>Error deleting user</h1>';
        }
    }

    /**
     * Manage user registration settings.
     *
     * @return void
     */
    public function settings()
    {
        try {
            global $config;

            $usersConfig = $config['users'] ?? [];
            $registrationEnabled = !empty($usersConfig['registration_enabled']);
            $requireEmailVerify = !empty($usersConfig['require_email_verify']);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $tm = new TokenManager($config);
                $verify = $tm->verify($_POST['token'] ?? '');
                if (($verify['status'] ?? 'fail') !== 'success') {
                    $_SESSION['error'] = 'Invalid CSRF token.';
                    header('Location: ' . self::USER_SETTINGS_REDIRECT);
                    exit;
                }

                $registrationEnabled = isset($_POST['registration_enabled']) && $_POST['registration_enabled'] === '1';
                $requireEmailVerify = isset($_POST['require_email_verify']) && $_POST['require_email_verify'] === '1';

                $result = $this->updateUsersConfigSettings($registrationEnabled, $requireEmailVerify);
                if (!$result['success']) {
                    $_SESSION['error'] = $result['message'];
                } else {
                    $_SESSION['success'] = 'User settings updated.';
                }

                header('Location: ' . self::USER_SETTINGS_REDIRECT);
                exit;
            }

            include __DIR__ . '/../users/views/settings.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error loading user settings.';
            header('Location: /admin/users');
            exit;
        }
    }

    /**
     * Persist users settings in app/config.php while preserving file structure.
     *
     * @param bool $registrationEnabled
     * @param bool $requireEmailVerify
     * @return array{success:bool,message:string}
     */
    private function updateUsersConfigSettings($registrationEnabled, $requireEmailVerify)
    {
        $configPath = dirname(__DIR__, 3) . '/app/config.php';
        if (!file_exists($configPath) || !is_readable($configPath) || !is_writable($configPath)) {
            return ['success' => false, 'message' => 'Config file is not writable.'];
        }

        $content = file_get_contents($configPath);
        if ($content === false) {
            return ['success' => false, 'message' => 'Unable to read config file.'];
        }

        $regValue = $registrationEnabled ? 'true' : 'false';
        $verifyValue = $requireEmailVerify ? 'true' : 'false';
        $usersBlockUpdated = false;

        $updatedContent = preg_replace_callback(
            "/('users'\\s*=>\\s*array\\s*\\()(.*?)(\\n\\s*\\),)/s",
            function ($matches) use ($regValue, $verifyValue, &$usersBlockUpdated) {
                $usersBlockUpdated = true;
                $prefix = $matches[1];
                $body = $matches[2];
                $suffix = $matches[3];

                $body = preg_replace(
                    "/(^\\s*'registration_enabled'\\s*=>\\s*)(true|false)(\\s*,\\s*(?:\\/\\/.*)?$)/mi",
                    "$1{$regValue}$3",
                    $body,
                    1,
                    $registrationReplaced
                );

                if (($registrationReplaced ?? 0) === 0) {
                    $body .= "\n    'registration_enabled' => {$regValue},";
                }

                $body = preg_replace(
                    "/(^\\s*'require_email_verify'\\s*=>\\s*)(true|false)(\\s*,\\s*(?:\\/\\/.*)?$)/mi",
                    "$1{$verifyValue}$3",
                    $body,
                    1,
                    $verifyReplaced
                );

                if (($verifyReplaced ?? 0) === 0) {
                    $body .= "\n    'require_email_verify' => {$verifyValue},";
                }

                return $prefix . $body . $suffix;
            },
            $content,
            1
        );

        if (!$usersBlockUpdated || $updatedContent === null) {
            return ['success' => false, 'message' => 'Unable to locate users config block.'];
        }

        $writeResult = file_put_contents($configPath, $updatedContent, LOCK_EX);
        if ($writeResult === false) {
            return ['success' => false, 'message' => 'Unable to write config file.'];
        }

        return ['success' => true, 'message' => 'Settings updated'];
    }

    /**
     * Resolve currently authenticated admin user id from session.
     *
     * @param array $config
     * @return int|null
     */
    private function resolveCurrentUserId(array $config)
    {
        $sessionPrefix = $config['session_prefix'] ?? 'app_';
        if (!empty($_SESSION[$sessionPrefix . 'user_id'])) {
            return (int)$_SESSION[$sessionPrefix . 'user_id'];
        }

        if (!empty($_SESSION[$sessionPrefix . 'user']['id'])) {
            return (int)$_SESSION[$sessionPrefix . 'user']['id'];
        }

        return null;
    }
}
