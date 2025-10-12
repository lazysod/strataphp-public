<?php
namespace App\Modules\Links\Controllers;

use App\DB;
use App\Modules\Admin\Models\Links;

/**
 * Links Controller
 * 
 * Handles Linktree-style link management and display functionality.
 * Provides methods for displaying links and about page with error handling.
 * 
 * @package App\Modules\Links\Controllers
 * @author  StrataPHP Framework
 * @version 1.0.0
 */
class LinksController
{
    /**
     * Display the main links page
     * 
     * Retrieves all available links from the database and displays them
     * in a Linktree-style layout. Includes error handling for database failures.
     * 
     * @return void
     */
    public function index()
    {
        try {
            global $config;
            $db = new DB($config);
            $linksModel = new Links($db, $config);
            $links = $linksModel->getAll();
            $show_adult_warning = false; // Set true to show adult warning
            include __DIR__ . '/../views/links.php';
        } catch (\Exception $e) {
            error_log('Error loading links: ' . $e->getMessage());
            $links = [];
            $show_adult_warning = false;
            include __DIR__ . '/../views/links.php';
        }
    }

    /**
     * Display the about page
     * 
     * Shows biographical information or about content for the links page.
     * Includes error handling to gracefully handle display failures.
     * 
     * @return void
     */
    public function about()
    {
        try {
            $bio = 'This is a sample bio. You can edit this in the controller or load from DB.';
            include __DIR__ . '/../views/about.php';
        } catch (\Exception $e) {
            error_log('Error loading about page: ' . $e->getMessage());
            $bio = 'Error loading bio information.';
            include __DIR__ . '/../views/about.php';
        }
    }
}
