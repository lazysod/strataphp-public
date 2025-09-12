<?php
// Ensure $config and $modules are always available
if (!isset($config)) {
    $config = file_exists($_SERVER['DOCUMENT_ROOT'] . '/app/config.php') ? include $_SERVER['DOCUMENT_ROOT'] . '/app/config.php' : [];
}

// Admin session check
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/config.php';
$sessionPrefix = $config['session_prefix'] ?? ($config['prefix'] ?? 'framework');
if (!isset($_SESSION[$sessionPrefix . 'admin']) || $_SESSION[$sessionPrefix . 'admin'] < 1) {
    header('Location: /admin/admin_login.php');
    exit;
}

require $_SERVER['DOCUMENT_ROOT'] . '/views/partials/admin_header.php'; ?>
<section class="py-5">
    <div class="container px-5">
        <h2>Admin Active Sessions!</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Device</th>
                    <th>IP Address</th>
                    <th>Created</th>
                    <th>Last Active</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sessions as $session): ?>
                    <?php $isCurrent = ($session['id'] ?? null) == ($_SESSION[PREFIX . 'session_id'] ?? null); ?>
                    <tr<?= $isCurrent ? ' style="background:#e0ffe0;font-weight:bold;"' : '' ?>>
                        <td>
                            <?php
                            $deviceLabel = !empty($session['device_info']) ? $session['device_info'] : ($session['device_type'] ?? 'Unknown');
                            ?>
                            <?php if ($isCurrent): ?>
                                <form method="post" action="/admin/sessions/update-device">
                                    <input type="hidden" name="session_id" value="<?= $session['id'] ?>">
                                    <input type="text" name="device_info" value="<?= htmlspecialchars($deviceLabel) ?>" size="18">
                                    <button type="submit">Rename</button>
                                </form>
                            <?php else: ?>
                                <?= htmlspecialchars($deviceLabel) ?>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($session['ip_address'] ?? '') ?></td>
                        <td><?= htmlspecialchars($session['created_at'] ?? '') ?></td>
                        <td><?= htmlspecialchars($session['last_active'] ?? '') ?></td>
                        <td>
                            <?php if (!$isCurrent): ?>
                                <form method="post" action="/admin/sessions/revoke">
                                    <input type="hidden" name="session_id" value="<?= $session['id'] ?>">
                                    <button type="submit">Revoke</button>
                                </form>
                            <?php else: ?>
                                Current Session
                            <?php endif; ?>
                        </td>
                        </tr>
                    <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/views/partials/footer.php'; ?>