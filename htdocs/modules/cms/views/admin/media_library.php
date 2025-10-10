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
    <nav style="margin-bottom: 18px; font-size: 15px;">
        <a href="/admin/cms/dashboard" style="color: #007cba; text-decoration: none;">Dashboard</a>
        <span style="color: #888;"> &gt; </span>
        <span style="color: #333; font-weight: 500;">Media Library</span>
    </nav>
    <div class="header">
        <h1>Media Library</h1>
        <div>
            <a href="/admin/cms/pages" class="btn btn-secondary">Back to Pages</a>
            <a href="/admin/cms/dashboard" class="btn btn-secondary">Dashboard</a>
        </div>
    </div>

    <div class="upload-area" onclick="document.getElementById('media-upload').click()">
        <h3>📷 Upload New Images or PDFs</h3>
        <p>Click here or drag and drop images or PDFs to upload</p>
        <input type="file" id="media-upload" multiple accept="image/*,application/pdf">
        <div id="upload-progress" style="margin-top:10px; display:none;">
            <div style="background:#eee; border-radius:4px; overflow:hidden; height:18px; width:100%;">
                <div id="progress-bar" style="background:#007cba; height:18px; width:0%; transition:width 0.2s;"></div>
            </div>
            <div id="progress-label" style="font-size:13px; color:#333; margin-top:4px;"></div>
        </div>
    </div>

    <?php if (empty($images)): ?>
        <div class="empty-state">
            <h2>No images uploaded yet</h2>
            <p>Upload your first image using the upload area above.</p>
        </div>
    <?php else: ?>
        <div class="media-grid">
            <?php foreach ($images as $image):
                $ext = strtolower(pathinfo($image['filename'], PATHINFO_EXTENSION));
            ?>
                <div class="media-item" data-filename="<?= htmlspecialchars($image['filename']) ?>">
                    <div class="media-preview">
                        <?php if (in_array($ext, ['jpg','jpeg','png','gif','webp'])): ?>
                            <img src="<?= htmlspecialchars($image['thumbnail']) ?>" alt="<?= htmlspecialchars($image['filename']) ?>">
                        <?php elseif ($ext === 'pdf'): ?>
                            <a href="<?= htmlspecialchars($image['url']) ?>" target="_blank" style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;text-decoration:none;">
                                <span style="font-size:48px;">📄</span>
                            </a>
                        <?php else: ?>
                            <a href="<?= htmlspecialchars($image['url']) ?>" target="_blank" style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;text-decoration:none;">
                                <span style="font-size:36px;">📁</span>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="media-info">
                        <div class="media-filename"><?= htmlspecialchars($image['filename']) ?></div>
                        <div class="media-meta">
                            Size: <?= number_format($image['size'] / 1024, 1) ?> KB<br>
                            Uploaded: <?= htmlspecialchars($image['uploaded']) ?>
                        </div>
                        <div class="media-actions">
                            <button class="btn btn-small" onclick="copyUrl(this, '<?= htmlspecialchars($image['url']) ?>')">Copy URL</button>
                            <a class="btn btn-small btn-info" href="<?= htmlspecialchars($image['url']) ?>" download target="_blank">Download</a>
                            <button class="btn btn-small btn-danger" onclick="deleteImage('<?= htmlspecialchars($image['filename']) ?>')">Delete</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (!empty($images) && isset($page) && isset($totalPages) && $totalPages > 1): ?>
            <div style="text-align:center; margin: 30px 0;">
                <nav aria-label="Media pagination">
                    <ul style="display:inline-flex; list-style:none; padding:0; margin:0; gap:6px;">
                        <?php if ($page > 1): ?>
                            <li><a href="?page=<?= $page - 1 ?>" class="btn btn-small">&laquo; Prev</a></li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li><a href="?page=<?= $i ?>" class="btn btn-small<?= ($i == $page ? ' btn-info' : '') ?>"><?= $i ?></a></li>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages): ?>
                            <li><a href="?page=<?= $page + 1 ?>" class="btn btn-small">Next &raquo;</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
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
            const allowedTypes = [
                'image/jpeg','image/png','image/gif','image/webp','image/svg+xml','application/pdf'
            ];
            if (!allowedTypes.includes(file.type)) {
                alert('Only images and PDFs are allowed.');
                return;
            }
            if (file.size > 10 * 1024 * 1024) {
                alert('File size must be less than 10MB: ' + file.name);
                return;
            }
            const formData = new FormData();
            formData.append('image', file);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/admin/cms/upload/image', true);
            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    document.getElementById('upload-progress').style.display = 'block';
                    document.getElementById('progress-bar').style.width = percent + '%';
                    document.getElementById('progress-label').textContent = 'Uploading ' + file.name + ' (' + percent + '%)';
                }
            };
            xhr.onload = function() {
                document.getElementById('progress-bar').style.width = '0%';
                document.getElementById('progress-label').textContent = '';
                document.getElementById('upload-progress').style.display = 'none';
                if (xhr.status === 200) {
                    let data;
                    try { data = JSON.parse(xhr.responseText); } catch (e) { data = {}; }
                    if (data.location || data.url) {
                        // Add new file to grid dynamically (simple reload for now)
                        window.location.reload();
                    } else if (data.error) {
                        alert('Upload failed: ' + data.error);
                    } else {
                        alert('Upload failed: Unknown error.');
                    }
                } else {
                    alert('Upload failed: Server error.');
                }
            };
            xhr.onerror = function() {
                document.getElementById('upload-progress').style.display = 'none';
                alert('Upload failed: Network error.');
            };
            xhr.send(formData);
        }

        function copyUrl(btn, url) {
            const fullUrl = window.location.origin + url;
            navigator.clipboard.writeText(fullUrl).then(function() {
                btn.textContent = 'Copied!';
                setTimeout(() => {
                    btn.textContent = 'Copy URL';
                }, 1000);
            }).catch(function() {
                // Fallback for older browsers
                const textarea = document.createElement('textarea');
                textarea.value = fullUrl;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                btn.textContent = 'Copied!';
                setTimeout(() => {
                    btn.textContent = 'Copy URL';
                }, 1000);
            });
        }

        function deleteImage(filename) {
            if (confirm('Are you sure you want to delete this file? This action cannot be undone.')) {
                fetch('/admin/cms/media/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'filename=' + encodeURIComponent(filename)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the media item from the grid
                        const item = document.querySelector('.media-item[data-filename="' + filename.replace(/"/g, '\\"') + '"]');
                        if (item) item.remove();
                    } else {
                        alert('Delete failed: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(() => {
                    alert('Delete failed: Network error');
                });
            }
        }
    </script>
</body>
</html>