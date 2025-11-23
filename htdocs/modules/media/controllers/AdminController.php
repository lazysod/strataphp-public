<?php
namespace App\Modules\Media\Controllers;

class AdminController {
    /**
     * Render the media dashboard page
     */
    public function dashboard() {
        include __DIR__ . '/../views/dashboard.php';
    }
}
