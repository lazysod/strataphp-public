<?php
// Admin: List all sites
if (!defined('STRPHP_ROOT')) {
}
$success_message = $_SESSION['success'] ?? null;
$error_message = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Manage Sites') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
        .breadcrumb {
            margin-bottom: 20px;
            font-size: 14px;
        }
        .breadcrumb a {
            color: #3498db;
            text-decoration: none;
        }
        .breadcrumb a:hover {
            text-decoration: underline;
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
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        .table tr:hover {
            background-color: #f8f9fa;
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
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="breadcrumb">
            <a href="/admin">Admin</a> > <a href="/admin/cms">CMS</a> > Sites
        </div>
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        <div class="header">
            <h1><?= htmlspecialchars($title ?? 'Manage Sites') ?></h1>
            <a href="/admin/cms/sites/create" class="btn btn-success">+ Create New Site</a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>API Key</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($sites as $site): ?>
                <tr>
                    <td><?= $site['id'] ?></td>
                    <td><?= htmlspecialchars($site['name']) ?></td>
                    <td style="font-family:monospace;word-break:break-all;max-width:300px;">
                        <?= htmlspecialchars($site['api_key']) ?>
                    </td>
                    <td><?= htmlspecialchars($site['status']) ?></td>
                    <td><?= htmlspecialchars($site['created_at']) ?></td>
                    <td><?= htmlspecialchars($site['updated_at']) ?></td>
                    <td>
                        <a href="/admin/cms/sites/regenerate/<?= $site['id'] ?>" class="btn btn-warning btn-small" onclick="return confirm('Regenerate API key for this site?')">Regenerate Key</a>
                        <form method="post" action="/admin/cms/sites/delete/<?= $site['id'] ?>" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this site? This cannot be undone.');">
                            <button type="submit" class="btn btn-danger btn-small" title="Delete Site" style="margin-left:5px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
