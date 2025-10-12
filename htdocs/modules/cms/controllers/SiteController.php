<?php

namespace App\Modules\Cms\Controllers;

use App\DB;
use App\Modules\Cms\Models\Site;

/**
 * Controller for managing CMS Sites in the admin panel.
 *
 * Handles listing, creating, updating, and deleting sites, as well as API key management.
 */
class SiteController
{
    /**
     * @var DB Database connection
     */
    private $db;
    /**
     * @var array Configuration array
     */
    private $config;

    /**
     * SiteController constructor. Initializes DB and config.
     */
    public function __construct()
    {
        // Define the constant to allow access to CMS view files
        if (!defined('STRPHP_ROOT')) {
            define('STRPHP_ROOT', true);
        }
        $this->config = include dirname(__DIR__, 3) . '/app/config.php';
        $this->db = new DB($this->config);
    }

    /**
     * List all sites.
     * Loads the sites list view.
     * @return void
     */
    public function index()
    {
        try {
            require_once __DIR__ . '/../models/Site.php';
            $siteModel = new Site($this->config);
            $sites = $siteModel->getAll();
            include __DIR__ . '/../views/admin/sites_list.php';
        } catch (\Throwable $e) {
            error_log("SiteController::index error: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred loading the sites list.';
            header('Location: /admin/cms');
            exit;
        }
    }

    /**
     * Show the create site form.
     * @return void
     */
    public function create()
    {
        try {
            include __DIR__ . '/../views/admin/site_create.php';
        } catch (\Throwable $e) {
            error_log("SiteController::create error: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred loading the create site form.';
            header('Location: /admin/cms/sites');
            exit;
        }
    }

    /**
     * Handle create site POST request.
     * @return void
     */
    public function store()
    {
        try {
            require_once __DIR__ . '/../models/Site.php';
            $siteModel = new Site($this->config);
            $name = trim($_POST['name'] ?? '');
            if (!$name) {
                $_SESSION['error'] = 'Site name is required.';
                header('Location: /admin/cms/sites/create');
                exit;
            }
            $apiKey = bin2hex(random_bytes(32));
            $siteModel->create($name, $apiKey);
            $_SESSION['success'] = 'Site created!';
            header('Location: /admin/cms/sites');
            exit;
        } catch (\Throwable $e) {
            error_log("SiteController::store error: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred creating the site.';
            header('Location: /admin/cms/sites/create');
            exit;
        }
    }

    /**
     * Regenerate API key for a site.
     * @param int $id Site ID
     * @return void
     */
    public function regenerateKey($id)
    {
        try {
            require_once __DIR__ . '/../models/Site.php';
            $siteModel = new Site($this->config);
            $apiKey = bin2hex(random_bytes(32));
            $siteModel->updateApiKey($id, $apiKey);
            $_SESSION['success'] = 'API key regenerated!';
            header('Location: /admin/cms/sites');
            exit;
        } catch (\Throwable $e) {
            error_log("SiteController::regenerateKey error: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred regenerating the API key.';
            header('Location: /admin/cms/sites');
            exit;
        }
    }

    /**
     * Delete a site.
     * @param int $id Site ID
     * @return void
     */
    public function delete($id)
    {
        try {
            require_once __DIR__ . '/../models/Site.php';
            $siteModel = new Site($this->config);
            if ($siteModel->delete($id)) {
                $_SESSION['success'] = 'Site deleted.';
            } else {
                $_SESSION['error'] = 'Failed to delete site.';
            }
            header('Location: /admin/cms/sites');
            exit;
        } catch (\Throwable $e) {
            error_log("SiteController::delete error: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred deleting the site.';
            header('Location: /admin/cms/sites');
            exit;
        }
    }
}
