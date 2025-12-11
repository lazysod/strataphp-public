<?php
// Only include the admin navigation bar partial once, inside the <body>


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Posts - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="/modules/cms/assets/css/styles.css">
</head>
<body>
<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$modules = include __DIR__ . '/../../../../app/modules.php';
include __DIR__ . '/../../../../views/admin/partials/nav.php'; 
?>
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center mb-3">
            <h2><i class="fas fa-blog"></i> Blog Posts</h2>
            <a href="/admin/blog/post/add" class="btn btn-primary"><i class="fas fa-plus"></i> Add Post</a>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loop blog posts here -->
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
