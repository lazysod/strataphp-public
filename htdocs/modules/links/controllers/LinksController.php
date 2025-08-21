<?php
// Minimal LinksController for Linktree-style module
class LinksController
{
    public function index()
    {
        global $config;
        $db = new DB($config);
        // Use the same Links model as admin
        if (class_exists('Links')) {
            $linksModel = new Links($db, $config);
            $links = $linksModel->getAll();
        } else {
            // Fallback to demo data if model not found
            $links = [
                ['title' => 'GitHub', 'url' => 'https://github.com/', 'icon' => 'fab fa-github'],
                ['title' => 'My Website', 'url' => 'https://example.com/', 'icon' => 'fas fa-link'],
                ['title' => 'Twitter', 'url' => 'https://twitter.com/', 'icon' => 'fab fa-twitter'],
            ];
        }
        $show_adult_warning = false; // Set true to show adult warning
        include __DIR__ . '/../views/links.php';
    }
    public function about()
    {
        $bio = 'This is a sample bio. You can edit this in the controller or load from DB.';
        include __DIR__ . '/../views/about.php';
    }
}
