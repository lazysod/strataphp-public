<?php 
$title = 'Register';
$pageJs = '';
$showNav = true;
require __DIR__ . '/partials/header.php'; 
?>
<main>
    <h1>Register</h1>
    <?php if (!empty($error)) : ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Username: <input type="text" name="username" required></label><br>
        <label>Email: <input type="email" name="email" required></label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="/user/login">Login here</a>.</p>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
