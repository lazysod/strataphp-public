<?php
/**
 * File Browser for TinyMCE
 * 
 * Simple file browser interface for uploaded images
 */

// Prevent direct access
if (!defined('STRPHP_ROOT')) {
    define('STRPHP_ROOT', dirname(__DIR__, 2));
}

require_once STRPHP_ROOT . '/htdocs/app/start.php';

// Check if user is authenticated admin
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    http_response_code(403);
    echo 'Unauthorized access';
    exit;
}

// Get upload directory
$uploadDir = STRPHP_ROOT . '/htdocs/storage/uploads/cms/images';
$baseUrl = '/storage/uploads/cms/images';

// Get files recursively
function getUploadedFiles($dir, $baseUrl, $basePath) {
    $files = [];
    
    if (!is_dir($dir)) {
        return $files;
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $extension = strtolower($file->getExtension());
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $relativePath = str_replace($basePath, '', $file->getPathname());
                $files[] = [
                    'name' => $file->getFilename(),
                    'path' => $file->getPathname(),
                    'url' => $baseUrl . $relativePath,
                    'size' => $file->getSize(),
                    'date' => $file->getMTime(),
                    'extension' => $extension
                ];
            }
        }
    }
    
    // Sort by date, newest first
    usort($files, function($a, $b) {
        return $b['date'] - $a['date'];
    });
    
    return $files;
}

$files = getUploadedFiles($uploadDir, $baseUrl, $uploadDir);

// Format file sizes
function formatFileSize($bytes) {
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Browser</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: #3498db;
            color: white;
            padding: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
        }
        .files-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .file-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .file-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .file-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            background: #f8f9fa;
        }
        .file-info {
            padding: 10px;
        }
        .file-name {
            font-weight: 600;
            margin-bottom: 5px;
            word-break: break-word;
            font-size: 14px;
        }
        .file-meta {
            font-size: 12px;
            color: #666;
            display: flex;
            justify-content: space-between;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .empty-state img {
            width: 64px;
            height: 64px;
            opacity: 0.3;
            margin-bottom: 20px;
        }
        .selected {
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        .actions {
            padding: 20px;
            border-top: 1px solid #ddd;
            background: #f8f9fa;
            text-align: right;
        }
        .btn {
            padding: 10px 20px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }
        .btn:hover {
            background: #2980b9;
        }
        .btn-secondary {
            background: #95a5a6;
        }
        .btn-secondary:hover {
            background: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📁 Image Browser</h1>
        </div>
        
        <?php if (empty($files)): ?>
            <div class="empty-state">
                <div style="font-size: 48px; margin-bottom: 20px;">📷</div>
                <h3>No images uploaded yet</h3>
                <p>Upload images through the content editor to see them here.</p>
            </div>
        <?php else: ?>
            <div class="files-grid">
                <?php foreach ($files as $file): ?>
                    <div class="file-item" onclick="selectFile('<?= htmlspecialchars($file['url']) ?>', '<?= htmlspecialchars($file['name']) ?>')">
                        <img src="<?= htmlspecialchars($file['url']) ?>" alt="<?= htmlspecialchars($file['name']) ?>" class="file-image" loading="lazy">
                        <div class="file-info">
                            <div class="file-name"><?= htmlspecialchars($file['name']) ?></div>
                            <div class="file-meta">
                                <span><?= formatFileSize($file['size']) ?></span>
                                <span><?= date('M j, Y', $file['date']) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="actions">
            <button type="button" class="btn btn-secondary" onclick="window.close()">Cancel</button>
            <button type="button" class="btn" onclick="insertSelected()" id="insertBtn" disabled>Insert Image</button>
        </div>
    </div>

    <script>
        let selectedFile = null;
        let selectedElement = null;

        function selectFile(url, name) {
            selectedFile = { url: url, name: name };
            
            // Remove previous selection
            if (selectedElement) {
                selectedElement.classList.remove('selected');
            }
            
            // Add selection to clicked element
            selectedElement = event.currentTarget;
            selectedElement.classList.add('selected');
            
            // Enable insert button
            document.getElementById('insertBtn').disabled = false;
        }

        function insertSelected() {
            if (!selectedFile) return;
            
            // Check if we're in a popup (TinyMCE file picker)
            if (window.opener && window.opener.tinymce) {
                // TinyMCE callback
                if (window.filePickerCallback) {
                    window.filePickerCallback(selectedFile.url, { title: selectedFile.name });
                }
                window.close();
            } else {
                // Copy URL to clipboard as fallback
                navigator.clipboard.writeText(selectedFile.url).then(function() {
                    alert('Image URL copied to clipboard: ' + selectedFile.url);
                });
            }
        }

        // Handle double-click for quick selection
        document.addEventListener('dblclick', function(e) {
            if (e.target.closest('.file-item')) {
                insertSelected();
            }
        });
    </script>
</body>
</html>