<?php
require __DIR__ . '/partials/header.php';

use App\App;
use App\Version;
// ...header and Version.php are now handled by controller or view system
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
                            <li><b>Version:</b> <?php echo Version::get(); ?></li>
                            <li><b>Admin Email:</b> <a href="mailto:<?php echo App::config('admin_email'); ?>"><?php echo App::config('admin_email'); ?></a></li>
                            <li><b>PHP Version:</b> <?php echo phpversion();  ?></li>
                            <li><b>Database Host:</b> <?php echo App::config('db')['host']; ?></li>
                            <li><b>Database Name:</b> <?php echo App::config('db')['database']; ?></li>
                            <li><b>Debug Mode:</b> <?php echo App::config('debug') ? 'Enabled' : 'Disabled'; ?></li>
                            <li><b>Admin Login:</b> <a href="<?php echo App::config('base_url') . '/admin'; ?>"><?php echo App::config('base_url') . '/admin'; ?></a></li>
                            <li><b>Connection:</b> <?php
                                                    // detect if connection is active by trying to connect to the database
                                                    global $config;
                                                    try {
                                                        $db = new \App\DB($config);
                                                        echo '<span class="text-success">Active</span>';
                                                    } catch (Exception $e) {
                                                        echo '<span class="text-danger">Inactive</span>';
                                                    }
                                                    ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

</div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>