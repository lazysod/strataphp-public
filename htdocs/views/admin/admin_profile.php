<?php
$startPath = dirname(__DIR__, 2) . '/app/start.php';
if (file_exists($startPath)) {
    $config = include dirname(__DIR__, 2) . '/app/config.php';
    include_once $startPath;
}

$sessionPrefix = $config['session_prefix'] ?? ($config['prefix'] ?? 'framework');
if (!isset($_SESSION[$sessionPrefix . 'admin']) || $_SESSION[$sessionPrefix . 'admin'] < 1) {
    header('Location: /admin/admin_login.php');
    exit;
}

$db = class_exists('DB') ? new DB($config) : null;
$adminId = $_SESSION[$sessionPrefix . 'admin'] ?? null;
$admin = null;
if ($db && $adminId) {
    $admin = $db->fetch("SELECT * FROM users WHERE id = ? AND is_admin = 1", [$adminId]);
}
$success = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tm = new TokenManager($config);
    $verify = $tm->verify($_POST['csrf_token'] ?? '');
    if (!isset($_POST['csrf_token']) || $verify['status'] !== 'success') {
        $error = 'Invalid CSRF token.';
    } elseif (!$admin) {
        $error = 'Admin not found.';
    } else {
        $first_name = trim($_POST['first_name'] ?? '');
        $second_name = trim($_POST['second_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pwd = $_POST['pwd'] ?? '';
        $pwd2 = $_POST['pwd2'] ?? '';
        if ($pwd && $pwd !== $pwd2) {
            $error = 'Passwords do not match.';
        } elseif (!$first_name || !$second_name || !$email) {
            $error = 'All fields except password are required.';
        } else {
            $params = [$first_name, $second_name, $email, $adminId];
            $sql = "UPDATE users SET first_name = ?, second_name = ?, email = ?";
            if ($pwd) {
                $hashed = password_hash($pwd, PASSWORD_DEFAULT);
                $sql .= ", password = ?";
                $params[] = $hashed;
            }
            $sql .= " WHERE id = ? AND is_admin = 1";
            if ($pwd) {
                // Move id to end for param order
                $params = [$first_name, $second_name, $email, $hashed, $adminId];
            }
            $db->query($sql, $params);
            $success = 'Profile updated successfully.';
            // Refresh admin data
            $admin = $db->fetch("SELECT * FROM users WHERE id = ? AND is_admin = 1", [$adminId]);
        }
    }
}
?>
<?php require __DIR__ . '/../partials/admin_header.php'; ?>
<section class="py-5">
    <div class="container mt-5">
        <?php if (!empty($success)) : ?>
            <div class="alert alert-success"> <?php echo htmlspecialchars($success) ?> </div>
        <?php endif; ?>
        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger"> <?php echo htmlspecialchars($error) ?> </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-6">
                <h2>Admin Profile</h2>
                <div class="card" style="max-width: 500px;">
                    <div class="card-body">
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($admin['username'] ?? '') ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['email'] ?? '') ?></p>
                        <p><strong>Role:</strong> Admin</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <form method="post" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(TokenManager::csrf($config)) ?>" />
                    <div class="form-floating mb-3">
                        <input class="form-control" id="first_name" name="first_name" type="text" value="<?php echo htmlspecialchars($admin['first_name'] ?? '') ?>" required />
                        <label for="first_name">First Name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" id="second_name" name="second_name" type="text" value="<?php echo htmlspecialchars($admin['second_name'] ?? '') ?>" required />
                        <label for="second_name">Second Name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" id="email" name="email" type="email" value="<?php echo htmlspecialchars($admin['email'] ?? '') ?>" required />
                        <label for="email">Email address</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" id="pwd" name="pwd" type="password" placeholder="New password (leave blank to keep current)" />
                        <label for="pwd">New Password</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" id="pwd2" name="pwd2" type="password" placeholder="Confirm new password" />
                        <label for="pwd2">Confirm New Password</label>
                    </div>
                    <div class="d-grid"><button class="btn btn-primary btn-lg" type="submit">Update Profile</button></div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>