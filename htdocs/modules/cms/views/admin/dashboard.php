<?php
// CMS Dashboard Template
if (!defined('STRPHP_ROOT')) {
    exit('Direct access not allowed');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'CMS Dashboard') ?></title>
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
            border-bottom: 2px solid #3498db;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #3498db;
            color: white;
            padding: 20px;
            border-radius: 6px;
            text-align: center;
        }
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 2em;
        }
        .stat-card p {
            margin: 0;
            opacity: 0.9;
        }
        .actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .action-card {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 6px;
            text-align: center;
        }
        .action-card h3 {
            margin: 0 0 15px 0;
            color: #2c3e50;
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
        .recent-content {
            margin-top: 30px;
        }
        .recent-content h2 {
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .content-list {
            list-style: none;
            padding: 0;
        }
        .content-list li {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .content-list li:last-child {
            border-bottom: none;
        }
        .status {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            text-transform: uppercase;
        }
        .status.published {
            background: #d4edda;
            color: #155724;
        }
        .status.draft {
            background: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?= htmlspecialchars($title ?? 'CMS Dashboard') ?></h1>
            <p>Welcome to your StrataPHP Content Management System</p>
        </div>

        <?php if (isset($stats)): ?>
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= $stats['total_pages'] ?? 0 ?></h3>
                <p>Total Pages</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['published_pages'] ?? 0 ?></h3>
                <p>Published Pages</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['draft_pages'] ?? 0 ?></h3>
                <p>Draft Pages</p>
            </div>
        </div>
        <?php endif; ?>

        <div class="actions">
            <div class="action-card">
                <h3>📝 Manage Pages</h3>
                <p>Create, edit, and organize your website pages</p>
                <a href="/admin/cms/pages" class="btn">Manage Pages</a>
            </div>
            <div class="action-card">
                <h3>➕ Create New Page</h3>
                <p>Add new content to your website</p>
                <a href="/admin/cms/pages/create" class="btn btn-success">Create Page</a>
            </div>
            <div class="action-card">
                <h3>🌐 View Website</h3>
                <p>See how your site looks to visitors</p>
                <a href="/" class="btn" target="_blank">View Site</a>
            </div>
            <div class="action-card">
                <h3>📊 API Access</h3>
                <p>Access your content via REST API</p>
                <a href="/api/cms/pages" class="btn" target="_blank">View API</a>
            </div>
        </div>

        <?php if (isset($stats['recent_pages']) && !empty($stats['recent_pages'])): ?>
        <div class="recent-content">
            <h2>Recent Pages</h2>
            <ul class="content-list">
                <?php foreach ($stats['recent_pages'] as $page): ?>
                <li>
                    <div>
                        <strong><?= htmlspecialchars($page['title']) ?></strong>
                        <br>
                        <small>Created: <?= date('M j, Y', strtotime($page['created_at'])) ?></small>
                    </div>
                    <div>
                        <span class="status <?= $page['status'] ?>"><?= ucfirst($page['status']) ?></span>
                        <a href="/admin/cms/pages/<?= $page['id'] ?>/edit" class="btn" style="margin-left: 10px; padding: 5px 10px; font-size: 12px;">Edit</a>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666; font-size: 14px;">
            <p>StrataPHP CMS Module - Version 1.0.0</p>
            <p><a href="/admin" style="color: #3498db;">← Back to Admin Panel</a></p>
        </div>
    </div>
</body>
</html>