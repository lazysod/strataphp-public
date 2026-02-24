<?php
// modules/user/views/activate.php
require dirname(__DIR__, 3) . '/views/partials/header.php';
?>
<div class="container mt-5">
    <h2>Account Activation</h2>
    <?php if (!empty($success)) : ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error) ?></div>
    <?php endif; ?>
</div>
<?php
require dirname(__DIR__, 3) . '/views/partials/footer.php';
