<?php
use Modules\Blog\Controllers\AdminBlogController;

// Blog admin routes
if (isset($router)) {
    $router->get('/admin/blog', [AdminBlogController::class, 'index']);
    $router->get('/admin/blog/post/add', [AdminBlogController::class, 'addPost']);
    $router->post('/admin/blog/post/add', [AdminBlogController::class, 'addPost']);
    // Add more admin routes for add/edit/delete post
}
// Public blog routes to be added
