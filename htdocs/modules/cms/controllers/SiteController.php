<?php
namespace App\Modules\Cms\Controllers;

use App\DB;
use App\Modules\Cms\Models\Site;

class SiteController
{
    private $db;
    private $config;

    public function __construct()
    {
        // Define the constant to allow access to CMS view files
        if (!defined('STRPHP_ROOT')) {
            define('STRPHP_ROOT', true);
        }
        $this->config = include dirname(__DIR__, 3) . '/app/config.php';
        $this->db = new DB($this->config);
    }

    // List all sites
    public function index()
    {
        require_once __DIR__ . '/../models/Site.php';
        $siteModel = new Site($this->config);
        $sites = $siteModel->getAll();
        include __DIR__ . '/../views/admin/sites_list.php';
    }

    // Show create site form
    public function create()
    {
        include __DIR__ . '/../views/admin/site_create.php';
    }

    // Handle create site POST
    public function store()
    {
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
    }

    // Regenerate API key for a site
    public function regenerateKey($id)
    {
        require_once __DIR__ . '/../models/Site.php';
        $siteModel = new Site($this->config);
        $apiKey = bin2hex(random_bytes(32));
        $siteModel->updateApiKey($id, $apiKey);
        $_SESSION['success'] = 'API key regenerated!';
        header('Location: /admin/cms/sites');
        exit;
    }

    // Delete a site
    public function delete($id)
    {
        require_once __DIR__ . '/../models/Site.php';
        $siteModel = new Site($this->config);
        if ($siteModel->delete($id)) {
            $_SESSION['success'] = 'Site deleted.';
        } else {
            $_SESSION['error'] = 'Failed to delete site.';
        }
        header('Location: /admin/cms/sites');
        exit;
    }
}
