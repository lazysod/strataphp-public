<?php
namespace App\Controllers;
class HomeController
{
    public function index()
    {
    $title = 'New Framework';
    $pageJs = 'home';
    extract(['title' => $title, 'pageJs' => $pageJs]);
    include __DIR__ . '/../views/home.php';
    }
}
