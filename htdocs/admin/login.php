<?php
require_once __DIR__ . '/../app/config.php';
session_start();
$sessionPrefix = $config['session_prefix'] ?? ($config['prefix'] ?? 'framework');
if (isset($_SESSION[$sessionPrefix . 'admin']) && $_SESSION[$sessionPrefix . 'admin'] > 0) {
    header('Location: /admin/dashboard');
    exit;
}
// Show admin login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    // Replace with your DB logic
    require_once __DIR__ . '/../app/start.php';
    $db = class_exists('DB') ? new DB($config) : null;
    $admin = $db ? $db->fetch("SELECT * FROM users WHERE username = ? AND is_admin = 1", [$username]) : null;
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION[$sessionPrefix . 'admin'] = $admin['id'];
        header('Location: /admin/dashboard');
        exit;
    } else {
        $error = 'Invalid admin credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="/themes/default/assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="<?php echo App::config('theme_path'); ?>/css/styles.css">
</head>
<body>
    <main class="flex-shrink-0">
        <section class="py-5">
            <div class="container px-5">
                <div class="bg-light rounded-3 py-5 px-4 px-md-5 mb-5">
                    <div class="text-center mb-5">
                        <i class="bi bi-person-fill-lock"></i>
                        <h1 class="fw-bolder">Admin Login</h1>
                    </div>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                    <div class="mt-3 text-center">
                        <a href="/admin/reset-request">Forgot your password?</a>
                        <br>
                        <a href="/admin/reset-password">Already have a reset link?</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
