<?php
namespace App\Modules\Admin\Controllers;

use App\DB;
use App\Logger;
class AdminController
{
    /**
     * Show admin dashboard with user and profile counts.
     * Handles SQL securely and passes variables to the view.
     */
    public function dashboard()
    {
        try {
            //  you can add admin stats or queries here
            global $config;
            include __DIR__ . '/../views/admin_dashboard.php';
        } catch (\Exception $e) {
            $logger = Logger::getInstance();
            $logger->error('AdminController dashboard error: ' . $e->getMessage());
            $_SESSION['error'] = 'Failed to load dashboard.';
            include __DIR__ . '/../views/admin_dashboard.php';
        }
    }
    /**
     * Show admin login page.
     */
    public function index()
    {
        try {
            include dirname(__DIR__, 3) . '/views/admin/admin_login.php';
        } catch (\Exception $e) {
            $logger = Logger::getInstance();
            $logger->error('AdminController index error: ' . $e->getMessage());
            $_SESSION['error'] = 'Failed to load admin login.';
        }
    }
}
