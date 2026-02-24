<?php
use App\Modules\User\Controllers\UserLoginController;
use App\Modules\User\Controllers\UserProfileController;
use App\App;
// Ensure Composer autoloader is loaded for App class
$composerAutoload = __DIR__ . '/../../../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}
// modules/User/routes.php
// Register user module routes using the router and modules['user'] config

global $router;
if (!empty(App::config('modules')['user'])) {
    // Register / as root if user is the default module
    if (!empty(App::config('default_module')) && App::config('default_module') === 'user') {
        $router->get('/', [UserLoginController::class, 'index']);
    }
    $router->get('/user/profile_settings', [\App\Modules\User\Controllers\UserProfileSettingsController::class, 'index']);
    $router->post('/user/profile_settings', [\App\Modules\User\Controllers\UserProfileSettingsController::class, 'index']);
    $router->get('/user/login', [UserLoginController::class, 'index']);
    $router->get('/user/dashboard', [\App\Modules\User\Controllers\UserDashboardController::class, 'index']); // Will show new dashboard menu
    $router->get('/dashboard', [\App\Modules\User\Controllers\UserDashboardController::class, 'index']); // Will show new dashboard menu
    $router->get('/user/links', [\App\Modules\User\Controllers\UserLinksController::class, 'index']); // Old dashboard view now at /user/links
    $router->post('/user/login', [UserLoginController::class, 'index']);
    $router->get('/user/register', [\App\Modules\User\Controllers\UserRegisterController::class, 'index']);
    $router->post('/user/register', [\App\Modules\User\Controllers\UserRegisterController::class, 'index']);
    $router->get('/user/profile', [UserProfileController::class, 'index']);
    $router->post('/user/profile', [UserProfileController::class, 'index']);
    $router->get('/user/reset-request', [\App\Modules\User\Controllers\UserResetRequestController::class, 'index']);
    $router->post('/user/reset-request', [\App\Modules\User\Controllers\UserResetRequestController::class, 'index']);
    $router->get('/user/reset', [\App\Modules\User\Controllers\UserResetController::class, 'index']);
    $router->post('/user/reset', [\App\Modules\User\Controllers\UserResetController::class, 'index']);
    $router->get('/user/activate', [\App\Modules\User\Controllers\UserActivateController::class, 'index']); 
    $router->get('/user/sessions', [\App\Modules\User\Controllers\UserSessionsController::class, 'index']);
    $router->post('/user/sessions/revoke', [\App\Modules\User\Controllers\UserSessionsController::class, 'revoke']);
    $router->post('/user/sessions/update-device', [\App\Modules\User\Controllers\UserSessionsController::class, 'updateDevice']);
    // Add more user routes as needed
    // Events module routes
    $router->get('/user/events', [\App\Modules\User\Events\Controllers\EventsController::class, 'index']);
    $router->get('/user/events/show', function() {
        $id = $_GET['id'] ?? null;
        (new \App\Modules\User\Events\Controllers\EventsController())->show($id);
    });
    $router->get('/user/events/create', [\App\Modules\User\Events\Controllers\EventsController::class, 'create']);
    $router->post('/user/events/store', function() {
        (new \App\Modules\User\Events\Controllers\EventsController())->store($_POST);
    });
    $router->get('/user/themes', function() {
        // Load config and dependencies
        $config = require $_SERVER['DOCUMENT_ROOT'] . '/app/config.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/app/Version.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/app/iconpicker.php';
        require_once __DIR__ . '/../../../vendor/autoload.php';
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $sessionPrefix = $config['session_prefix'] ?? 'app_';
        if (empty($_SESSION[$sessionPrefix . 'user_id'])) {
            header('Location: /user/login');
            exit;
        }
        $db = new \App\DB($config['db']);
        $userModel = new \App\User($db, $config);
        $themeService = new \App\Themes\UserTheme($db, $config);
        $userId = $_SESSION[$sessionPrefix . 'user_id'];
        $profile = $userModel->get_profile_from_name($_SESSION[$sessionPrefix . 'profile']['profile_name'] ?? null);

        // App::dump($_SESSION);
         if (!$profile || !is_array($profile)) {
            $profile = [
                'bio' => '',
                'pride_logo' => 0,
                'verified' => 0,
                'profile_name' => '',
                'locked' => 0,
            ];
        }
        // App::dump($profile, 'Profile Data');
        // die();
        // Ensure 'links' key always exists for the view
        if (!isset($profile['links'])) {
            $profile['links'] = [];
        }
        // $sql = "SELECT `link_id`, `user_id`, `link_title`, `short_link`, `link_url`, `link_order`, `adult`, `date_added`, `active`, `expiry_date`, `is_link`, `is_email` FROM `links` WHERE user_id = ? ORDER BY link_order ASC";
        // $links = $db->fetchAll($sql, [$_SESSION[$sessionPrefix . 'user_id']]);
        $profile_id = $_SESSION[$sessionPrefix . 'active_profile'] ?? null;
        $userClass = new \App\User($db, $config);
        if (isset($profile['lnk_groups']) && $profile['lnk_groups'] > 0 && $profile_id) {
            $mode = 1;
            $LinkGroups = $db->fetchAll("SELECT * FROM link_groups WHERE profile_id = ? ORDER BY group_order ASC", [$profile_id]);
            // Remove duplicate group names
            $uniqueGroups = [];
            foreach ($LinkGroups as $g) {
                if (!isset($uniqueGroups[$g['group_name']])) {
                    $uniqueGroups[$g['group_name']] = $g;
                }
            }
            $LinkGroups = array_values($uniqueGroups);
            foreach ($LinkGroups as &$group) {
                $group['links'] = $db->fetchAll(
                    "SELECT l.*, ltg.group_order FROM links l
                     INNER JOIN links_to_group ltg ON l.link_id = ltg.link_id
                     WHERE l.profile_id = ? AND ltg.group_id = ?
                     ORDER BY ltg.group_order ASC",
                    [$profile_id, $group['id']]
                );
            }
            unset($group);
        } else if ($profile_id) {
            $LinkList = $db->fetchAll("SELECT * FROM links WHERE profile_id = ? ORDER BY link_order ASC", [$profile_id]);
        }
        // end new block
        // $profile['links'] = $links;
        // Get themes
        $sql = "SELECT `theme_id`, `theme_title`, `theme_path`, `active` FROM `themes` WHERE `active` = 1 ORDER BY `theme_title` ASC";
        $theme_list = $db->fetchAll($sql);
        $themes = $themeService->get_all();
        $selectedTheme = $_GET['theme'] ?? ($themes[0]['theme_path'] ?? 'default');
        foreach ($themes as $theme) {
            if ($theme['theme_path'] === $selectedTheme) {
                $themeData = $theme;
                break;
            }
        }
        if (!isset($themeData)) {
            $themeData = $themeService->get_default_theme();
        }
        include __DIR__ . '/views/theme_tester.php';
    });
    $router->get('/user/sso', [\App\Modules\User\Controllers\SSOController::class, 'index']);
    $router->post('/user/sso/revoke', [\App\Modules\User\Controllers\SSOController::class, 'revoke']);
}
    // Additional context lines can be added here if necessary
