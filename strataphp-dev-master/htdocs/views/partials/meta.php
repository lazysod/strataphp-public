<?php
use App\App;
$currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
    . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<title><?php echo App::config('site_name'); ?> | <?php echo App::config('site_description'); ?></title>
<meta name="Author" content="<?php echo App::config('site_name'); ?>"> 
<meta name="description" content="<?php echo App::config('site_name'); ?> is the fun word puzzle game that challenges your vocabulary and creativity.">
<meta name="keywords" content="writing, editing, collaboration, <?php echo App::config('site_name'); ?>">
<meta name="expires" content="never"> 
<meta name="language" content="EN"> 
<meta name="distribution" content="Global">  
<meta name="copyright" content="<?php echo App::config('site_name'); ?>"> 
<meta name="robots" content="index,follow,noodp,noydir, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

 <!-- canonical Link -->
<link rel="canonical" href="<?php echo htmlspecialchars($currentUrl); ?>">
<!-- start link -->
<link rel="image_src" href="<?php echo App::config('base_url'); ?>/assets/images/large_logo.png">
<meta property="og:type" content="website">
<meta property="og:title" content="<?php echo App::config('site_name'); ?> | <?php echo App::config('site_description'); ?>">
<meta property="og:image" content="<?php echo App::config('base_url'); ?>/assets/images/large_logo.png">
<meta property="og:description" content="<?php echo App::config('site_name'); ?> is the fun word puzzle game that challenges your vocabulary and creativity.">
<meta property="og:url" content="<?php echo htmlspecialchars($currentUrl); ?>">
<link href="<?php echo App::config('base_url'); ?>/assets/images/favicon.ico" rel="shortcut icon" type="image/x-icon">
<!-- Twitter Card -->

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@">
<meta name="twitter:title" content="<?php echo App::config('site_name'); ?> | <?php echo App::config('site_description'); ?>">
<meta name="twitter:description" content="<?php echo App::config('site_name'); ?> is the fun word puzzle game that challenges your vocabulary and creativity.">
<meta name="twitter:image" content="<?php echo App::config('base_url'); ?>/assets/images/large_logo.png">

<!-- Fav Icons -->
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo App::config('base_url'); ?>/assets/images/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo App::config('base_url'); ?>/assets/images/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo App::config('base_url'); ?>/assets/images/favicon-16x16.png">