<!-- CMS TEMPLATE: show.php -->
<?php
$title = $data['item']['title'] ?? 'Cms';
$showNav = true;
require __DIR__ . '/../../../views/partials/header.php';
?>

<section class="py-5">
    asdhashdaslkdhasodphapsodhas
    <div class="container px-5">
        <div class="row gx-5 justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="fw-bolder"><?= htmlspecialchars($data['item']['title']) ?></h1>
                    <div>
                        <a href="/cms/<?= $data['item']['id'] ?>/edit" class="btn btn-outline-primary">Edit</a>
                        <a href="/cms" class="btn btn-outline-secondary">Back</a>
                    </div>
                </div>
                
                <div class="content">
                    <?php
                    // Process content to handle line breaks and paragraphs properly
                    $content = $data['item']['content'] ?? '';
                    
                    // If content contains HTML tags, preserve them; otherwise convert line breaks
                    if (strip_tags($content) === $content) {
                        // No HTML tags found, process as plain text with proper paragraphs
                        echo \App\HtmlSanitizer::plainTextToHtml($content);
                    } else {
                        // Content already contains HTML, just output it
                        echo $content;
                    }
                    ?>
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