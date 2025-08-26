<?php
namespace App;
class View
{
    private $twig;
    private $useTwig;
    private $twigPath;

    public function __construct($config)
    {
        $this->useTwig = !empty($config['use_twig']);
        $this->twigPath = $config['twig_templates_path'] ?? __DIR__ . '/../../views';
        if ($this->useTwig && class_exists('Twig\Loader\FilesystemLoader')) {
            $loader = new \Twig\Loader\FilesystemLoader($this->twigPath);
            $this->twig = new \Twig\Environment($loader);
        }
    }

    public function render($template, $data = [])
    {
        if ($this->useTwig && $this->twig && preg_match('/\.twig$/', $template)) {
            echo $this->twig->render($template, $data);
        } else {
            extract($data);
            include $this->twigPath . '/' . $template;
        }
    }
}
