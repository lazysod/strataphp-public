<?php
// User profile page for updating details
require dirname(__DIR__, 3) . '/views/partials/header.php';
?>
<section class="py-5">
    <div class="container px-5">
        <div class="bg-light rounded-3 py-5 px-4 px-md-5 mb-5">
            <div class="text-center mb-5">
                <h1 class="fw-bolder">Your Profile</h1>
            </div>
            <div class="row gx-5 justify-content-center">
                <div class="col-lg-8 col-xl-6">
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
                    <form method="post" action="">
                        <div class="form-floating mb-3">
                            <input class="form-control" id="first_name" name="first_name" type="text" value="<?php echo htmlspecialchars($user['first_name'] ?? '') ?>" required />
                            <label for="first_name">First Name</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="second_name" name="second_name" type="text" value="<?php echo htmlspecialchars($user['second_name'] ?? '') ?>" required />
                            <label for="second_name">Second Name</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="email" name="email" type="email" value="<?php echo htmlspecialchars($user['email'] ?? '') ?>" required />
                            <label for="email">Email address</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="pwd" name="pwd" type="password" placeholder="New password (leave blank to keep current)" />
                            <label for="pwd">New Password</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="pwd2" name="pwd2" type="password" placeholder="Confirm new password" />
                            <label for="pwd2">Confirm New Password</label>
                        </div>
                        <div class="d-grid"><button class="btn btn-primary btn-lg" type="submit">Update Profile</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require dirname(__DIR__, 3) . '/views/partials/footer.php'; ?>
