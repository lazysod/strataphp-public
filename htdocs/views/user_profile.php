<?php 
$title = 'Profile';
$pageJs = '';
$showNav = true;
require __DIR__ . '/partials/header.php'; 
?>
<main>
    <h1>Your Profile</h1>
    <?php if ($user) : ?>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <a href="/user/logout">Logout</a>
    <?php else: ?>
        <p>You are not logged in. <a href="/user/login">Login</a></p>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
