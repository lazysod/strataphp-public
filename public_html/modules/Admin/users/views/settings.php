<?php
$localConfig = include dirname(__DIR__, 4) . '/app/config.php';
$sessionPrefix = $localConfig['session_prefix'] ?? 'app_';
$csrfToken = \App\TokenManager::csrf($localConfig);
if (empty($_SESSION[$sessionPrefix . 'admin'])) {
    header('Location: /admin');
    exit;
}
require __DIR__ . '/../../../../views/partials/admin_header.php';
?>
<section class="py-5">
    <div class="container px-5">
        <?php if (!empty($_SESSION['success'])) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($_SESSION['success']); ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error'])) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="/admin/users">User List</a></li>
                        <li class="breadcrumb-item active" aria-current="page">User Settings</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="bg-light rounded-3 py-5 px-4 px-md-5 mb-5">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <h1>User Settings</h1>
                    <p class="text-muted mb-4">Configure account registration behavior for the site.</p>

                    <form method="post" action="/admin/users/settings">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($csrfToken); ?>">

                        <div class="mb-4">
                            <label for="registration_enabled" class="form-label">Registration Enabled</label>
                            <select class="form-select" id="registration_enabled" name="registration_enabled">
                                <option value="1" <?php echo !empty($registrationEnabled) ? 'selected' : ''; ?>>Enabled</option>
                                <option value="0" <?php echo empty($registrationEnabled) ? 'selected' : ''; ?>>Disabled</option>
                            </select>
                            <div class="form-text">Allow or block new user signups.</div>
                        </div>

                        <div class="mb-4">
                            <label for="require_email_verify" class="form-label">Require Email Verification</label>
                            <select class="form-select" id="require_email_verify" name="require_email_verify">
                                <option value="1" <?php echo !empty($requireEmailVerify) ? 'selected' : ''; ?>>Required</option>
                                <option value="0" <?php echo empty($requireEmailVerify) ? 'selected' : ''; ?>>Not Required</option>
                            </select>
                            <div class="form-text">If required, users must verify their email before using their account.</div>
                        </div>

                        <button type="submit" class="btn btn-success">Save Settings</button>
                        <a href="/admin/users" class="btn btn-secondary">Back to User List</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../../../../views/partials/footer.php'; ?>
