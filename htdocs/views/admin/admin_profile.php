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
            // Avatar upload
            $avatarPath = $admin['avatar'] ?? '';
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/webp' => 'webp'];
                $fileType = mime_content_type($_FILES['avatar']['tmp_name']);
                if (isset($allowedTypes[$fileType])) {
                    $ext = $allowedTypes[$fileType];
                    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/storage/uploads/admin/' . $adminId . '/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
                    // Remove existing avatar files
                    foreach (['png', 'jpg', 'jpeg', 'webp'] as $oldExt) {
                        $oldFile = $uploadDir . 'avatar.' . $oldExt;
                        if (file_exists($oldFile)) @unlink($oldFile);
                    }
                    $fileName = 'avatar.' . $ext;
                    $destPath = $uploadDir . $fileName;
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destPath)) {
                        $avatarPath = '/storage/uploads/admin/' . $adminId . '/' . $fileName;
                    } else {
                        $error = 'Failed to save avatar.';
                    }
                } else {
                    $error = 'Invalid avatar file type.';
                }
            }
            $params = [$first_name, $second_name, $email];
            $sql = "UPDATE users SET first_name = ?, second_name = ?, email = ?";
            if ($avatarPath) {
                $sql .= ", avatar = ?";
                $params[] = $avatarPath;
            }
            if ($pwd) {
                $hashed = password_hash($pwd, PASSWORD_DEFAULT);
                $sql .= ", password = ?";
                $params[] = $hashed;
            }
            $sql .= " WHERE id = ? AND is_admin = 1";
            $params[] = $adminId;
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
                    <div class="card-body text-center">
                        <?php
                        $avatarPath = $admin['avatar'] ?? '';
                        $adminAvatarDir = '/storage/uploads/admin/' . $adminId . '/';
                        $avatarFullPath = $_SERVER['DOCUMENT_ROOT'] . $avatarPath;
                        if ($avatarPath && file_exists($avatarFullPath)) {
                            echo '<img src="' . htmlspecialchars($avatarPath) . '" alt="Avatar" class="rounded-circle mb-2" style="width:80px;height:80px;object-fit:cover;">';
                        } else {
                            $gravatar = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($admin['email'] ?? ''))) . '?s=80&r=g&d=mm';
                            echo '<img src="' . $gravatar . '" alt="Avatar" class="rounded-circle mb-2" style="width:80px;height:80px;object-fit:cover;">';
                        }
                        ?>
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($admin['username'] ?? '') ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['email'] ?? '') ?></p>
                        <p><strong>Role:</strong> Admin</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <form method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(TokenManager::csrf($config)) ?>" />
                    <div class="mb-3 text-center">
                        <label class="form-label">Avatar</label><br>
                        <input type="file" name="avatar" accept="image/png,image/jpeg,image/jpg,image/webp" class="form-control mt-2" style="max-width:300px;margin:auto;">
                        <small class="text-muted">Allowed: PNG, JPG, JPEG, WEBP. Max 2MB.</small>
                    </div>
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