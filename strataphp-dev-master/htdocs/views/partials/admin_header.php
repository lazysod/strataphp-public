<?php 
$config = include __DIR__ . '/../../app/config.php';
use App\App;
$controllersDir = __DIR__ . '/../../controllers/';
$controllerFiles = glob($controllersDir . '*Controller.php');
$navConfig = include __DIR__ . '/../../app/adminNavConfig.php';
$sessionPrefix = $config['session_prefix'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>
        <?php
        echo App::config('site_name');
        if (isset($title)) {
            echo ' - ' . (isset($title) ? $title : '');
        }
        ?>
    </title>
    <meta name="robots" content="noindex,nofollow,noodp,noydir, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Font Awesome (required for forum icons)-->
    <link rel="stylesheet" href="/themes/default/assets/fontawesome/css/all.min.css">

    <link rel="stylesheet" href="<?php echo App::config('theme_path'); ?>/css/styles.css">
</head>

<body class="d-flex flex-column h-100">

    <main class="flex-shrink-0">
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container px-5">
                <a class="navbar-brand" href="<?php echo App::config('base_url'); if(isset($_SESSION[$sessionPrefix . 'admin']) && $_SESSION[$sessionPrefix . 'admin'] > 0){ echo '/admin/dashboard';}?>"><img src="<?php echo App::config('logo_url'); ?>" class="img-fluid" alt="<?php echo App::config('site_name'); ?>" id="logo_img"> <?php echo App::config('site_name'); ?> | Admin</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">


                        <?php
                        if (!isset($showNav)) { $showNav = true;
                        }
                        if ($showNav) {
                            if (!isset($_SESSION[$sessionPrefix . 'admin']) || $_SESSION[$sessionPrefix . 'admin'] < 1) {
                                // Only show admin menu if logged in as admin
                                echo '<li class="nav-item"><a class="nav-link" href="/admin/admin_login.php">Admin Login</a></li>';
                            } else {
                                foreach ($navConfig as $key => $config) {
                                    if (!($config['show'] ?? true)) { continue; }
                                    $label = $config['label'] ?? $key;
                                    $url = $config['url'] ?? ('/' . strtolower($key));
                                    $currentPath = '/' . trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
                                    if ($currentPath === '/') { $currentPath = '/'; }
                                    if (!empty($config['children'])) {
                                        $active = ($url === $currentPath) ? ' class="active nav-item dropdown"' : ' class=" nav-item dropdown"';
                                        echo '<li' . $active . '>';
                                        $slug = App::stripSpaces($label);
                                        echo '<a class="nav-link dropdown-toggle" href="' . $url . '" id="navbarDropdown' . $slug . '" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . $label . '</a>';
                                        echo '<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown' . $slug . '">';
                                        foreach ($config['children'] as $childKey => $child) {
                                            if (!($child['show'] ?? true)) { continue; }
                                            $childLabel = $child['label'] ?? $childKey;
                                            $childUrl = $child['url'] ?? ($url . '/' . strtolower($childKey));
                                            $childActive = ($childUrl === $currentPath) ? ' class="active"' : 'class="nav-item dropdown"';
                                            echo '<li' . $childActive . '><a href="' . $childUrl . '" class="dropdown-item">' . $childLabel . '</a></li>';
                                        }
                                        echo '</ul>';
                                    } else {
                                        $active = ($url === $currentPath) ? 'active' : '';
                                        echo '<li class="nav-item ">';
                                        echo '<a class="nav-link ' . $active . '" href="' . $url . '">' . $label . '</a>';
                                    }
                                    echo '</li>';
                                }
                            }
                        }
                        ?>

                            <?php if (!empty($_SESSION[$sessionPrefix . 'user_id'])) : ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION[$sessionPrefix . 'first_name'] ?? 'User'); ?>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                        <?php $currentPath = '/' . trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'); ?>
                                        <li><a class="dropdown-item" href="<?php echo App::config('base_url'); ?>/admin/dashboard/profile">Profile</a></li>
                                        <li><a class="dropdown-item" href="<?php echo App::config('base_url'); ?>/admin/sessions">Device & Sessions</a></li>
                                        <li><a class="dropdown-item" href="<?php echo App::config('base_url'); ?>/logout.php">Logout</a></li>
                                        <li><hr></li>
                                        <li><a class="dropdown-item" href="<?php echo App::config('base_url'); ?>">Front Page</a></li>
                                    </ul>
                                </li>
                            <?php else: ?>
                                <li class="nav-item"><a class="nav-link" href="/user/login">Login</a></li>
                                <li class="nav-item"><a class="nav-link" href="/user/register">Register</a></li>
                            <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
