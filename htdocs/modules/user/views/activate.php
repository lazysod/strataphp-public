<?php
require_once dirname(__DIR__, 4) . '/bootstrap.php';
require dirname(__DIR__, 3) . '/views/partials/header.php';
?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2>Account Activation</h2>
            <?php if (!empty($success)) : ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
        </div>
    </div>

</div>
<?php
require dirname(__DIR__, 3) . '/views/partials/footer.php';
