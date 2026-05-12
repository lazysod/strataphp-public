<?php
namespace App\Modules\Media\Controllers;
use App\DB;
use App\Logger;
/**
 * Media Admin Controller
 * Handles rendering of the media dashboard page.
 */
class AdminController
{
    /**
     * Render the media dashboard page
     */
    /**
     * Render the media dashboard page
     * Handles errors gracefully.
     */
    public function dashboard()
    {
        $logger = Logger::getInstance();
        try {
            include __DIR__ . '/../views/dashboard.php';
        } catch (\Exception $e) {
            $logger->error('AdminController dashboard error: ' . $e->getMessage());
            echo '<h2>Error loading dashboard view.</h2>';
        }
    }
}
