<?php
class AdminController
{
    public function profile()
    {
        $showNav = true;
        $admin = $_SESSION[PREFIX . 'user'] ?? null;
        $success = '';
        $error = '';
        if (!$admin || empty($admin['is_admin'])) {
            header('Location: /admin/login');
            exit;
        }
        $config = include dirname(__DIR__) . '/app/config.php';
        $userId = $_SESSION[PREFIX . 'user_id'];
        $db = new DB($config);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF check
            $csrfValid = isset($_POST['csrf_token']) && isset($_SESSION[PREFIX . 'csrf_token']) && hash_equals($_SESSION[PREFIX . 'csrf_token'], $_POST['csrf_token']);
            if (!$csrfValid) {
                $error = 'Invalid CSRF token.';
            } else {
                $first_name = trim($_POST['first_name'] ?? '');
                $second_name = trim($_POST['second_name'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $pwd = $_POST['pwd'] ?? '';
                $pwd2 = $_POST['pwd2'] ?? '';
                if (!$first_name || !$second_name || !$email) {
                    $error = 'All fields except password are required.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid email address.';
                } elseif ($pwd && $pwd !== $pwd2) {
                    $error = 'Passwords do not match.';
                } elseif ($pwd && strlen($pwd) < 8) {
                    $error = 'Password must be at least 8 characters.';
                } else {
                    // Update user info
                    $params = [$first_name, $second_name, $email, $userId];
                    $db->query('UPDATE users SET first_name = ?, second_name = ?, email = ? WHERE id = ?', $params);
                    if ($pwd) {
                        $hash = password_hash($pwd, PASSWORD_DEFAULT);
                        $db->query('UPDATE users SET password = ? WHERE id = ?', [$hash, $userId]);
                    }
                    // Refresh session user info
                    $sql = "SELECT * FROM users WHERE id = ?";
                    $rows = $db->fetchAll($sql, [$userId]);
                    $user = $rows[0] ?? [];
                    $_SESSION[PREFIX . 'user']['first_name'] = $user['first_name'] ?? '';
                    $_SESSION[PREFIX . 'user']['second_name'] = $user['second_name'] ?? '';
                    $_SESSION[PREFIX . 'user']['email'] = $user['email'] ?? '';
                    $success = 'Profile updated successfully.';
                }
            }
        }
        // Fetch current user info for display
        $sql = "SELECT * FROM users WHERE id = ?";
        $rows = $db->fetchAll($sql, [$userId]);
        $user = $rows[0] ?? [];
        include __DIR__ . '/../views/admin/admin_profile.php';
    }
    public function index()
    {
        // Example: load a view
        $admin_page = true; // Set a flag for admin pages
        $title = 'Admin Area Login';
        $pageJs = 'admin'; // Specify JS file for admin
        $showNav = false; // Show navigation for admin
        include __DIR__ . '/../views/admin/admin_login.php';
    }

    public function dashboard()
    {
        $email = 'info@example.com';
        // APP::dump($_SESSION);
    include __DIR__ . '/../views/admin/admin_dashboard.php';
    }

    public function resetRequest()
    {
        $showNav = false;
        $message = '';
        global $config;
        $db = new DB($config);
        $userModel = new User($db, $config);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email'])) {
            $email = trim($_POST['email']);
            $result = $userModel->requestPasswordReset($email, $config['base_url'], true); // true = admin only
            if ($result['status'] === 'success') {
                include_once dirname(__DIR__) . '/vendor/autoload.php';
                $token = $result['token'];
                $resetLink = $config['base_url'] . "/admin/reset-password/confirm?token=$token";
                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = $config['mail']['host'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $config['mail']['username'];
                    $mail->Password = $config['mail']['password'];
                    $mail->SMTPSecure = $config['mail']['encryption'];
                    $mail->Port = $config['mail']['port'];
                    $mail->setFrom($config['mail']['from_email'], $config['site_name']);
                    $mail->addAddress($email);
                    $mail->Subject = 'Admin Password Reset Request';
                    $mail->Body = "Click the following link to reset your admin password: $resetLink\nIf you did not request this, please ignore.";
                    $mail->send();
                    $success = 'If your email is registered as an admin, a reset link has been sent.';
                } catch (\Exception $e) {
                    $error = 'Email failed: ' . $mail->ErrorInfo;
                }
            } else {
                $error = $result['message'];
            }
        }
        include __DIR__ . '/../views/admin/admin_reset_request.php';
    }

    public function resetPassword()
    {
        $showNav = false;
        global $config;
        $db = new DB($config);
        $message = '';
        $token = $_GET['token'] ?? ($_POST['reset_token'] ?? '');
        if (!$token) {
            $message = 'Invalid or missing token.';
            include __DIR__ . '/../views/admin/admin_reset_form.php';
            return;
        }
        // Find admin by token in reset table
        $sql = "SELECT r.user_id, r.expiry_date FROM reset r JOIN users u ON r.user_id = u.id WHERE r.key = ? AND u.is_admin = 1";
        $rows = $db->fetchAll($sql, [$token]);
        if (count($rows) === 0) {
            $message = 'Invalid or expired token.';
            include __DIR__ . '/../views/admin/admin_reset_form.php';
            return;
        }
        $userId = $rows[0]['user_id'];
        $expiry = $rows[0]['expiry_date'];
        if (strtotime($expiry) < time()) {
            $message = 'This reset link has expired.';
            include __DIR__ . '/../views/admin/admin_reset_form.php';
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['password'])) {
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];
            if ($password !== $password_confirm) {
                $message = 'Passwords do not match.';
            } elseif (strlen($password) < 8) {
                $message = 'Password must be at least 8 characters.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $db->query('UPDATE users SET password = ? WHERE id = ?', [$hash, $userId]);
                // Invalidate the token
                $db->query('DELETE FROM reset WHERE `key` = ?', [$token]);
                $message = 'Password reset successful. <a href="/admin">Login</a>';
            }
        }
        include __DIR__ . '/../views/admin/admin_reset_form.php';
    }
}
