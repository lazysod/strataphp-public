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
    <style>
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
        
        .source-textarea {
            resize: vertical;
            background: #f8f9fa;
            color: #333;
            border: none !important;
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
                                        <label for="template">Template</label>
                                        <select id="template" name="template">
                                            <option value="default" <?= (isset($page) && $page['template'] === 'default') ? 'selected' : '' ?>>Default</option>
                                            <option value="home" <?= (isset($page) && $page['template'] === 'home') ? 'selected' : '' ?>>Home</option>
                                            <option value="about" <?= (isset($page) && $page['template'] === 'about') ? 'selected' : '' ?>>About</option>
                                            <option value="contact" <?= (isset($page) && $page['template'] === 'contact') ? 'selected' : '' ?>>Contact</option>
                                        </select>
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
                                        <input type="url" id="og_image" name="og_image" 
                                               value="<?= isset($page) ? htmlspecialchars($page['og_image'] ?? '') : '' ?>" 
                                               placeholder="https://example.com/image.jpg">
                                        <div class="form-text">Recommended: 1200x630px</div>
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
            toolbar.innerHTML = `
                <button type="button" onclick="execCmd('bold')" title="Bold"><b>B</b></button>
                <button type="button" onclick="execCmd('italic')" title="Italic"><i>I</i></button>
                <button type="button" onclick="execCmd('underline')" title="Underline"><u>U</u></button>
                <button type="button" onclick="execCmd('formatBlock', 'h2')" title="Heading 2">H2</button>
                <button type="button" onclick="execCmd('formatBlock', 'h3')" title="Heading 3">H3</button>
                <button type="button" onclick="execCmd('formatBlock', 'p')" title="Paragraph">P</button>
                <button type="button" onclick="execCmd('insertUnorderedList')" title="Bullet List">• List</button>
                <button type="button" onclick="execCmd('insertOrderedList')" title="Numbered List">1. List</button>
                <button type="button" onclick="execCmd('createLink')" title="Link">🔗</button>
                <button type="button" onclick="execCmd('removeFormat')" title="Clear Format">Clear</button>
                <button type="button" onclick="toggleSource()" title="HTML Source">&lt;&gt;</button>
            `;
            
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
            if (window.richEditor && !window.richEditor.sourceMode) {
                if (cmd === 'createLink') {
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
    </script>
</body>
</html>