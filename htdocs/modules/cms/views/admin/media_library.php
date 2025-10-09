<?php
// CMS Media Library Template
if (!defined('STRPHP_ROOT')) {
    exit('Direct access not allowed');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Library - CMS Admin</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007cba;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background: #005a87;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #545b62;
        }
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .media-item {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .media-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .media-preview {
            width: 100%;
            height: 200px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .media-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
        .media-info {
            padding: 15px;
        }
        .media-filename {
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
            word-break: break-word;
        }
        .media-meta {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
        }
        .media-actions {
            display: flex;
            gap: 10px;
        }
        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .empty-state h2 {
            color: #666;
            margin-bottom: 10px;
        }
        .empty-state p {
            color: #999;
            margin-bottom: 20px;
        }
        .upload-area {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            margin-bottom: 30px;
            background: white;
            transition: border-color 0.3s;
        }
        .upload-area:hover {
            border-color: #007cba;
        }
        .upload-area.dragover {
            border-color: #007cba;
            background: #f0f8ff;
        }
        #media-upload {
            display: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Media Library</h1>
        <div>
            <a href="/admin/cms/pages" class="btn btn-secondary">Back to Pages</a>
            <a href="/admin/cms/dashboard" class="btn btn-secondary">Dashboard</a>
        </div>
    </div>

    <div class="upload-area" onclick="document.getElementById('media-upload').click()">
        <h3>📷 Upload New Images</h3>
        <p>Click here or drag and drop images to upload</p>
        <input type="file" id="media-upload" multiple accept="image/*">
    </div>

    <?php if (empty($images)): ?>
        <div class="empty-state">
            <h2>No images uploaded yet</h2>
            <p>Upload your first image using the upload area above.</p>
        </div>
    <?php else: ?>
        <div class="media-grid">
            <?php foreach ($images as $image): ?>
                <div class="media-item" data-filename="<?= htmlspecialchars($image['filename']) ?>">
                    <div class="media-preview">
                        <img src="<?= htmlspecialchars($image['thumbnail']) ?>" alt="<?= htmlspecialchars($image['filename']) ?>">
                    </div>
                    <div class="media-info">
                        <div class="media-filename"><?= htmlspecialchars($image['filename']) ?></div>
                        <div class="media-meta">
                            Size: <?= number_format($image['size'] / 1024, 1) ?> KB<br>
                            Uploaded: <?= htmlspecialchars($image['uploaded']) ?>
                        </div>
                        <div class="media-actions">
                            <button class="btn btn-small" onclick="copyUrl('<?= htmlspecialchars($image['url']) ?>')">Copy URL</button>
                            <button class="btn btn-small btn-danger" onclick="deleteImage('<?= htmlspecialchars($image['filename']) ?>')">Delete</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <script>
        // File upload handling
        const uploadInput = document.getElementById('media-upload');
        const uploadArea = document.querySelector('.upload-area');

        uploadInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            files.forEach(file => uploadFile(file));
        });

        // Drag and drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));
            files.forEach(file => uploadFile(file));
        });

        function uploadFile(file) {
            if (!file.type.startsWith('image/')) {
                alert('Please select image files only.');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB: ' + file.name);
                return;
            }

            const formData = new FormData();
            formData.append('image', file);

            fetch('/admin/cms/upload/image', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to show new image
                    window.location.reload();
                } else {
                    alert('Upload failed: ' + data.error);
                }
            })
            .catch(error => {
                alert('Upload failed: ' + error.message);
                console.error('Upload error:', error);
            });
        }

        function copyUrl(url) {
            // Create full URL
            const fullUrl = window.location.origin + url;
            
            // Copy to clipboard
            navigator.clipboard.writeText(fullUrl).then(function() {
                // Provide feedback
                const originalText = event.target.textContent;
                event.target.textContent = 'Copied!';
                setTimeout(() => {
                    event.target.textContent = originalText;
                }, 1000);
            }).catch(function(err) {
                // Fallback for older browsers
                const textarea = document.createElement('textarea');
                textarea.value = fullUrl;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                
                const originalText = event.target.textContent;
                event.target.textContent = 'Copied!';
                setTimeout(() => {
                    event.target.textContent = originalText;
                }, 1000);
            });
        }

        function deleteImage(filename) {
            if (confirm('Are you sure you want to delete this image? This action cannot be undone.')) {
                // TODO: Implement delete functionality
                alert('Delete functionality will be implemented in the next update.');
            }
        }
    </script>
</body>
</html>