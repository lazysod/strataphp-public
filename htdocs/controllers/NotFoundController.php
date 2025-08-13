<?php 

class NotFoundController
{
    public function index()
    {
        http_response_code(404);
        // Load the 404 view
        // This view should be created in the views directory
        // e.g., htdocs/views/404.php
        // It should contain a user-friendly message for not found pages
        $showNav = false; // Show navigation for admin
        $h1 = "404 - Not Found";
        $message = "The page you requested could not be found.";
        $title = '404 - Not Found';
        $homeLink = '/'; // Link to home page
        include __DIR__ . '/../views/system/404.php';
    }
}