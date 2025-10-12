<?php
// Theme page template for CMS pages
// Copy of modules/cms/views/page.php, but placed in the theme directory for clarity
if (!defined('STRPHP_ROOT')) {
    exit('Direct access not allowed');
}
?>
<!-- THEME TEMPLATE: themes/default/page.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($meta_description ?? '') ?>">
    <title><?= htmlspecialchars($meta_title ?? $title ?? 'Page') ?></title>
    <link rel="stylesheet" href="/themes/default/css/styles.css">
    <style>
    /* You can add theme-specific overrides here */
    </style>
</head>
<body>
    <main class="page-content">
        <?php
        $processedContent = $content ?? '';
        if (strip_tags($processedContent) === $processedContent) {
            $processedContent = htmlspecialchars($processedContent);
            $paragraphs = preg_split('/\n\s*\n/', $processedContent);
            $processedParagraphs = [];
            foreach ($paragraphs as $paragraph) {
                $paragraph = trim($paragraph);
                if (!empty($paragraph)) {
                    $paragraph = nl2br($paragraph);
                    $processedParagraphs[] = '<p>' . $paragraph . '</p>';
                }
            }
            $processedContent = implode("\n", $processedParagraphs);
        }
        echo $processedContent;
        ?>
    </main>
    <?php if (isset($page) && $page): ?>
    <div class="page-meta">
        <?php if (!empty($page['created_at'])): ?>
            <p>Published: <?= date('F j, Y', strtotime($page['created_at'])) ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</body>
</html>
