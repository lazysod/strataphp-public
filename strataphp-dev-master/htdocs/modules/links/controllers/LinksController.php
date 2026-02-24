<?php
namespace App\Modules\Links\Controllers;
use App\DB;
use App\Modules\Admin\Models\Links;
// Minimal LinksController for Linktree-style module
class LinksController
{
    public function index()
    {
    global $config;
    $db = new DB($config);
    $linksModel = new Links($db, $config);
    $links = $linksModel->getAll();
    $show_adult_warning = false; // Set true to show adult warning
    include __DIR__ . '/../views/links.php';
    }
    public function about()
    {
        $bio = 'This is a sample bio. You can edit this in the controller or load from DB.';
        include __DIR__ . '/../views/about.php';
    }
}
