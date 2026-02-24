<?php
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
use App\DB;
use App\User;
use App\Logger;
require_once __DIR__ . '/../app/App.php';
// TokenManager is now autoloaded via Composer
use App\TokenManager;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load config and bootstrap
$config = include __DIR__ . '/../app/config.php';
$startPath = __DIR__ . '/../app/start.php';
if (file_exists($startPath)) {
    include_once $startPath;
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$sessionPrefix = $config['session_prefix'] ?? ($config['prefix'] ?? 'framework');
if (!defined('PREFIX')) {
    define('PREFIX', $sessionPrefix);
}
$tm = new TokenManager($config);
if (isset($_SESSION[$sessionPrefix . 'admin']) && $_SESSION[$sessionPrefix . 'admin'] > 0) {
    header('Location: /admin/dashboard');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $verify = $tm->verify($_POST['token'] ?? '');
    if (!isset($_POST['token']) || $verify['status'] !== 'success') {
        $error = 'Invalid CSRF token.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $db = null;
        if (class_exists('DB')) {
            $db = new DB($config);
        }
        $user = new User($db, $config);
        $loginResult = $user->login(['email' => $email, 'pwd' => $password]);
        // Only allow admin login if user is admin
        if ($loginResult['status'] === 'success' && !empty($_SESSION[$sessionPrefix . 'admin']) && $_SESSION[$sessionPrefix . 'admin'] > 0) {
            // Set admin session variable to user_id for consistency
            $_SESSION[$sessionPrefix . 'admin'] = $_SESSION[$sessionPrefix . 'user_id'];
            // Create session in user_sessions for admin
            require_once __DIR__ . '/../app/SessionManager.php';
            $db = new DB($config);
            $sessionManager = new App\SessionManager($db, $config);
            $sessionManager->createSession($_SESSION[$sessionPrefix . 'user_id'], false);
            header('Location: /admin/dashboard');
            exit;
        } else {
            $error = 'Invalid admin credentials.';
            session_destroy();
            $logger = new Logger($config);
            $logger->warning(
                'Failed admin login',
                [
                    'email' => $email,
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                    'time' => date('Y-m-d H:i:s')
                ]
            );
        }
    }
}
require __DIR__ . '/../views/partials/admin_header.php';
?>
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
                <input type="hidden" name="token" value="<?= htmlspecialchars($tm->generate()) ?>">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <div class="mt-3 text-center">
                <a href="/admin/reset-request">Forgot your password?</a>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../views/partials/footer.php'; ?>