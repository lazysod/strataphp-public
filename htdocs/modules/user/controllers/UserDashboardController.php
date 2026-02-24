<?php
namespace App\Modules\User\Controllers;

/**
 * User Dashboard Controller
 *
 * Displays the user dashboard page.
 */
class UserDashboardController
{
    public function index()
    {
        // You can add logic here to fetch user data, stats, etc.
        // For now, just show a simple dashboard view.
        $view = __DIR__ . '/../views/dashboard.php';
        if (file_exists($view)) {
            include $view;
        } else {
            echo '<h1>User Dashboard</h1><p>Welcome to your dashboard!</p>';
        }
    }
}
