<?php
// Enhanced CMS Page Form Template with Tabbed Interface
if (!defined('STRPHP_ROOT')) {
    exit('Direct access not allowed');
}

$isEdit = isset($page) && $page;
$pageTitle = $isEdit ? 'Edit Page' : 'Create New Page';
$formAction = $isEdit ? "/admin/cms/pages/{$page['id']}/edit" : "/admin/cms/pages/create";

// Check for session messages
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : null;
$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : null;
if (isset($_SESSION['success'])) unset($_SESSION['success']);
if (isset($_SESSION['error'])) unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?= htmlspecialchars($pageTitle) ?> - v<?= time() ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    /* Fallback for Bootstrap float and utility classes */
    .float-start { float: left !important; }
    .float-end { float: right !important; }
    .mx-auto { margin-left: auto !important; margin-right: auto !important; }
    .d-block { display: block !important; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #3498db;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
        }
        .breadcrumb {
            margin-bottom: 20px;
            font-size: 14px;
            color: #666;
        }
        .breadcrumb a {
            color: #3498db;
            text-decoration: none;
        }
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
        }
        .btn:hover {
            background: #2980b9;
        }
        .btn-success {
            background: #27ae60;
        }
        .btn-success:hover {
            background: #229954;
        }
        .btn-secondary {
            background: #95a5a6;
        }
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        .btn-outline {
            background: transparent;
            border: 1px solid #3498db;
            color: #3498db;
        }
        .btn-outline:hover {
            background: #3498db;
            color: white;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        
        /* Tab Navigation */
        .nav-tabs {
            list-style: none;
            padding: 0;
            margin: 0 0 20px 0;
            border-bottom: 1px solid #ddd;
            display: flex;
        }
        .nav-tabs li {
            margin-right: 5px;
        }
        .nav-tabs button {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-bottom: none;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 4px 4px 0 0;
            font-size: 14px;
            color: #666;
            transition: all 0.2s;
        }
        .nav-tabs button:hover {
            background: #e9ecef;
            color: #333;
        }
        .nav-tabs button.active {
            background: white;
            color: #333;
            border-bottom: 1px solid white;
            margin-bottom: -1px;
            font-weight: 600;
        }
        .nav-tabs .fas {
            margin-right: 8px;
        }
        
        /* Tab Content */
        .tab-content {
            min-height: 400px;
        }
        .tab-pane {
            display: none;
            animation: fadeIn 0.3s ease-in;
        }
        .tab-pane.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Form Styling */
        .row {
            display: flex;
            margin: 0 -15px;
        }
        .col-md-8 {
            flex: 0 0 66.666667%;
            padding: 0 15px;
        }
        .col-md-6 {
            flex: 0 0 50%;
            padding: 0 15px;
        }
        .col-md-4 {
            flex: 0 0 33.333333%;
            padding: 0 15px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #2c3e50;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group textarea {
            resize: vertical;
            font-family: inherit;
        }
        .form-text {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 12px;
        }
        .form-check {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }
        .form-check input {
            width: auto;
            margin-right: 8px;
        }
        .required {
            color: #e74c3c;
        }
        
        /* Card Components */
        .card {
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .card-header {
            background: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            border-radius: 4px 4px 0 0;
        }
        .card-header h6 {
            margin: 0;
            font-weight: 600;
            color: #2c3e50;
        }
        .card-body {
            padding: 20px;
        }
        .card.bg-light {
            background: #f8f9fa;
        }
        
        /* Image Upload Styles */
        .image-upload-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .upload-controls {
            display: flex;
            gap: 10px;
        }
        .upload-controls button {
            background: #007cba;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .upload-controls button:hover {
            background: #005a87;
        }
        .upload-controls button:last-child {
            background: #dc3545;
        }
        .upload-controls button:last-child:hover {
            background: #c82333;
        }
        .image-preview {
            margin-top: 10px;
            max-width: 300px;
        }
        .image-preview img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .upload-progress {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }
        
        /* Form Actions */
        .form-actions {
            border-top: 1px solid #ddd;
            padding-top: 20px;
            margin-top: 30px;
        }
        
        /* Input Group */
        .input-group {
            display: flex;
        }
        .input-group input {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
        .input-group .btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            margin-right: 0;
        }
        
        /* Character Counter Colors */
        .text-warning {
            color: #f39c12 !important;
        }
        .text-danger {
            color: #e74c3c !important;
        }
        .text-muted {
            color: #666 !important;
        }
        
        /* Rich Text Editor Styles */
        .rich-editor-container {
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        
        .rich-editor-toolbar {
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
            padding: 8px;
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }
        
        .rich-editor-toolbar button {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 3px;
            padding: 6px 10px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            color: #333;
            transition: all 0.2s;
            min-width: 30px;
        }
        
        .rich-editor-toolbar button:hover {
            background: #e9ecef;
            border-color: #aaa;
        }
        
        .rich-editor-toolbar button:active {
            background: #dee2e6;
            transform: translateY(1px);
        }
        
        .rich-editor-content {
            min-height: 300px;
            max-height: 500px;
            border: none !important;
            box-sizing: border-box;
            cursor: text;
        }
        
        .rich-editor-content:focus {
            outline: 2px solid #3498db;
            outline-offset: -2px;
        }
        
        .rich-editor-content p {
            margin: 0 0 1em 0;
        }
        
        .rich-editor-content h2 {
            margin: 1em 0 0.5em 0;
            font-size: 1.5em;
            font-weight: bold;
        }
        
        .rich-editor-content h3 {
            margin: 1em 0 0.5em 0;
            font-size: 1.2em;
            font-weight: bold;
        }
        
        .rich-editor-content ul, .rich-editor-content ol {
            margin: 0.5em 0;
            padding-left: 2em;
        }
        
        .rich-editor-content a {
            color: #3498db;
            text-decoration: underline;
        }
        
        /* Drag and drop visual feedback */
        .rich-editor-content.drag-over {
            border: 2px dashed #007bff !important;
            background-color: #f8f9fa !important;
            position: relative;
        }

        .rich-editor-content.drag-over::after {
            content: "📷 Drop images here to upload";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 123, 255, 0.95);
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            pointer-events: none;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .source-textarea {
            resize: vertical;
            background: #f8f9fa;
            color: #333;
            border: none !important;
        }

        .editor-image-wrapper.selected {
            outline: 3px solid #3498db !important;
            box-shadow: 0 0 0 4px rgba(52,152,219,0.15);
            position: relative;
        }
        .resize-handle {
            user-select: none;
            transition: background 0.2s;
        }
        .resize-handle:hover {
            background: #217dbb;
        }
        .editor-image-wrapper {
            max-width: 100%;
            box-sizing: border-box;
            clear: both;
            transition: outline 0.2s, box-shadow 0.2s;
        }
        .editor-image-wrapper img {
            max-width: 100%;
            height: auto;
            cursor: pointer;
        }

        /* Floating image toolbar */
        #image-float-toolbar {
            position: absolute;
            display: none;
            z-index: 9999;
            background: #fff;
            border: 1px solid #3498db;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(52,152,219,0.15);
            padding: 4px 8px;
            gap: 4px;
            align-items: center;
            transition: opacity 0.15s;
            font-size: 15px;
            user-select: none;
        }
        #image-float-toolbar .img-toolbar-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            font-size: 15px;
            transition: color 0.2s;
        }
        #image-float-toolbar .img-toolbar-btn:hover {
            color: #3498db;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="breadcrumb">
            <a href="/admin">Admin</a> > <a href="/admin/cms">CMS</a> > <a href="/admin/cms/pages">Pages</a> > <?= $pageTitle ?>
        </div>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <div class="header">
            <h1><?= htmlspecialchars($pageTitle) ?> <small style="color: #27ae60; font-size: 14px;">[Enhanced v2.0 - <?= date('H:i:s') ?>]</small></h1>
            <a href="/admin/cms/pages" class="btn btn-secondary">← Back to Pages</a>
        </div>

        <!-- Tab Navigation -->
        <ul class="nav-tabs" id="pageFormTabs">
            <li>
                <button type="button" class="tab-button active" data-target="content-pane">
                    <i class="fas fa-file-alt"></i> Content
                </button>
            </li>
            <li>
                <button type="button" class="tab-button" data-target="seo-pane">
                    <i class="fas fa-search"></i> SEO & Social
                </button>
            </li>
            <li>
                <button type="button" class="tab-button" data-target="settings-pane">
                    <i class="fas fa-cog"></i> Settings
                </button>
            </li>
        </ul>

        <form method="POST" action="<?= $formAction ?>" id="pageForm">
            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Content Tab -->
                <div class="tab-pane active" id="content-pane">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="title">Title <span class="required">*</span></label>
                                <input type="text" id="title" name="title" 
                                       value="<?= isset($page) ? htmlspecialchars($page['title']) : '' ?>" 
                                       required maxlength="255">
                            </div>

                            <div class="form-group">
                                <label for="slug">Slug <span class="required">*</span></label>
                                <input type="text" id="slug" name="slug" 
                                       value="<?= isset($page) ? htmlspecialchars($page['slug']) : '' ?>" 
                                       required maxlength="255">
                                <div class="form-text">URL-friendly version of the title</div>
                            </div>

                            <div class="form-group">
                                <label for="excerpt">Excerpt</label>
                                <textarea id="excerpt" name="excerpt" rows="3" maxlength="500"><?= isset($page) ? htmlspecialchars($page['excerpt']) : '' ?></textarea>
                                <div class="form-text">Brief description of the page content</div>
                            </div>

                            <div class="form-group">
                                <label for="content">Content</label>
                                <textarea id="content" name="content" rows="15"><?= isset($page) ? htmlspecialchars($page['content']) : '' ?></textarea>
                                <button type="button" class="btn btn-outline-info mt-2" id="openMediaManagerBtn" style="margin-bottom:6px;">
                                    <i class="fas fa-photo-video"></i> Open Media Manager
                                </button>
                                <div class="form-text">Browse and select media to insert directly into your content.</div>
                                <!-- Modal for Media Manager -->
                                <div id="mediaManagerModal" style="display:none;position:fixed;z-index:9999;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;flex-direction:row;">
                                    <div style="background:#fff;max-width:900px;width:90vw;height:80vh;overflow:auto;position:relative;border-radius:8px;box-shadow:0 4px 32px rgba(0,0,0,0.2);">
                                        <button type="button" id="closeMediaManagerModal" style="position:absolute;top:10px;right:10px;font-size:1.5rem;background:none;border:none;">&times;</button>
                                        <iframe src="/admin/cms/media?embed=1" style="width:100%;height:75vh;border:none;border-radius:8px;"></iframe>
                                    </div>
                                </div>
                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    // Modal open/close logic
                                    var mediaModal = document.getElementById('mediaManagerModal');
                                    var openBtn = document.getElementById('openMediaManagerBtn');
                                    var closeBtn = document.getElementById('closeMediaManagerModal');
                                    if (openBtn && mediaModal) {
                                        openBtn.onclick = function() {
                                            mediaModal.style.display = 'flex';
                                            mediaModal.style.alignItems = 'center';
                                            mediaModal.style.justifyContent = 'center';
                                            mediaModal.style.flexDirection = 'row';
                                        };
                                    }
                                    if (closeBtn && mediaModal) {
                                        closeBtn.onclick = function() {
                                            mediaModal.style.display = 'none';
                                        };
                                    }
                                    // Listen for messages from iframe (media selection)
                                    window.addEventListener('message', function(event) {
                                        if (event.origin !== window.location.origin) return;
                                        if (event.data && event.data.mediaUrl) {
                                            var url = event.data.mediaUrl;
                                            var tag = url.match(/\.(jpg|jpeg|png|gif|webp|svg)$/i) ? '<img src="'+url+'" alt="" />' : url;
                                            // Insert into rich text editor at cursor
                                            var editor = document.querySelector('.rich-editor-content');
                                            if (editor && editor.isContentEditable) {
                                                // Insert HTML at cursor
                                                var sel = window.getSelection();
                                                if (sel && sel.rangeCount > 0 && editor.contains(sel.anchorNode)) {
                                                    var range = sel.getRangeAt(0);
                                                    var el = document.createElement('span');
                                                    el.innerHTML = tag;
                                                    var frag = document.createDocumentFragment(), node, lastNode;
                                                    while ((node = el.firstChild)) {
                                                        lastNode = frag.appendChild(node);
                                                    }
                                                    range.deleteContents();
                                                    range.insertNode(frag);
                                                    // Move cursor after inserted node
                                                    if (lastNode) {
                                                        range.setStartAfter(lastNode);
                                                        range.collapse(true);
                                                        sel.removeAllRanges();
                                                        sel.addRange(range);
                                                    }
                                                } else {
                                                    // Fallback: append to end
                                                    editor.innerHTML += tag;
                                                }
                                                // Sync textarea
                                                var textarea = document.getElementById('content');
                                                if (textarea) textarea.value = editor.innerHTML;
                                            } else {
                                                // Fallback: insert into textarea
                                                var textarea = document.getElementById('content');
                                                if (textarea) {
                                                    var start = textarea.selectionStart, end = textarea.selectionEnd;
                                                    var before = textarea.value.substring(0, start), after = textarea.value.substring(end);
                                                    textarea.value = before + tag + after;
                                                    textarea.selectionStart = textarea.selectionEnd = before.length + tag.length;
                                                    textarea.focus();
                                                }
                                            }
                                            mediaModal.style.display = 'none';
                                        }
                                    });
                                });
                                </script>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6>Publishing</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select id="status" name="status">
                                            <option value="draft" <?= (isset($page) && $page['status'] === 'draft') ? 'selected' : '' ?>>Draft</option>
                                            <option value="published" <?= (isset($page) && $page['status'] === 'published') ? 'selected' : '' ?>>Published</option>
                                            <option value="private" <?= (isset($page) && $page['status'] === 'private') ? 'selected' : '' ?>>Private</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="site_id">Site <span class="required">*</span></label>
                                        <select id="site_id" name="site_id" required>
                                            <option value="">-- Select Site --</option>
                                            <?php if (isset($sites) && is_array($sites)): ?>
                                                <?php foreach ($sites as $site): ?>
                                                    <option value="<?= htmlspecialchars($site['id']) ?>" <?= (isset($page) && isset($page['site_id']) && $page['site_id'] == $site['id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($site['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <div class="form-text">Assign this page to a site.</div>
                                    </div>

                                    <div class="form-group">
                                        <label for="template">Template</label>
                                        <select id="template" name="template">
                                            <option value="default" <?= (isset($page) && $page['template'] === 'default') ? 'selected' : '' ?>>Default</option>
                                            <option value="home" <?= (isset($page) && $page['template'] === 'home') ? 'selected' : '' ?>>Home</option>
                                            <option value="about" <?= (isset($page) && $page['template'] === 'about') ? 'selected' : '' ?>>About</option>
                                            <option value="contact" <?= (isset($page) && $page['template'] === 'contact') ? 'selected' : '' ?>>Contact</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="parent_id">Parent Page</label>
                                        <select id="parent_id" name="parent_id">
                                            <option value="">-- None (Top Level) --</option>
                                            <?php
                                            // Helper to build a flat list with indentation for hierarchy
                                            function renderParentOptions($pages, $currentId = null, $parentId = null, $level = 0, $excludeIds = []) {
                                                foreach ($pages as $p) {
                                                    if ($p['id'] == $currentId || in_array($p['id'], $excludeIds)) continue;
                                                    if ($p['parent_id'] != $parentId) continue;
                                                    $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $level);
                                                    $selected = (isset($page['parent_id']) && $page['parent_id'] == $p['id']) ? 'selected' : '';
                                                    echo '<option value="' . htmlspecialchars($p['id']) . '" ' . $selected . '>' . $indent . htmlspecialchars($p['title']) . '</option>';
                                                    // Prevent circular reference by adding this id to excludeIds
                                                    renderParentOptions($pages, $currentId, $p['id'], $level + 1, array_merge($excludeIds, [$currentId]));
                                                }
                                            }
                                            if (isset($allPages)) {
                                                renderParentOptions($allPages, isset($page['id']) ? $page['id'] : null);
                                            }
                                            ?>
                                        </select>
                                        <div class="form-text">Select a parent page to nest this page under another. Leave blank for top-level.</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="menu_order">Menu Order</label>
                                        <input type="number" id="menu_order" name="menu_order" 
                                               value="<?= isset($page) ? htmlspecialchars($page['menu_order']) : '0' ?>" 
                                               min="0" max="999">
                                        <div class="form-text">Order in navigation menu (0 = hidden)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO & Social Tab -->
                <div class="tab-pane" id="seo-pane">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Search Engine Optimization</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="meta_title">Meta Title</label>
                                        <input type="text" id="meta_title" name="meta_title" 
                                               value="<?= isset($page) ? htmlspecialchars($page['meta_title'] ?? '') : '' ?>" 
                                               maxlength="60">
                                        <div class="form-text">Recommended: 50-60 characters</div>
                                    </div>

                                    <div class="form-group">
                                        <label for="meta_description">Meta Description</label>
                                        <textarea id="meta_description" name="meta_description" 
                                                  rows="3" maxlength="160"><?= isset($page) ? htmlspecialchars($page['meta_description'] ?? '') : '' ?></textarea>
                                        <div class="form-text">Recommended: 150-160 characters</div>
                                    </div>

                                    <div class="form-group">
                                        <label for="canonical_url">Canonical URL</label>
                                        <input type="url" id="canonical_url" name="canonical_url" 
                                               value="<?= isset($page) ? htmlspecialchars($page['canonical_url'] ?? '') : '' ?>" 
                                               placeholder="https://example.com/page">
                                        <div class="form-text">Leave empty to use page URL</div>
                                    </div>

                                    <div class="form-check">
                                        <input type="checkbox" id="noindex" name="noindex" value="1"
                                               <?= (isset($page) && ($page['noindex'] ?? 0)) ? 'checked' : '' ?>>
                                        <label for="noindex">No Index (hide from search engines)</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Social Media</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="og_type">Open Graph Type</label>
                                        <select id="og_type" name="og_type">
                                            <option value="">Select type...</option>
                                            <option value="website" <?= (isset($page) && ($page['og_type'] ?? '') === 'website') ? 'selected' : '' ?>>Website</option>
                                            <option value="article" <?= (isset($page) && ($page['og_type'] ?? '') === 'article') ? 'selected' : '' ?>>Article</option>
                                            <option value="product" <?= (isset($page) && ($page['og_type'] ?? '') === 'product') ? 'selected' : '' ?>>Product</option>
                                            <option value="profile" <?= (isset($page) && ($page['og_type'] ?? '') === 'profile') ? 'selected' : '' ?>>Profile</option>
                                        </select>
                                        <div class="form-text">For Facebook, LinkedIn sharing</div>
                                    </div>

                                    <div class="form-group">
                                        <label for="og_image">Open Graph Image</label>
                                        <div class="image-upload-container">
                                            <input type="url" id="og_image" name="og_image" 
                                                   value="<?= isset($page) ? htmlspecialchars($page['og_image'] ?? '') : '' ?>" 
                                                   placeholder="https://example.com/image.jpg">
                                            <div class="upload-controls">
                                                <input type="file" id="og_image_file" accept="image/*" style="display: none;">
                                                <button type="button" onclick="uploadOgImageButtonClick(); return false;">Upload Image</button>
                                                <button type="button" onclick="clearOgImage()">Clear</button>
                                            </div>
                                            <div id="og_image_preview" class="image-preview">
                                                <?php if (!empty($page['og_image'])): ?>
                                                <img src="<?= htmlspecialchars($page['og_image']) ?>" alt="OG Image Preview">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="form-text">Recommended: 1200x630px. You can upload an image or enter a URL manually.</div>
                                    </div>

                                    <div class="form-group">
                                        <label for="twitter_card">Twitter Card Type</label>
                                        <select id="twitter_card" name="twitter_card">
                                            <option value="">Select type...</option>
                                            <option value="summary" <?= (isset($page) && ($page['twitter_card'] ?? '') === 'summary') ? 'selected' : '' ?>>Summary</option>
                                            <option value="summary_large_image" <?= (isset($page) && ($page['twitter_card'] ?? '') === 'summary_large_image') ? 'selected' : '' ?>>Summary Large Image</option>
                                            <option value="app" <?= (isset($page) && ($page['twitter_card'] ?? '') === 'app') ? 'selected' : '' ?>>App</option>
                                            <option value="player" <?= (isset($page) && ($page['twitter_card'] ?? '') === 'player') ? 'selected' : '' ?>>Player</option>
                                        </select>
                                        <div class="form-text">For Twitter sharing</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Tab -->
                <div class="tab-pane" id="settings-pane">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Advanced Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Page ID</label>
                                        <input type="text" value="<?= isset($page) ? $page['id'] : 'Auto-generated' ?>" readonly>
                                        <div class="form-text">Unique identifier for this page</div>
                                    </div>

                                    <div class="form-group">
                                        <label>Created</label>
                                        <input type="text" 
                                               value="<?= isset($page) ? date('M j, Y g:i A', strtotime($page['created_at'])) : 'Not yet created' ?>" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label>Last Modified</label>
                                        <input type="text" 
                                               value="<?= isset($page) ? date('M j, Y g:i A', strtotime($page['updated_at'])) : 'Not yet created' ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Preview</h6>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($page)): ?>
                                        <div class="form-group">
                                            <label>Page URL</label>
                                            <div class="input-group">
                                                <input type="text" value="/<?= htmlspecialchars($page['slug']) ?>" readonly>
                                                <a href="/<?= htmlspecialchars($page['slug']) ?>" target="_blank" class="btn btn-outline">View</a>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <p style="color: #666;">Save the page to see preview options</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">
                    <?= $isEdit ? 'Update Page' : 'Create Page' ?>
                </button>
                <a href="/admin/cms/pages" class="btn btn-secondary">Cancel</a>
                <?php if (isset($page)): ?>
                    <a href="/<?= htmlspecialchars($page['slug']) ?>" target="_blank" class="btn btn-outline" style="float: right;">Preview Page</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Simple Rich Text Editor (Self-hosted) -->
    <script>
        console.log('=== PAGE FORM DEBUG ===');
        console.log('Page loaded at:', new Date());
        
        // Alignment logic for selected image
        function alignSelectedImage(alignment) {
            if (!window.richEditor) return;
            let selected = document.querySelector('.editor-image-wrapper.selected');
            if (!selected) return;
            const img = selected.querySelector('img');
            if (!img) return;
            // Remove all Bootstrap alignment classes and reset display
            img.classList.remove('float-start', 'float-end', 'mx-auto', 'd-block');
            selected.classList.remove('text-center', 'text-start', 'text-end');
            // For left/right, REMOVE wrapper and float the image directly
            if (alignment === 'left' || alignment === 'right') {
                // If image is wrapped, unwrap it
                if (selected && selected.classList.contains('editor-image-wrapper')) {
                    selected.replaceWith(img);
                }
                // Remove all float/alignment classes from image
                img.classList.remove('float-start', 'float-end', 'mx-auto', 'd-block');
                // Apply float class
                if (alignment === 'left') {
                    img.classList.add('float-start');
                } else {
                    img.classList.add('float-end');
                }
                // Place image directly in the parent element (no wrapper)
                // No wrapper for left/right
            } else if (alignment === 'center') {
                if (selected.tagName !== 'DIV') {
                    const div = document.createElement('div');
                    div.className = selected.className;
                    div.classList.add('editor-image-wrapper');
                    div.contentEditable = 'false';
                    div.appendChild(img);
                    selected.replaceWith(div);
                    selected = div;
                }
                selected.style.display = 'block';
            } else {
                // For inline, use span
                if (selected.tagName !== 'SPAN') {
                    const span = document.createElement('span');
                    span.className = selected.className;
                    span.classList.add('editor-image-wrapper');
                    span.contentEditable = 'false';
                    span.appendChild(img);
                    selected.replaceWith(span);
                    selected = span;
                }
                selected.style.display = '';
            }
            // Remove all float and alignment classes from image
            img.classList.remove('float-start', 'float-end', 'mx-auto', 'd-block');
            // Apply new alignment
            switch (alignment) {
                case 'left':
                    selected.classList.remove('text-center', 'text-end');
                    selected.classList.add('text-start');
                    img.classList.add('float-start');
                    break;
                case 'center':
                    selected.classList.remove('text-start', 'text-end');
                    selected.classList.add('text-center');
                    img.classList.add('mx-auto', 'd-block');
                    break;
                case 'right':
                    selected.classList.remove('text-center', 'text-start');
                    selected.classList.add('text-end');
                    img.classList.add('float-end');
                    break;
                default:
                    // inline, no extra classes
                    break;
            }
            window.richEditor.textarea.value = window.richEditor.element.innerHTML;
        }

        // Simple Rich Text Editor Implementation
        function initializeRichTextEditor() {
            const textarea = document.getElementById('content');
            if (!textarea) {
                console.error('Content textarea not found');
                return;
            }
            
            // Create editor container
            const editorContainer = document.createElement('div');
            editorContainer.className = 'rich-editor-container';
            
            // Create toolbar
            const toolbar = document.createElement('div');
            toolbar.className = 'rich-editor-toolbar';
            

            // Add Media Manager button to the toolbar
            const toolbarHtml = `
                <button type="button" data-cmd="bold" title="Bold"><b>B</b></button>
                <button type="button" data-cmd="italic" title="Italic"><i>I</i></button>
                <button type="button" data-cmd="underline" title="Underline"><u>U</u></button>
                <button type="button" data-cmd="formatBlock" data-value="h2" title="Heading 2">H2</button>
                <button type="button" data-cmd="formatBlock" data-value="h3" title="Heading 3">H3</button>
                <button type="button" data-cmd="formatBlock" data-value="p" title="Paragraph">P</button>
                <button type="button" data-cmd="insertUnorderedList" title="Bullet List">• List</button>
                <button type="button" data-cmd="insertOrderedList" title="Numbered List">1. List</button>
                <button type="button" data-cmd="createLink" title="Link">🔗</button>
                <button type="button" id="insertImageBtn" title="Insert Image">📷</button>
                <button type="button" id="mediaManagerBtn" title="Open Media Manager">🖼️</button>
                <button type="button" id="alignLeftBtn" title="Align Left">⬅️</button>
                <button type="button" id="alignCenterBtn" title="Align Center">↔️</button>
                <button type="button" id="alignRightBtn" title="Align Right">➡️</button>
                <button type="button" data-cmd="removeFormat" title="Clear Format">Clear</button>
                <button type="button" id="toggleSourceBtn" title="HTML Source">&lt;&gt;</button>
            `;
            toolbar.innerHTML = toolbarHtml;

            // Add event listener for Media Manager button
            toolbar.querySelector('#mediaManagerBtn').addEventListener('click', function() {
                window.open('/admin/cms/media', '_blank', 'noopener');
            });

            // Add event listeners for toolbar buttons
            Array.from(toolbar.querySelectorAll('button[data-cmd]')).forEach(btn => {
                btn.addEventListener('click', function() {
                    const cmd = btn.getAttribute('data-cmd');
                    const value = btn.getAttribute('data-value') || null;
                    execCmd(cmd, value);
                });
            });

            toolbar.querySelector('#insertImageBtn').addEventListener('click', insertImage);
            toolbar.querySelector('#alignLeftBtn').addEventListener('click', function() { alignSelectedImage('left'); });
            toolbar.querySelector('#alignCenterBtn').addEventListener('click', function() { alignSelectedImage('center'); });
            toolbar.querySelector('#alignRightBtn').addEventListener('click', function() { alignSelectedImage('right'); });
            toolbar.querySelector('#toggleSourceBtn').addEventListener('click', toggleSource);
            
            // Create editor div (instead of iframe)
            const editor = document.createElement('div');
            editor.className = 'rich-editor-content';
            editor.contentEditable = true;
            editor.style.width = '100%';
            editor.style.height = '400px';
            editor.style.border = '1px solid #ddd';
            editor.style.borderTop = 'none';
            editor.style.padding = '10px';
            editor.style.fontSize = '14px';
            editor.style.fontFamily = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif';
            editor.style.lineHeight = '1.6';
            editor.style.backgroundColor = 'white';
            editor.style.overflowY = 'auto';
            editor.style.outline = 'none';
            
            // Set initial content
            editor.innerHTML = textarea.value || '<p>Start writing your content here...</p>';
            
            // Hide original textarea
            textarea.style.display = 'none';
            
            // Insert editor before textarea
            textarea.parentNode.insertBefore(editorContainer, textarea);
            editorContainer.appendChild(toolbar);
            editorContainer.appendChild(editor);
            
            // Update textarea when content changes
            editor.addEventListener('input', function() {
                textarea.value = editor.innerHTML;
                console.log('Editor content updated');
            });
            
            editor.addEventListener('blur', function() {
                textarea.value = editor.innerHTML;
            });
            
            // Handle paste events
            editor.addEventListener('paste', function(e) {
                e.preventDefault();
                const text = e.clipboardData.getData('text/plain');
                document.execCommand('insertText', false, text);
                textarea.value = editor.innerHTML;
            });

            // Add drag and drop functionality
            editor.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'copy';
                editor.classList.add('drag-over');
            });

            editor.addEventListener('dragleave', function(e) {
                e.preventDefault();
                editor.classList.remove('drag-over');
            });

            editor.addEventListener('drop', function(e) {
                e.preventDefault();
                editor.classList.remove('drag-over');
                
                const files = Array.from(e.dataTransfer.files);
                const imageFiles = files.filter(file => file.type.startsWith('image/'));
                
                if (imageFiles.length === 0) {
                    alert('Please drop image files only.');
                    return;
                }
                
                // Save current position for insertion
                const range = document.createRange();
                const selection = window.getSelection();
                
                // Try to get the position where the drop occurred
                if (document.caretPositionFromPoint) {
                    const caretPos = document.caretPositionFromPoint(e.clientX, e.clientY);
                    if (caretPos) {
                        range.setStart(caretPos.offsetNode, caretPos.offset);
                        range.collapse(true);
                    }
                } else if (document.caretRangeFromPoint) {
                    const caretRange = document.caretRangeFromPoint(e.clientX, e.clientY);
                    if (caretRange) {
                        range.setStart(caretRange.startContainer, caretRange.startOffset);
                        range.collapse(true);
                    }
                }
                
                selection.removeAllRanges();
                selection.addRange(range);
                
                // Upload each image
                imageFiles.forEach(file => {
                    if (file.size > 5 * 1024 * 1024) {
                        alert(`File ${file.name} is too large. Maximum size is 5MB.`);
                        return;
                    }
                    uploadImageForEditor(file, range);
                });
            });
            
            // Store reference for later use
            window.richEditor = {
                element: editor,
                textarea: textarea,
                sourceMode: false
            };
            
            console.log('Rich text editor initialized successfully');
            
            // Focus the editor
            setTimeout(() => {
                editor.focus();
            }, 100);
        }
        
        // Editor command functions
        function execCmd(cmd, value = null) {
            console.log('execCmd called with:', cmd, 'value:', value);
            console.trace(); // This will show us the call stack
            
            if (window.richEditor && !window.richEditor.sourceMode) {
                if (cmd === 'createLink') {
                    console.log('createLink command detected - this is what causes the URL prompt');
                    value = prompt('Enter URL:');
                    if (!value) return;
                }
                
                // Focus the editor first
                window.richEditor.element.focus();
                
                // Execute the command
                document.execCommand(cmd, false, value);
                
                // Update textarea
                window.richEditor.textarea.value = window.richEditor.element.innerHTML;
            }
        }
        
        function insertImage() {
            if (!window.richEditor || window.richEditor.sourceMode) return;
            
            // Create file input
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.style.display = 'none';
            
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;
                
                // Validate file
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file.');
                    return;
                }
                
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must be less than 5MB.');
                    return;
                }
                
                // Save current selection
                const selection = window.getSelection();
                const range = selection.rangeCount > 0 ? selection.getRangeAt(0) : null;
                
                // Upload image
                uploadImageForEditor(file, range);
            });
            
            document.body.appendChild(input);
            input.click();
            document.body.removeChild(input);
        }
        
        function uploadImageForEditor(file, range) {
            const formData = new FormData();
            formData.append('image', file);
            
            // Show loading in editor
            if (range) {
                const loadingSpan = document.createElement('span');
                loadingSpan.textContent = 'Uploading image...';
                loadingSpan.style.color = '#666';
                loadingSpan.style.fontStyle = 'italic';
                
                range.deleteContents();
                range.insertNode(loadingSpan);
                
                window.getSelection().removeAllRanges();
            }
            
            fetch('/admin/cms/upload/image', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(async response => {
                const text = await response.text();
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        // Create image wrapper as inline span by default
                        const imageWrapper = document.createElement('span');
                        imageWrapper.className = 'editor-image-wrapper';
                        imageWrapper.contentEditable = 'false';
                        // Create image element with Bootstrap classes
                        const img = document.createElement('img');
                        img.src = data.url;
                        img.alt = 'Uploaded image';
                        img.className = 'editor-image img-fluid';
                        img.style.maxWidth = '100%';
                        img.style.height = 'auto';
                        // Add click handler for selection
                        img.addEventListener('click', function(e) {
                            e.preventDefault();
                            selectImage(imageWrapper);
                        });
                        imageWrapper.appendChild(img);
                        // Replace loading text with image
                        if (range) {
                            const loadingSpan = window.richEditor.element.querySelector('span');
                            if (loadingSpan && loadingSpan.textContent === 'Uploading image...') {
                                loadingSpan.replaceWith(imageWrapper);
                            } else {
                                // Fallback: append to end
                                window.richEditor.element.appendChild(imageWrapper);
                            }
                        } else {
                            window.richEditor.element.appendChild(imageWrapper);
                        }
                        // Update textarea
                        window.richEditor.textarea.value = window.richEditor.element.innerHTML;
                        console.log('Image inserted successfully:', data);
                    } else {
                        // Remove loading text
                        const loadingSpan = window.richEditor.element.querySelector('span');
                        if (loadingSpan && loadingSpan.textContent === 'Uploading image...') {
                            loadingSpan.remove();
                        }
                        alert('Upload failed: ' + data.error);
                    }
                } catch (e) {
                    // Remove loading text
                    const loadingSpan = window.richEditor.element.querySelector('span');
                    if (loadingSpan && loadingSpan.textContent === 'Uploading image...') {
                        loadingSpan.remove();
                    }
                    alert('Upload failed: Server returned non-JSON response. See console for details.');
                    console.error('Upload response (not JSON):', text);
                }
            })
            .catch(error => {
                // Remove loading text
                const loadingSpan = window.richEditor.element.querySelector('span');
                if (loadingSpan && loadingSpan.textContent === 'Uploading image...') {
                    loadingSpan.remove();
                }
                alert('Upload failed: ' + error.message);
                console.error('Upload error:', error);
            });
        }
        
        function toggleSource() {
            if (!window.richEditor) return;
            
            const editor = window.richEditor;
            const button = event.target;
            
            if (editor.sourceMode) {
                // Switch back to visual mode
                const textarea = document.querySelector('.source-textarea');
                editor.element.innerHTML = textarea.value;
                editor.textarea.value = textarea.value;
                textarea.remove();
                editor.element.style.display = 'block';
                button.textContent = '<>';
                button.title = 'HTML Source';
                editor.sourceMode = false;
                editor.element.focus();
            } else {
                // Switch to source mode
                const sourceTextarea = document.createElement('textarea');
                sourceTextarea.className = 'source-textarea';
                sourceTextarea.style.width = '100%';
                sourceTextarea.style.height = '400px';
                sourceTextarea.style.border = '1px solid #ddd';
                sourceTextarea.style.borderTop = 'none';
                sourceTextarea.style.fontFamily = 'Monaco, Consolas, monospace';
                sourceTextarea.style.fontSize = '12px';
                sourceTextarea.style.padding = '10px';
                sourceTextarea.style.resize = 'vertical';
                sourceTextarea.value = editor.element.innerHTML;
                
                sourceTextarea.addEventListener('input', function() {
                    editor.textarea.value = this.value;
                });
                
                editor.element.style.display = 'none';
                editor.element.parentNode.insertBefore(sourceTextarea, editor.element.nextSibling);
                button.textContent = 'Visual';
                button.title = 'Visual Editor';
                editor.sourceMode = true;
                sourceTextarea.focus();
            }
        }

        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded - initializing tabs and editor');
            
            // Initialize rich text editor
            initializeRichTextEditor();
            
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabPanes = document.querySelectorAll('.tab-pane');
            
            console.log('Found tab buttons:', tabButtons.length);
            console.log('Found tab panes:', tabPanes.length);
            
            tabButtons.forEach((button, index) => {
                console.log(`Tab button ${index}:`, button.getAttribute('data-target'));
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    console.log('Tab clicked:', targetId);
                    
                    // Remove active class from all buttons and panes
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabPanes.forEach(pane => pane.classList.remove('active'));
                    
                    // Add active class to clicked button and corresponding pane
                    this.classList.add('active');
                    const targetPane = document.getElementById(targetId);
                    if (targetPane) {
                        targetPane.classList.add('active');
                        console.log('Tab switched to:', targetId);
                    } else {
                        console.error('Target pane not found:', targetId);
                    }
                    
                    // Store active tab
                    localStorage.setItem('pageFormActiveTab', targetId);
                });
            });
            
            // Restore active tab
            const activeTab = localStorage.getItem('pageFormActiveTab');
            if (activeTab && activeTab !== 'content-pane') {
                const targetButton = document.querySelector(`[data-target="${activeTab}"]`);
                if (targetButton) {
                    targetButton.click();
                }
            }
        });

        // Auto-generate slug from title
        document.getElementById('title').addEventListener('input', function() {
            const title = this.value;
            const slug = title.toLowerCase()
                .replace(/[^\w\s-]/g, '') // Remove special characters
                .replace(/\s+/g, '-')     // Replace spaces with hyphens
                .replace(/-+/g, '-')      // Replace multiple hyphens with single
                .trim();
            document.getElementById('slug').value = slug;
        });

        // Character counters for meta fields
        function updateCharacterCount(inputId, maxLength) {
            const input = document.getElementById(inputId);
            const helpText = input.nextElementSibling;
            
            input.addEventListener('input', function() {
                const currentLength = this.value.length;
                const remaining = maxLength - currentLength;
                
                if (helpText && helpText.classList.contains('form-text')) {
                    helpText.textContent = `${currentLength}/${maxLength} characters`;
                    
                    if (remaining < 10) {
                        helpText.className = 'form-text text-warning';
                    } else if (remaining < 0) {
                        helpText.className = 'form-text text-danger';
                    } else {
                        helpText.className = 'form-text text-muted';
                    }
                }
            });
        }

        // Initialize character counters
        updateCharacterCount('meta_title', 60);
        updateCharacterCount('meta_description', 160);

        // Form submission handling
        document.getElementById('pageForm').addEventListener('submit', function(e) {
            // Ensure rich editor content is saved to textarea before submission
            if (window.richEditor && !window.richEditor.sourceMode) {
                window.richEditor.textarea.value = window.richEditor.element.innerHTML;
            } else if (window.richEditor && window.richEditor.sourceMode) {
                const sourceTextarea = document.querySelector('.source-textarea');
                if (sourceTextarea) {
                    window.richEditor.textarea.value = sourceTextarea.value;
                }
            }
            
            console.log('Enhanced form submitting to:', this.action);
            
            // Log all form data
            const formData = new FormData(this);
            console.log('Form data:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ':', value);
            }
        });

        // Simple OG image upload handler
        function uploadOgImageButtonClick() {
            console.log('uploadOgImageButtonClick called - this should NOT show URL prompt');
            
            // Prevent any default action or event bubbling
            if (window.event) {
                window.event.preventDefault();
                window.event.stopPropagation();
            }
            
            const fileInput = document.getElementById('og_image_file');
            if (fileInput) {
                console.log('File input found, triggering click');
                fileInput.click();
            } else {
                console.error('File input not found');
            }
            
            return false; // Prevent any form submission or other actions
        }

        // Image Upload Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const ogImageFileInput = document.getElementById('og_image_file');
            if (ogImageFileInput) {
                ogImageFileInput.addEventListener('change', function(e) {
                    console.log('OG Image file selected:', e.target.files[0]);
                    const file = e.target.files[0];
                    if (!file) return;
                    
                    // Validate file type
                    if (!file.type.startsWith('image/')) {
                        alert('Please select an image file.');
                        return;
                    }
                    
                    // Validate file size (5MB max)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('File size must be less than 5MB.');
                        return;
                    }
                    
                    uploadOgImage(file);
                });
            } else {
                console.error('og_image_file input not found');
            }
        });
        
        function uploadOgImage(file) {
            const formData = new FormData();
            formData.append('image', file);
            
            // Show upload progress
            const preview = document.getElementById('og_image_preview');
            preview.innerHTML = '<div class="upload-progress">Uploading...</div>';
            
            fetch('/admin/cms/upload/image', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        // Update the URL field with the full-size image
                        document.getElementById('og_image').value = data.url;
                        
                        // Show preview using thumbnail (better for admin UI)
                        preview.innerHTML = '<img src="' + data.thumbnail + '" alt="OG Image Preview">';
                    } else {
                        preview.innerHTML = '';
                        alert('Upload failed: ' + data.error);
                    }
                } catch (e) {
                    preview.innerHTML = '';
                    console.error('JSON parse error:', e);
                    console.error('Server returned:', text);
                    alert('Upload failed: Server returned invalid response. Check console for details.');
                }
            })
            .catch(error => {
                preview.innerHTML = '';
                alert('Upload failed: ' + error.message);
                console.error('OG Upload error:', error);
            });
        }
        
        function clearOgImage() {
            document.getElementById('og_image').value = '';
            document.getElementById('og_image_preview').innerHTML = '';
            document.getElementById('og_image_file').value = '';
        }

        // Fallback: define addResizeHandles if not present
        if (typeof addResizeHandles !== 'function') {
            function addResizeHandles(wrapper, img) {
                // Remove existing handles
                wrapper.querySelectorAll('.resize-handle').forEach(h => h.remove());
                // Only add if not already resizing
                const handles = ['se', 'sw', 'ne', 'nw'];
                handles.forEach(dir => {
                    const handle = document.createElement('span');
                    handle.className = 'resize-handle resize-' + dir;
                    handle.dataset.dir = dir;
                    handle.style.position = 'absolute';
                    handle.style.width = '12px';
                    handle.style.height = '12px';
                    handle.style.background = '#fff';
                    handle.style.border = '2px solid #3498db';
                    handle.style.borderRadius = '50%';
                    handle.style.boxShadow = '0 1px 4px rgba(52,152,219,0.15)';
                    handle.style.zIndex = '10000';
                    handle.style.cursor = dir+'-resize';
                    handle.style.userSelect = 'none';
                    handle.style.display = 'block';
                    // Position handle
                    if (dir === 'se') { handle.style.right = '-6px'; handle.style.bottom = '-6px'; }
                    if (dir === 'sw') { handle.style.left = '-6px'; handle.style.bottom = '-6px'; }
                    if (dir === 'ne') { handle.style.right = '-6px'; handle.style.top = '-6px'; }
                    if (dir === 'nw') { handle.style.left = '-6px'; handle.style.top = '-6px'; }
                    handle.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        startImageResize(e, wrapper, img, dir);
                    });
                    wrapper.style.position = 'relative';
                    wrapper.appendChild(handle);
                });
            }
        }

        // Fallback: define startImageResize if not present
        if (typeof startImageResize !== 'function') {
            function startImageResize(e, wrapper, img, dir) {
                e.preventDefault();
                e.stopPropagation();
                const startX = e.clientX;
                const startY = e.clientY;
                const startWidth = img.offsetWidth;
                const startHeight = img.offsetHeight;
                const aspect = startWidth / startHeight;
                function onMove(ev) {
                    let dx = ev.clientX - startX;
                    let dy = ev.clientY - startY;
                    let newWidth = startWidth, newHeight = startHeight;
                    if (dir === 'se') {
                        newWidth = startWidth + dx;
                        newHeight = newWidth / aspect;
                    } else if (dir === 'sw') {
                        newWidth = startWidth - dx;
                        newHeight = newWidth / aspect;
                    } else if (dir === 'ne') {
                        newWidth = startWidth + dx;
                        newHeight = newWidth / aspect;
                    } else if (dir === 'nw') {
                        newWidth = startWidth - dx;
                        newHeight = newWidth / aspect;
                    }
                    if (newWidth < 32) { newWidth = 32; newHeight = newWidth / aspect; }
                    img.style.width = newWidth + 'px';
                    img.style.height = newHeight + 'px';
                }
                function onUp(ev) {
                    document.removeEventListener('mousemove', onMove);
                    document.removeEventListener('mouseup', onUp);
                    // Sync textarea after resize
                    const editor = document.querySelector('.rich-editor-content');
                    const textarea = document.getElementById('content');
                    if (editor && textarea) textarea.value = editor.innerHTML;
                }
                document.addEventListener('mousemove', onMove);
                document.addEventListener('mouseup', onUp);
            }
        }

        // Expose functions to window for toolbar and image handlers
        window.execCmd = execCmd;
        window.insertImage = insertImage;
        window.alignSelectedImage = alignSelectedImage;
        window.addResizeHandles = addResizeHandles;
        window.startImageResize = startImageResize;
        window.selectImage = selectImage;

        // Ensure selectImage is always called on image click (delegated)
        document.addEventListener('DOMContentLoaded', function() {
            const editor = document.querySelector('.rich-editor-content');
            if (editor) {
                editor.addEventListener('click', function(e) {
                    if (e.target && e.target.tagName === 'IMG' && e.target.classList.contains('editor-image')) {
                        const wrapper = e.target.closest('.editor-image-wrapper');
                        if (wrapper) selectImage(wrapper);
                    }
                });
            }
        });

        // Floating image toolbar
        (function() {
            const toolbar = document.createElement('div');
            toolbar.id = 'image-float-toolbar';
            toolbar.style.position = 'absolute';
            toolbar.style.display = 'none';
            toolbar.style.zIndex = '9999';
            toolbar.style.background = '#fff';
            toolbar.style.border = '1px solid #3498db';
            toolbar.style.borderRadius = '6px';
            toolbar.style.boxShadow = '0 2px 8px rgba(52,152,219,0.15)';
            toolbar.style.padding = '4px 8px';
            toolbar.style.gap = '4px';
            toolbar.style.alignItems = 'center';
            toolbar.style.transition = 'opacity 0.15s';
            toolbar.style.fontSize = '15px';
            toolbar.style.userSelect = 'none';
            toolbar.innerHTML = `
                <button type="button" class="img-toolbar-btn" data-action="left" title="Align Left">⬅️</button>
                <button type="button" class="img-toolbar-btn" data-action="center" title="Align Center">↔️</button>
                <button type="button" class="img-toolbar-btn" data-action="right" title="Align Right">➡️</button>
                <button type="button" class="img-toolbar-btn" data-action="alt" title="Edit Alt Text">📝</button>
                <button type="button" class="img-toolbar-btn" data-action="delete" title="Remove Image">🗑️</button>
            `;
            document.body.appendChild(toolbar);

            // Toolbar button actions
            toolbar.addEventListener('click', function(e) {
                if (!window._selectedImageWrapper) return;
                const action = e.target.getAttribute('data-action');
                if (!action) return;
                if (action === 'left' || action === 'center' || action === 'right') {
                    alignSelectedImage(action);
                } else if (action === 'alt') {
                    const img = window._selectedImageWrapper.querySelector('img');
                    if (img) {
                        const newAlt = prompt('Alt text for image:', img.alt || '');
                        if (newAlt !== null) img.alt = newAlt;
                    }
                } else if (action === 'delete') {
                    window._selectedImageWrapper.remove();
                    toolbar.style.display = 'none';
                    window._selectedImageWrapper = null;
                    window.richEditor.textarea.value = window.richEditor.element.innerHTML;
                }
                window.richEditor.textarea.value = window.richEditor.element.innerHTML;
            });

            // Hide toolbar on click outside
            document.addEventListener('mousedown', function(e) {
                if (!toolbar.contains(e.target) && (!window._selectedImageWrapper || !window._selectedImageWrapper.contains(e.target))) {
                    toolbar.style.display = 'none';
                    if (window._selectedImageWrapper) window._selectedImageWrapper.classList.remove('selected');
                    window._selectedImageWrapper = null;
                }
            });

            // Position toolbar above selected image (with debug and fallback)
            window.showImageToolbar = function(wrapper) {
                const toolbar = document.getElementById('image-float-toolbar');
                const rect = wrapper.getBoundingClientRect();
                const editor = document.querySelector('.rich-editor-content');
                const editorRect = editor ? editor.getBoundingClientRect() : {left:0,top:0,width:window.innerWidth};
                let left = rect.left + window.scrollX;
                let top = rect.top + window.scrollY - toolbar.offsetHeight - 8;
                // Clamp toolbar within editor horizontally
                if (left < editorRect.left + window.scrollX) left = editorRect.left + window.scrollX + 8;
                if (left + toolbar.offsetWidth > editorRect.left + window.scrollX + editorRect.width) left = editorRect.left + window.scrollX + editorRect.width - toolbar.offsetWidth - 8;
                // Clamp toolbar to top of editor if needed
                if (top < editorRect.top + window.scrollY) top = rect.bottom + window.scrollY + 8;
                toolbar.style.left = left + 'px';
                toolbar.style.top = top + 'px';
                toolbar.style.display = 'flex';
                toolbar.style.background = '#fff';
                toolbar.style.border = '2px solid #e67e22';
                toolbar.style.zIndex = '99999';
                toolbar.style.opacity = '1';
                toolbar.style.visibility = 'visible';
                toolbar.style.boxShadow = '0 0 10px 2px #e67e22';
                toolbar.style.pointerEvents = 'auto';
                window._selectedImageWrapper = wrapper;
            };
        })();

        // Enhance selectImage to show floating toolbar
        function selectImage(wrapper) {
            console.log('[DEBUG] selectImage called for wrapper:', wrapper);
            // Deselect others
            document.querySelectorAll('.editor-image-wrapper.selected').forEach(el => el.classList.remove('selected'));
            wrapper.classList.add('selected');
            // Show resize handles
            const img = wrapper.querySelector('img');
            if (img) addResizeHandles(wrapper, img);
            // Show floating toolbar
            if (window.showImageToolbar) window.showImageToolbar(wrapper);
        }

        // Ensure all images are wrapped in .editor-image-wrapper (on load and mutation)
        function wrapAllEditorImages() {
            const editor = document.querySelector('.rich-editor-content');
            if (!editor) return;
            editor.querySelectorAll('img').forEach(img => {
                // Always add editor-image class
                img.classList.add('editor-image');
                if (!img.closest('.editor-image-wrapper')) {
                    const wrapper = document.createElement('span');
                    wrapper.className = 'editor-image-wrapper';
                    wrapper.contentEditable = 'false';
                    img.parentNode.insertBefore(wrapper, img);
                    wrapper.appendChild(img);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const editor = document.querySelector('.rich-editor-content');
            if (editor) {
                // Initial wrap
                wrapAllEditorImages();
                // Observe for DOM changes (e.g. paste, undo, etc)
                const observer = new MutationObserver(() => {
                    wrapAllEditorImages();
                });
                observer.observe(editor, { childList: true, subtree: true });
                // Event delegation for image selection
                editor.addEventListener('click', function(e) {
                    if (e.button !== 0) return;
                    let img = null;
                    if (e.target.tagName === 'IMG' && e.target.classList.contains('editor-image')) {
                        img = e.target;
                    }
                    if (img) {
                        let wrapper = img.closest('.editor-image-wrapper');
                        if (wrapper) {
                            selectImage(wrapper);
                        } else {
                            // Fallback: select the image directly and show toolbar
                            img.classList.add('selected');
                            if (window.showImageToolbar) window.showImageToolbar(img);
                        }
                        e.stopPropagation();
                    }
                });
            }
        });
    </script>
</body>
</html>