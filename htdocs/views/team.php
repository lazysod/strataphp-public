<?php 
$title = 'Our Team';
$pageJs = '';
$showNav = true;
require __DIR__ . '/partials/header.php'; 
?>
<main>
    <h1>Meet the Team</h1>
    <ul>
        <?php foreach ($team as $member): ?>
            <li><?php echo htmlspecialchars($member); ?></li>
        <?php endforeach; ?>
    </ul>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
