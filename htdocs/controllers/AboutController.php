<?php
use App\View;
use App\App;
class AboutController
{
    public function index()
    {
        $config = require __DIR__ . '/../app/config.php';
        $view = new View($config);
        $data = [
            'title' => 'About StrataPHP',
            'content' => 'We are a modern PHP framework company.',
            'site_name' => App::config('site_name')
        ];

        // Example: Conditionally render PHP or Twig view based on config
        // If Twig is enabled in config, render Twig template
        if (!empty($config['use_twig']) && $config['use_twig'] !== false && $config['use_twig'] !== 'false') {
            // Render Twig template (about.twig)
            $view->render('about.twig', $data);
        } else {
            // Render classic PHP view (about.php)
            extract($data);
            include __DIR__ . '/../views/about.php';
        }
    }

}
