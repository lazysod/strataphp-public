<?php
namespace App\Controllers;
error_log('DEBUG: HomeController.php loaded');
class HomeController
{
    public function index()
    {
        // Example: load a view
        include __DIR__ . '/../views/home.php';
    }
}
