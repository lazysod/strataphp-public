<?php
require_once __DIR__ . '/../bootstrap.php';
use App\DB;
use App\User;
use App\Logger;
use App\TokenManager;

$tm = new TokenManager($config);
$sessionPrefix = $config['session_prefix'] ?? ($config['prefix'] ?? 'framework');

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
        try {
            $db = new DB($config);
            $user = new User($db, $config);
            $loginResult = $user->login(['email' => $email, 'pwd' => $password]);
            
            // Check login success AND admin rank AFTER login() runs
            if ($loginResult['status'] === 'success') {
                // User->login() already set app_admin, app_user_id, etc
                // Check if they're actually admin
                if (!empty($_SESSION[$sessionPrefix . 'admin']) && $_SESSION[$sessionPrefix . 'admin'] > 0) {
                    $userId = $_SESSION[$sessionPrefix . 'user_id'];
                    
                    // Create DB session row + set app_session_token cookie
                    require_once __DIR__ . '/../app/SessionManager.php';
                    $sessionManager = new App\SessionManager($db, $config);
                    $sessionManager->createSession($userId, false);
                    
                    // No need to repopulate - User->login() already did it
                    header('Location: /admin/dashboard');
                    exit;
                } else {
                    $error = 'Your account does not have admin access.';
                    // Unset only user-related session variables, preserve CSRF token
                    $userSessionKeys = [
                        $sessionPrefix . 'admin',
                        $sessionPrefix . 'email',
                        $sessionPrefix . 'user_id',
                        $sessionPrefix . 'sec_hash',
                        $sessionPrefix . 'first_name',
                        $sessionPrefix . 'second_name',
                        $sessionPrefix . 'last_log',
                        $sessionPrefix . 'avatar',
                        $sessionPrefix . 'user',
                        $sessionPrefix . 'rank_title',
                        $sessionPrefix . 'rank_level'
                    ];
                    foreach ($userSessionKeys as $key) {
                        if (isset($_SESSION[$key])) {
                            unset($_SESSION[$key]);
                        }
                    }
                }
            } else {
                $error = 'Invalid credentials.';
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
        } catch (Exception $e) {
            $error = 'Database connection error. Please try again.';
        }
    }
}
require __DIR__ . '/../views/partials/admin_header.php';
?>
<section class="py-5">
    <div class="container px-5">
        <div class="row">
            <div class="col-md-6 mx-auto">
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
        </div>
    </div>
</section>
<?php require __DIR__ . '/../views/partials/footer.php'; ?>