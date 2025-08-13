<?php 
$title = 'Login';
$pageJs = '';
$showNav = true;
require __DIR__ . '/partials/header.php'; 
?>
<main>
    <h1>Login</h1>
    <?php if (!empty($error)) : ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Username: <input type="text" name="username" required></label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="/user/register">Register here</a>.</p>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
