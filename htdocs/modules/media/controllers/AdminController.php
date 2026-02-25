<?php
namespace App\Modules\media\Controllers;

class AdminController
{
    /**
     * Render the media dashboard page
     */
    public function dashboard()
    {
        include __DIR__ . '/../views/dashboard.php';
    }
}
