<?php
// User Sessions Dashboard
?>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/views/partials/header.php'; ?>
<section class="py-5">
    <div class="container px-5">
        <div class="bg-light rounded-3 py-5 px-4 px-md-5 mb-5">
            <div class="text-center mb-5">
                <h1 class="fw-bolder">Your Profile</h1>
            </div>
            <div class="row gx-5 justify-content-center">
                <div class="col-lg-12 col-xl-12">
                    <h2>Your Active Sessions</h2>
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
                            <?php $isCurrent = ($session['id'] ?? null) == ($_SESSION[$sessionPrefix . 'session_id'] ?? null); ?>
                            <tr<?= $isCurrent ? ' style="background:#e0ffe0;font-weight:bold;"' : '' ?>>
                                <td>
                                    <?php
                                    $deviceLabel = !empty($session['device_info']) ? $session['device_info'] : ($session['device_type'] ?? 'Unknown');
                                    ?>
                                    <?php if ($isCurrent): ?>
                                        <form method="post" action="/user/sessions/update-device">
                                            <input type="hidden" name="session_id" value="<?= $session['id'] ?>">
                                            <input type="text" name="device_info" value="<?= htmlspecialchars($deviceLabel) ?>" size="18">
                                            <button type="submit">Rename</button>
                                        </form>
                                    <?php else: ?>
                                        <?= htmlspecialchars($deviceLabel) ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($session['ip_address'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($session['created_at'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($session['last_active'] ?? '-') ?></td>
                                <td>
                                    <?php if (!$isCurrent): ?>
                                        <form method="post" action="/user/sessions/revoke">
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
            </div>
        </div>
    </div>
</section>

<?php require $_SERVER['DOCUMENT_ROOT'] . '/views/partials/footer.php'; ?>