<?php
$title = $data['item']['title'] ?? 'ModuleDirectory';
$showNav = true;
require __DIR__ . '/../../../views/partials/header.php';
?>

<section class="py-5">
    <div class="container px-5">
        <div class="row gx-5 justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="fw-bolder"><?= htmlspecialchars($data['item']['title']) ?></h1>
                    <div>
                        <a href="/module_directory/<?= $data['item']['id'] ?>/edit" class="btn btn-outline-primary">Edit</a>
                        <a href="/module_directory" class="btn btn-outline-secondary">Back</a>
                    </div>
                </div>
                
                <div class="content">
                    <?= nl2br(htmlspecialchars($data['item']['content'])) ?>
                </div>
                
                <div class="mt-4 text-muted">
                    <small>Created: <?= date('F j, Y g:i A', strtotime($data['item']['created_at'])) ?></small>
                    <?php if (isset($data['item']['updated_at'])): ?>
                        <br><small>Updated: <?= date('F j, Y g:i A', strtotime($data['item']['updated_at'])) ?></small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../../../views/partials/footer.php'; ?>