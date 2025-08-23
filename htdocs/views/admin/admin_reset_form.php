<?php
$sessionPrefix = $config['session_prefix'] ?? ($config['prefix'] ?? 'framework');
if (isset($_SESSION[$sessionPrefix . 'admin']) && $_SESSION[$sessionPrefix . 'admin'] > 0) {
    header('Location: /admin/dashboard');
    exit;
}
$startPath = dirname(__DIR__, 2) . '/app/start.php';
if (file_exists($startPath)) {
    include_once $startPath;
}
// Only allow access if a valid token is present
$token = $_GET['token'] ?? ($_POST['reset_token'] ?? '');
if (!$token) {
    header('Location: /admin/reset-request');
    exit;
}
require __DIR__ . '/../partials/header.php';
?>
<section class="py-5">
    <div class="container px-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <?php if (!empty($success)) : ?>
                    <div class="alert alert-success text-center alert-dismissible fade show" role="alert">
                        <?php echo $success ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if (!empty($error)) : ?>
                    <div class="alert alert-danger text-center alert-dismissible fade show" role="alert">
                        <?php echo $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h2>Set New Admin Password</h2>
                <form method="post" action="/admin/reset-password">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token ?? ''); ?>">
                    <label for="password">New Password:</label>
                    <input type="password" name="password" id="password" required class="form-control"><br>
                    <label for="password_confirm">Confirm Password:</label>
                    <input type="password" name="password_confirm" id="password_confirm" required class="form-control"><br>
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </form>
                <div class="pt-3 text-center">
                    <a href="/admin/login">Back to login</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require __DIR__ . '/../partials/footer.php';
?>