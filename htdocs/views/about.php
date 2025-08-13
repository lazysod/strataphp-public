<?php
$title = 'About - New Framework';
$pageJs = 'about';
require __DIR__ . '/partials/header.php';
?>
<section class="py-5" id="features">
    <div class="container px-5 my-5">
        <div class="row gx-5">
            <div class="col-lg-8 mx-auto mb-5 mb-lg-0">
                <h1><?php echo $title; ?></h1>
                <p class="lead"><?php echo App::config('site_name'); ?> is a minimal PHP framework designed to help you build web applications quickly and efficiently. It includes essential features like routing, controllers, and views, allowing you to focus on your application's logic without worrying about the underlying infrastructure.</p>
                <p class="lead">The framework is designed to be lightweight and easy to use, making it suitable for both beginners and experienced developers. It provides a solid foundation for building web applications, with a focus on simplicity and performance.</p>
                <p class="lead">You can customize this page by editing the <code>AboutController.php</code> file in the <code>controllers</code> directory and the <code>about.php</code> view file in the <code>views</code> directory. Feel free to explore the code and make changes to suit your needs.</p>
            </div>

        </div>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>