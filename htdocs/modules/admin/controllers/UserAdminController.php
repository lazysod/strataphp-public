<?php
// Admin User Management Controller
class UserAdminController
{
    // List/search users
    public function index()
    {
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
    }

    // Add new user
    public function add()
    {
        global $config;
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
    }

    // Edit user
    public function edit($id)
    {
        global $config;
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
            $dead_switch = ($active === 0) ? 1 : 0;
            $updateData = [
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
    }

    // Suspend/unsuspend user
    public function suspend($id)
    {
        global $config;
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
    }

        // Suspend/unsuspend user
    public function unsuspend($id)
    {
        global $config;
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
    }

    // (Optional) Delete user
    public function delete($id)
    {
        // Prevent user from deleting themselves
        $currentUserId = $_SESSION[PREFIX . 'admin']['id'] ?? null;
        if ($currentUserId && $currentUserId == $id) {
            // Optionally set a flash message or error
            $_SESSION['error'] = 'You cannot delete your own account.';
            header('Location: /admin/users');
            exit;
        }
        global $config;
        $db = new DB($config);
        $userModel = new User($db, $config);
        $userModel->deleteUser($id);
        header('Location: /admin/users');
        exit;
    }
}
