<?php
// DEBUG: Show all errors for troubleshooting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require dirname(__DIR__, 3) . '/views/partials/header.php';
?>
<section class="py-5">
    <div class="container px-5">
        <div class="bg-light rounded-3 py-5 px-4 px-md-5 mb-5">
            <div class="text-center mb-5">
               
                <h1 class="fw-bolder"><i class="bi bi-person-plus"></i> User Registration</h1>
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
                    <form id="userRegisterForm" method="post" action="">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars(TokenManager::csrf()); ?>">
                        <div class="form-floating mb-3">
                            <input class="form-control" id="first_name" name="first_name" type="text" placeholder="First Name" required />
                            <label for="first_name">First Name</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="second_name" name="second_name" type="text" placeholder="Last Name" required />
                            <label for="second_name">Last Name</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="email" name="email" type="email" placeholder="name@example.com" required />
                            <label for="email">Email address</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="password" name="password" type="password" placeholder="Enter password" required />
                            <label for="password">Password</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="confirm_password" name="confirm_password" type="password" placeholder="Confirm password" required />
                            <label for="confirm_password">Confirm Password</label>
                        </div>
                        <div class="d-grid"><button class="btn btn-primary btn-lg " id="submitButton" type="submit">Register</button></div>
                    </form>
                    <p class="text-center mt-4">
                        Don't have an account? <a href="/user/register">Register here</a> - or you may <a href="/user/login">login</a> here
                    </p>
                    <p>
                        By registering, you agree to our <a href="/terms">Terms of Service</a> and <a href="/privacy">Privacy Policy</a>.
                    </p>
                </div>

            </div>
        </div>
    </div>
</section>
<?php require dirname(__DIR__, 3) . '/views/partials/footer.php'; ?>