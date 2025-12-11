<?php global $config; error_log('TINYMCE_API_KEY from config: ' . ($config['tinymceApiKey'] ?? '')); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add/Edit Blog Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="/modules/cms/assets/css/styles.css">
    <!-- TinyMCE -->
    <?php
    // Try to load TinyMCE API key from environment, then config
    $tinymceApiKey = $_ENV['TINYMCE_API_KEY'] ?? getenv('TINYMCE_API_KEY') ?? ($config['tinymceApiKey'] ?? '');
    ?>
    <?php if ($tinymceApiKey): ?>
        <script src="https://cdn.tiny.cloud/1/<?= htmlspecialchars($tinymceApiKey) ?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <?php else: ?>
        <div class="alert alert-warning">TinyMCE API key is not set. Please set <code>TINYMCE_API_KEY</code> in your .env or config.</div>
    <?php endif; ?>

    <script>
    tinymce.init({
        selector: '#content',
        plugins: 'image link media code',
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright | outdent indent | link image media | code',
        file_picker_callback: function(callback, value, meta) {
            // Open media manager here
            window.open('/admin/media/media-library?field=' + meta.fieldname, 'MediaManager', 'width=900,height=600');
            window.tinymceFilePickerCallback = callback;
        }
    });
    </script>
</head>
<body>
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-edit"></i> Add/Edit Blog Post</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required value="<?= htmlspecialchars($post['title'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Content</label>
                    <textarea class="form-control" id="content" name="content" rows="10" required><?= htmlspecialchars($post['content'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="featured_image" class="form-label">Featured Image</label>
                    <input type="text" class="form-control" id="featured_image" name="featured_image" value="<?= htmlspecialchars($post['featured_image'] ?? '') ?>">
                    <button type="button" class="btn btn-secondary mt-2" onclick="window.open('/admin/media/media-library?field=featured_image', 'MediaManager', 'width=900,height=600');">Select Image</button>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Save Post</button>
                <a href="/admin/blog" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
