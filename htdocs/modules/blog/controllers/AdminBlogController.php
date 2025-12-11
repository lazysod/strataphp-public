<?php
namespace Modules\Blog\Controllers;

class AdminBlogController
{
    // List blog posts
    public function index()
    {
        include __DIR__ . '/../views/admin/index.php';
    }

    // Add/edit/delete post methods to be implemented
    public function addPost()
    {
        $post = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $featured_image = trim($_POST['featured_image'] ?? '');
            if ($title && $content) {
                // Save to DB (pseudo-code, replace with real DB logic)
                // $db->insert('blog_posts', [...]);
                header('Location: /admin/blog');
                exit;
            } else {
                $error = 'Title and content are required.';
                $post = compact('title', 'content', 'featured_image');
            }
        }
        include __DIR__ . '/../views/admin/post_form.php';
    }
}
