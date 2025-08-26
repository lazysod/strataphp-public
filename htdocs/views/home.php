<?php 
use App\App;
$title = 'New Framework';
$pageJs = 'home';
require __DIR__ . '/partials/header.php'; 

?>
            <header class="bg-dark py-5">
                <div class="container px-5">
                    <div class="row gx-5 align-items-center justify-content-center">
                        <div class="col-lg-8 col-xl-7 col-xxl-6">
                            <div class="my-5 text-center text-xl-start">
                                <h1 class="display-5 fw-bolder text-white mb-2">Welcome to <?php echo App::config('site_name'); ?></h1>
                                <p class="lead fw-normal text-white-50 mb-4">A modern PHP framework for building web applications.</p>
                                <div class="d-grid gap-3 d-sm-flex justify-content-sm-center justify-content-xl-start">
                                    <a class="btn btn-primary btn-lg px-4 me-sm-3" href="#features">Get Started</a>
                                    <a class="btn btn-outline-light btn-lg px-4" href="https://strataphp.org/documentation/">Learn More</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-5 col-xxl-6 d-none d-xl-block">
                            <div class="card">
                            <div class="card-body">
                                <ul class="list-unstyled lead">
                                    <li><b>Site Name:</b> <?php echo App::config('site_name'); ?></li>
                                    <li><b>Version:</b> <?php echo App::config('version'); ?></li>
                                    <li><b>Admin Email:</b> <?php echo App::config('admin_email'); ?></li>
                                    <li><b>PHP Version:</b> <?php echo phpversion();  ?></li>
                                    <li><b>Database Host:</b> <?php echo App::config('db')['host']; ?></li>
                                    <li><b>Database Name:</b> <?php echo App::config('db')['database']; ?></li>
                                </ul>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- Features section-->
            <section class="py-5" id="features">
                <div class="container px-5 my-5">
                    <div class="row mb-5">
                        <div class="col-lg-12 mb-5 mb-lg-0"><h2 class="text-center fw-bolder mb-0">A better way to start building.</h2></div>
                    </div>
                    <div class="row ">
                        <div class="col-lg-12">
                            <div class="row gx-5 row-cols-1 row-cols-md-2">
                                <div class="col mb-5 h-100">
                                    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-2"><i class="bi bi-collection"></i></div>
                                    <h2 class="h5">Modular architecture</h2>
                                    <p class="mb-0">Easily add or remove modules (user system, forum, etc.)</p>
                                </div>
                                <div class="col mb-5 h-100">
                                    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-2"><i class="bi bi-building"></i></div>
                                    <h2 class="h5">Unified DB class</h2>
                                    <p class="mb-0">Easily interact with the database using a unified API.</p>
                                </div>
                                <div class="col mb-5 mb-md-0 h-100">
                                    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-2"><i class="bi bi-toggles2"></i></div>
                                    <h2 class="h5">Admin & user systems are independent</h2>
                                    <p class="mb-0">Easily manage admin and user systems separately.</p>
                                </div>
                                <div class="col h-100 mt-3">
                                    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-2"><i class="bi bi-toggles2"></i></div>
                                    <h2 class="h5">User authentication</h2>
                                    <p class="mb-0">Registration, login, profile, password reset (with token expiry), all via modular, class-based controllers</p>
                                </div>

                                <div class="col h-100 mt-3">
                                    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-2"><i class="bi bi-toggles2"></i></div>
                                    <h2 class="h5">Admin login & profile</h2>
                                    <p class="mb-0">Separate admin authentication, dashboard, and profile page, with secure session and logging</p>
                                </div>
                                <div class="col h-100 mt-3">
                                    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-2"><i class="bi bi-toggles2"></i></div>
                                    <h2 class="h5">Email integration</h2>
                                    <p class="mb-0">Uses PHPMailer, configure mail settings in `app/config.php`</p>
                                </div>
                                <div class="col h-100 mt-3">
                                    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-2"><i class="bi bi-toggles2"></i></div>
                                    <h2 class="h5">CSRF protection</h2>
                                    <p class="mb-0">Automatically adds CSRF tokens to forms and validates on submission</p>
                                </div>
                                <div class="col h-100 mt-3">
                                    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-2"><i class="bi bi-toggles2"></i></div>
                                    <h2 class="h5">Session management</h2>
                                    <p class="mb-0">Robust, secure, auto-started in `app/start.php`</p>
                                </div>
                                <div class="col h-100 mt-3">
                                    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-2"><i class="bi bi-toggles2"></i></div>
                                    <h2 class="h5">Logging</h2>
                                    <p class="mb-0">Logs all user activity, with optional file-based or database storage</p>
                                </div>
                                <div class="col h-100 mt-3">
                                    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-2"><i class="bi bi-toggles2"></i></div>
                                    <h2 class="h5">Dynamic navigation</h2>
                                    <p class="mb-0">Automatically generates navigation links based on user permissions and roles.</p>
                                </div>
                                <div class="col h-100 mt-3">
                                    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-2"><i class="bi bi-toggles2"></i></div>
                                    <h2 class="h5">Extensible</h2>
                                    <p class="mb-0">Extensible framework with custom modules and features.</p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </section>

<?php require __DIR__ . '/partials/footer.php'; ?>
