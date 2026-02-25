<?php
$bootstrapPath = dirname(__DIR__, 2) . '/app/bootstrap.php';
if (file_exists($bootstrapPath)) {
    include_once $bootstrapPath;
}
require __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../app/config.php';

$sessionPrefix = $config['session_prefix'] ?? ($config['prefix'] ?? 'framework');
if (isset($_SESSION[$sessionPrefix . 'admin']) && $_SESSION[$sessionPrefix . 'admin'] > 0) {
    header('Location: /admin/dashboard');
    exit;
}
?>

<section class="py-5">
    <div class="container px-5">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <h2>Admin Password Reset</h2>
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
            <div class="col-md-6 bg-light p-4 mx-auto">

                <form method="post" action="/admin/reset-request">
                    <label for="email">Admin Email:</label>
                    <input type="email" name="email" id="email" required class="form-control"><br>
                    <button type="submit" class="btn btn-primary">Send Reset Link</button>
                </form>
                <div class="pt-3 text-center">
                    <a href="/admin/">Back to login</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require __DIR__ . '/../partials/footer.php';
?>