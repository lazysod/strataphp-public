<?php
// Load config and bootstrap
$startPath = dirname(__DIR__, 2) . '/app/start.php';
if (file_exists($startPath)) {
    $config = include dirname(__DIR__, 2) . '/app/config.php';
    include_once $startPath;
}
if(isset($_SESSION[PREFIX . 'admin']) && $_SESSION[PREFIX . 'admin'] > 0) {
    header('Location: /admin/dashboard');
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Config is autoloaded by start.php
    // CSRF check
    $tm = new TokenManager($config);
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
        // DEBUG: Show which User class is loaded
        // echo '<pre>User class loaded from: ' . (new ReflectionClass('User'))->getFileName() . '</pre>';
        $user = new User($db, $config);
        $loginResult = $user->login(['email' => $email, 'pwd' => $password]);
        if ($loginResult['status'] === 'success' && !empty($_SESSION[PREFIX . 'admin']) && $_SESSION[PREFIX . 'admin'] > 0) {
            // Only allow admin users
            $_SESSION[PREFIX . 'admin'] = $_SESSION[PREFIX . 'user_id'];
            header('Location: /admin');
            exit;
        } else {
            $error = 'Invalid admin credentials.';
            // Log failed admin login attempt
            $logger = new Logger($config);
            $logger->warning(
                'Failed admin login', [
                'email' => $email,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'time' => date('Y-m-d H:i:s')
                ]
            );
        }
    }
}
require __DIR__ . '/../partials/header.php';

?>
<section class="py-5">
    <div class="container px-5">
        <!-- Contact form-->
        <div class="bg-light rounded-3 py-5 px-4 px-md-5 mb-5">
            <div class="text-center mb-5">
                <i class="bi bi-person-fill-lock"></i>
                <h1 class="fw-bolder">Admin Login</h1>
            </div>
            <div class="row gx-5 justify-content-center">
                <div class="col-lg-8 col-xl-6">
                    <?php if ($error) : ?>
                        <div class="alert alert-danger text-center"> <?php echo htmlspecialchars($error) ?> </div>
                    <?php endif; ?>
                    <form id="contactForm" method="post" action="">
                        <!-- CSRF Token -->
                        <?php if (class_exists('TokenManager')) : ?>
                            <?php
                            $configPath = dirname(__DIR__, 2) . '/app/config.php';
                            if (!file_exists($configPath)) {
                                throw new \Exception('Config file not found: ' . $configPath);
                            }
                            ?>
                        <input type="hidden" name="token" value="<?php echo TokenManager::csrf(include $configPath) ?>">
                        <?php endif; ?>
                        <!-- Name input-->
                        <!-- Email address input-->
                        <div class="form-floating mb-3">
                            <input class="form-control" id="email" name="email" type="email" placeholder="name@example.com" data-sb-validations="required,email" />
                            <label for="email">Email address</label>
                            <div class="invalid-feedback" data-sb-feedback="email:required">An email is required.</div>
                            <div class="invalid-feedback" data-sb-feedback="email:email">Email is not valid.</div>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="password" name="password" type="password" placeholder="Enter password" data-sb-validations="required" />
                            <label for="password">Password</label>
                            <div class="invalid-feedback" data-sb-feedback="password:required">A password is required.</div>
                        </div>
                        <!-- Submit success message-->
                        <!---->
                        <!-- This is what your users will see when the form-->
                        <!-- has successfully submitted-->
                        <div class="d-none" id="submitSuccessMessage">
                            <div class="text-center mb-3">
                                <div class="fw-bolder">Form submission successful!</div>
                                To activate this form, sign up at
                                <br />
                                <a href="https://startbootstrap.com/solution/contact-forms">https://startbootstrap.com/solution/contact-forms</a>
                            </div>
                        </div>
                        <!-- Submit error message-->
                        <!---->
                        <!-- This is what your users will see when there is-->
                        <!-- an error submitting the form-->
                        <div class="d-none" id="submitErrorMessage">
                            <div class="text-center text-danger mb-3">Error sending message!</div>
                        </div>
                        <!-- Submit Button-->
                        <div class="d-grid"><button class="btn btn-primary btn-lg " id="submitButton" type="submit">Submit</button></div>
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars(TokenManager::csrf()); ?>">
                    </form>
                    <div class="mt-3 text-center">
                        <a href="/admin/reset-password">Forgot your password?</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact cards-->

    </div>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>