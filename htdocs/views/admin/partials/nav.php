<?php
// Simple admin navigation bar renderer
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
// use App\App;
// App::dump($_SESSION);
if (!defined('PREFIX')) {
    $config = include dirname(__DIR__, 3) . '/app/config.php';
    define('PREFIX', $config['session_prefix'] ?? 'app_');
}

if (empty($_SESSION[PREFIX . 'admin']) || $_SESSION[PREFIX . 'admin'] < 1) {
  // Not an admin, do not render nav bar
  return;
}
if (!isset($modules)) {
  $modules = include dirname(__DIR__, 3) . '/app/modules.php';
}
$navConfig = include dirname(__DIR__, 3) . '/app/adminNavConfig.php';
$config = include __DIR__ . '/../../../app/config.php';
$sessionPrefix = $config['session_prefix'];

?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?php echo \App\App::config('base_url'); if(isset($_SESSION[$sessionPrefix . 'admin']) && $_SESSION[$sessionPrefix . 'admin'] > 0){ echo '/admin/dashboard';}?>"><img src="<?php echo \App\App::config('logo_url'); ?>" class="img-fluid" alt="<?php echo \App\App::config('site_name'); ?>" id="logo_img" style="max-height: 40px;"> <?php echo \App\App::config('site_name'); ?> | Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="adminNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php foreach ($navConfig as $key => $item):
          if ((isset($item['show']) && !$item['show']) || (isset($item['visible']) && !$item['visible'])) continue;
        ?>
          <li class="nav-item">
            <a class="nav-link" href="<?= htmlspecialchars($item['url']) ?>">
              <?php if (!empty($item['icon'])): ?><i class="fa <?= htmlspecialchars($item['icon']) ?>"></i> <?php endif; ?>
              <?= htmlspecialchars($item['label']) ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</nav>
