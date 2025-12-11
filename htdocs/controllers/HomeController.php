<?php
namespace App\Controllers;
class HomeController
{
    public function index()
    {
        $title = 'New Framework';
        $pageJs = 'home';
        \App\App::loadView('home', [
            'title' => $title,
            'pageJs' => $pageJs
        ]);
    }
}
