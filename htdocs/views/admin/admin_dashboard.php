<?php
if (empty($_SESSION[PREFIX . 'admin'])) {
    header('Location: /admin');
    exit;
}
require __DIR__ . '/../partials/admin_header.php'; ?>
<section class="py-5">
    <div class="container px-5">
        <!-- Contact form-->
        <div class="bg-light rounded-3 py-5 px-4 px-md-5 mb-5">
            <div class="text-center mb-5">
                <i class="bi bi-person-fill-lock"></i>
                <h1 class="fw-bolder">Welcome</h1>
            </div>
            <div class="row gx-5 justify-content-center">
                <div class="col-lg-8 col-xl-6 text-center">
                    <a href="/logout.php" class="btn btn-danger btn-lg">Logout</a>
                </div>
            </div>
        </div>
        <!-- Contact cards-->

    </div>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>