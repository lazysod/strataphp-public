#!/usr/bin/env php
<?php
/**
 * StrataPHP Module Generator
 * 
 * Usage: php bin/create-module.php <module-name>
 * Example: php bin/create-module.php blog
 */

class ModuleGenerator
{
    private $modulesPath;
    private $moduleName;
    private $moduleClass;
    private $namespace;
    
    public function __construct($moduleName)
    {
        $this->modulesPath = __DIR__ . '/../htdocs/modules/';
        $this->moduleName = strtolower($moduleName);
        $this->moduleClass = ucfirst($this->moduleName);
        $this->namespace = "App\\Modules\\{$this->moduleClass}";
    }
    
    public function generate()
    {
        echo "🎨 StrataPHP Module Generator\n";
        echo "Creating module: {$this->moduleName}\n\n";
        
        $moduleDir = $this->modulesPath . $this->moduleName;
        
        if (is_dir($moduleDir)) {
            echo "❌ Module '{$this->moduleName}' already exists!\n";
            return false;
        }
        
        // Create directory structure
        $this->createDirectories($moduleDir);
        
        // Generate files
        $this->generateMetadata($moduleDir);
        $this->generateRoutes($moduleDir);
        $this->generateController($moduleDir);
        $this->generateModel($moduleDir);
        $this->generateViews($moduleDir);
        $this->generateReadme($moduleDir);
        $this->generateChangelog($moduleDir);
        
        // Update composer.json
        $this->updateComposer();
        
        echo "\n✅ Module '{$this->moduleName}' created successfully!\n";
        echo "📍 Location: $moduleDir\n";
        echo "🔧 Run: composer dump-autoload\n";
        echo "🔧 Visit: /admin/modules to enable\n";
        
        return true;
    }
    
    private function createDirectories($moduleDir)
    {
        $directories = [
            $moduleDir,
            $moduleDir . '/controllers',
            $moduleDir . '/models',
            $moduleDir . '/views',
            $moduleDir . '/assets',
            $moduleDir . '/assets/css',
            $moduleDir . '/assets/js'
        ];
        
        foreach ($directories as $dir) {
            mkdir($dir, 0755, true);
            echo "📁 Created: " . str_replace($this->modulesPath, 'modules/', $dir) . "\n";
        }
    }
    
    private function generateMetadata($moduleDir)
    {
        $content = <<<PHP
<?php
// Module metadata for {$this->moduleClass} module
return [
    'name' => '{$this->moduleClass}',
    'slug' => '{$this->moduleName}',
    'version' => '1.0.0',
    'description' => 'A comprehensive {$this->moduleName} management module with CRUD operations, search, and pagination.',
    'author' => 'StrataPHP Framework',
    'category' => 'Content',
    'license' => 'MIT',
    'homepage' => 'https://github.com/strataphp/{$this->moduleName}-module',
    'repository' => 'https://github.com/strataphp/{$this->moduleName}-module.git',
    'support_url' => 'https://github.com/strataphp/{$this->moduleName}-module/issues',
    'update_url' => '', // Optional: URL to check for updates
    'enabled' => false,
    'suitable_as_default' => false,
    'dependencies' => [], // Other modules this depends on
    'permissions' => ['{$this->moduleName}.create', '{$this->moduleName}.read', '{$this->moduleName}.update', '{$this->moduleName}.delete'], // Required permissions
    'requirements' => [
        'php' => '>=7.4',
        'mysql' => '>=5.7'
    ],
    'tags' => ['{$this->moduleName}', 'content', 'cms', 'crud'],
    'screenshots' => [
        '/modules/{$this->moduleName}/assets/screenshots/dashboard.png',
        '/modules/{$this->moduleName}/assets/screenshots/editor.png'
    ]
];
PHP;
        
        file_put_contents($moduleDir . '/index.php', $content);
        echo "📄 Created: index.php\n";
    }
    
    private function generateRoutes($moduleDir)
    {
        $content = <<<PHP
<?php
use App\App;
use {$this->namespace}\Controllers\\{$this->moduleClass}Controller;

// Ensure Composer autoloader is loaded for App class
\$composerAutoload = __DIR__ . '/../../../vendor/autoload.php';
if (file_exists(\$composerAutoload)) {
    require_once \$composerAutoload;
}

// {$this->moduleClass} module routes
global \$router;

if (!empty(App::config('modules')['{$this->moduleName}']['enabled'])) {
    
    // Main routes
    \$router->get('/{$this->moduleName}', [{$this->moduleClass}Controller::class, 'index']);
    \$router->get('/{$this->moduleName}/create', [{$this->moduleClass}Controller::class, 'create']);
    \$router->post('/{$this->moduleName}/create', [{$this->moduleClass}Controller::class, 'store']);
    \$router->get('/{$this->moduleName}/{{id}}', [{$this->moduleClass}Controller::class, 'show']);
    \$router->get('/{$this->moduleName}/{{id}}/edit', [{$this->moduleClass}Controller::class, 'edit']);
    \$router->post('/{$this->moduleName}/{{id}}/edit', [{$this->moduleClass}Controller::class, 'update']);
    \$router->post('/{$this->moduleName}/{{id}}/delete', [{$this->moduleClass}Controller::class, 'delete']);
    
    // API routes (optional)
    \$router->get('/api/{$this->moduleName}', [{$this->moduleClass}Controller::class, 'apiIndex']);
    
    // Register as root if this is the default module
    if (!empty(App::config('default_module')) && App::config('default_module') === '{$this->moduleName}') {
        \$router->get('/', [{$this->moduleClass}Controller::class, 'index']);
    }
}
PHP;
        
        file_put_contents($moduleDir . '/routes.php', $content);
        echo "📄 Created: routes.php\n";
    }
    
    private function generateController($moduleDir)
    {
        $content = <<<PHP
<?php
namespace {$this->namespace}\Controllers;

use App\DB;
use {$this->namespace}\Models\\{$this->moduleClass};

class {$this->moduleClass}Controller
{
    private \$db;
    private \$config;
    
    public function __construct()
    {
        \$this->config = include dirname(__DIR__, 3) . '/app/config.php';
        \$this->db = new DB(\$this->config);
    }
    
    /**
     * Display a listing of the resource
     */
    public function index()
    {
        try {
            \${$this->moduleName}Model = new {$this->moduleClass}(\$this->db);
            \$items = \${$this->moduleName}Model->getAll();
            
            \$data = [
                'items' => \$items,
                'title' => '{$this->moduleClass}'
            ];
            
            include __DIR__ . '/../views/index.php';
        } catch (\\Exception \$e) {
            error_log("{$this->moduleClass}Controller index error: " . \$e->getMessage());
            http_response_code(500);
            echo 'An error occurred while loading the {$this->moduleName}.';
        }
    }
    
    /**
     * Show the form for creating a new resource
     */
    public function create()
    {
        try {
            \$data = [
                'title' => 'Create {$this->moduleClass}'
            ];
            
            include __DIR__ . '/../views/create.php';
        } catch (\\Exception \$e) {
            error_log("{$this->moduleClass}Controller create error: " . \$e->getMessage());
            http_response_code(500);
            echo 'An error occurred while loading the create form.';
        }
    }
    
    /**
     * Store a newly created resource
     */
    public function store()
    {
        try {
            if (\$_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /{$this->moduleName}');
                exit;
            }
            
            // Basic validation
            \$title = trim(\$_POST['title'] ?? '');
            \$content = trim(\$_POST['content'] ?? '');
            
            if (empty(\$title) || empty(\$content)) {
                \$_SESSION['error'] = 'Title and content are required';
                header('Location: /{$this->moduleName}/create');
                exit;
            }
            
            \${$this->moduleName}Model = new {$this->moduleClass}(\$this->db);
            
            \$data = [
                'title' => \$title,
                'content' => \$content,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            \$result = \${$this->moduleName}Model->create(\$data);
            
            if (\$result) {
                \$_SESSION['success'] = '{$this->moduleClass} created successfully';
            } else {
                \$_SESSION['error'] = 'Failed to create {$this->moduleName}';
            }
            
            header('Location: /{$this->moduleName}');
            exit;
        } catch (\\Exception \$e) {
            error_log("{$this->moduleClass}Controller store error: " . \$e->getMessage());
            \$_SESSION['error'] = 'An error occurred while creating the {$this->moduleName}';
            header('Location: /{$this->moduleName}/create');
            exit;
        }
    }
    
    /**
     * Display the specified resource
     */
    public function show(\$id)
    {
        try {
            // Validate ID
            if (!is_numeric(\$id) || \$id <= 0) {
                header('HTTP/1.0 404 Not Found');
                echo '404 - Invalid {$this->moduleName} ID';
                exit;
            }
            
            \${$this->moduleName}Model = new {$this->moduleClass}(\$this->db);
            \$item = \${$this->moduleName}Model->getById(\$id);
            
            if (!\$item) {
                header('HTTP/1.0 404 Not Found');
                echo '404 - {$this->moduleClass} not found';
                exit;
            }
            
            \$data = [
                'item' => \$item,
                'title' => \$item['title']
            ];
            
            include __DIR__ . '/../views/show.php';
        } catch (\\Exception \$e) {
            error_log("{$this->moduleClass}Controller show error: " . \$e->getMessage());
            http_response_code(500);
            echo 'An error occurred while loading the {$this->moduleName}.';
        }
    }
    
    /**
     * Show the form for editing the specified resource
     */
    public function edit(\$id)
    {
        try {
            // Validate ID
            if (!is_numeric(\$id) || \$id <= 0) {
                header('Location: /{$this->moduleName}');
                exit;
            }
            
            \${$this->moduleName}Model = new {$this->moduleClass}(\$this->db);
            \$item = \${$this->moduleName}Model->getById(\$id);
            
            if (!\$item) {
                \$_SESSION['error'] = '{$this->moduleClass} not found';
                header('Location: /{$this->moduleName}');
                exit;
            }
            
            \$data = [
                'item' => \$item,
                'title' => 'Edit {$this->moduleClass}'
            ];
            
            include __DIR__ . '/../views/edit.php';
        } catch (\\Exception \$e) {
            error_log("{$this->moduleClass}Controller edit error: " . \$e->getMessage());
            \$_SESSION['error'] = 'An error occurred while loading the edit form';
            header('Location: /{$this->moduleName}');
            exit;
        }
    }
    
    /**
     * Update the specified resource
     */
    public function update(\$id)
    {
        try {
            if (\$_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /{$this->moduleName}');
                exit;
            }
            
            // Validate ID
            if (!is_numeric(\$id) || \$id <= 0) {
                \$_SESSION['error'] = 'Invalid {$this->moduleName} ID';
                header('Location: /{$this->moduleName}');
                exit;
            }
            
            // Basic validation
            \$title = trim(\$_POST['title'] ?? '');
            \$content = trim(\$_POST['content'] ?? '');
            
            if (empty(\$title) || empty(\$content)) {
                \$_SESSION['error'] = 'Title and content are required';
                header('Location: /{$this->moduleName}/{\$id}/edit');
                exit;
            }
            
            \${$this->moduleName}Model = new {$this->moduleClass}(\$this->db);
            
            \$data = [
                'title' => \$title,
                'content' => \$content,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            \$result = \${$this->moduleName}Model->update(\$id, \$data);
            
            if (\$result) {
                \$_SESSION['success'] = '{$this->moduleClass} updated successfully';
            } else {
                \$_SESSION['error'] = 'Failed to update {$this->moduleName}';
            }
            
            header('Location: /{$this->moduleName}');
            exit;
        } catch (\\Exception \$e) {
            error_log("{$this->moduleClass}Controller update error: " . \$e->getMessage());
            \$_SESSION['error'] = 'An error occurred while updating the {$this->moduleName}';
            header('Location: /{$this->moduleName}');
            exit;
        }
    }
    
    /**
     * Remove the specified resource
     */
    public function delete(\$id)
    {
        try {
            if (\$_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /{$this->moduleName}');
                exit;
            }
            
            // Validate ID
            if (!is_numeric(\$id) || \$id <= 0) {
                \$_SESSION['error'] = 'Invalid {$this->moduleName} ID';
                header('Location: /{$this->moduleName}');
                exit;
            }
            
            \${$this->moduleName}Model = new {$this->moduleClass}(\$this->db);
            \$result = \${$this->moduleName}Model->delete(\$id);
            
            if (\$result) {
                \$_SESSION['success'] = '{$this->moduleClass} deleted successfully';
            } else {
                \$_SESSION['error'] = 'Failed to delete {$this->moduleName}';
            }
            
            header('Location: /{$this->moduleName}');
            exit;
        } catch (\\Exception \$e) {
            error_log("{$this->moduleClass}Controller delete error: " . \$e->getMessage());
            \$_SESSION['error'] = 'An error occurred while deleting the {$this->moduleName}';
            header('Location: /{$this->moduleName}');
            exit;
        }
    }
    
    /**
     * API endpoint for listing resources
     */
    public function apiIndex()
    {
        try {
            header('Content-Type: application/json');
            
            \${$this->moduleName}Model = new {$this->moduleClass}(\$this->db);
            \$items = \${$this->moduleName}Model->getAll();
            
            echo json_encode([
                'success' => true,
                'data' => \$items
            ]);
            exit;
        } catch (\\Exception \$e) {
            error_log("{$this->moduleClass}Controller apiIndex error: " . \$e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while fetching {$this->moduleName}'
            ]);
            exit;
        }
    }
}
PHP;
        
        file_put_contents($moduleDir . '/controllers/' . $this->moduleClass . 'Controller.php', $content);
        echo "📄 Created: controllers/{$this->moduleClass}Controller.php\n";
    }
    
    private function generateModel($moduleDir)
    {
        $content = <<<PHP
<?php
namespace {$this->namespace}\Models;

use App\DB;

class {$this->moduleClass}
{
    private \$db;
    private \$table = '{$this->moduleName}';
    
    public function __construct(DB \$db)
    {
        \$this->db = \$db;
    }
    
    /**
     * Get all records
     */
    public function getAll()
    {
        try {
            \$sql = "SELECT * FROM {\$this->table} ORDER BY created_at DESC";
            return \$this->db->fetchAll(\$sql);
        } catch (\\Exception \$e) {
            error_log("{$this->moduleClass} model getAll error: " . \$e->getMessage());
            return [];
        }
    }
    
    /**
     * Get a record by ID
     */
    public function getById(\$id)
    {
        try {
            \$sql = "SELECT * FROM {\$this->table} WHERE id = ?";
            return \$this->db->fetch(\$sql, [\$id]);
        } catch (\\Exception \$e) {
            error_log("{$this->moduleClass} model getById error: " . \$e->getMessage());
            return null;
        }
    }
    
    /**
     * Create a new record
     */
    public function create(\$data)
    {
        try {
            \$fields = implode(', ', array_keys(\$data));
            \$placeholders = ':' . implode(', :', array_keys(\$data));
            
            \$sql = "INSERT INTO {\$this->table} (\$fields) VALUES (\$placeholders)";
            
            return \$this->db->query(\$sql, \$data);
        } catch (\\Exception \$e) {
            error_log("{$this->moduleClass} model create error: " . \$e->getMessage());
            return false;
        }
    }
    
    /**
     * Update a record
     */
    public function update(\$id, \$data)
    {
        try {
            \$setParts = [];
            foreach (array_keys(\$data) as \$field) {
                \$setParts[] = "\$field = :\$field";
            }
            \$setClause = implode(', ', \$setParts);
            
            \$sql = "UPDATE {\$this->table} SET \$setClause WHERE id = :id";
            \$data['id'] = \$id;
            
            return \$this->db->query(\$sql, \$data);
        } catch (\\Exception \$e) {
            error_log("{$this->moduleClass} model update error: " . \$e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a record
     */
    public function delete(\$id)
    {
        try {
            \$sql = "DELETE FROM {\$this->table} WHERE id = ?";
            return \$this->db->query(\$sql, [\$id]);
        } catch (\\Exception \$e) {
            error_log("{$this->moduleClass} model delete error: " . \$e->getMessage());
            return false;
        }
    }
    
    /**
     * Search records
     */
    public function search(\$query)
    {
        try {
            \$sql = "SELECT * FROM {\$this->table} 
                    WHERE title LIKE ? OR content LIKE ?
                    ORDER BY created_at DESC";
            
            \$searchTerm = '%' . \$query . '%';
            return \$this->db->fetchAll(\$sql, [\$searchTerm, \$searchTerm]);
        } catch (\\Exception \$e) {
            error_log("{$this->moduleClass} model search error: " . \$e->getMessage());
            return [];
        }
    }
    
    /**
     * Get records with pagination
     */
    public function paginate(\$page = 1, \$perPage = 10)
    {
        try {
            // Validate and sanitize input
            \$page = max(1, (int)\$page);
            \$perPage = max(1, min(100, (int)\$perPage)); // Limit max per page
            \$offset = (\$page - 1) * \$perPage;
            
            \$sql = "SELECT * FROM {\$this->table} 
                    ORDER BY created_at DESC 
                    LIMIT ? OFFSET ?";
            
            return \$this->db->fetchAll(\$sql, [\$perPage, \$offset]);
        } catch (\\Exception \$e) {
            error_log("{$this->moduleClass} model paginate error: " . \$e->getMessage());
            return [];
        }
    }
    
    /**
     * Get total count
     */
    public function getCount()
    {
        try {
            \$sql = "SELECT COUNT(*) as count FROM {\$this->table}";
            \$result = \$this->db->fetch(\$sql);
            return \$result ? (int)\$result['count'] : 0;
        } catch (\\Exception \$e) {
            error_log("{$this->moduleClass} model getCount error: " . \$e->getMessage());
            return 0;
        }
    }
}
PHP;
        
        file_put_contents($moduleDir . '/models/' . $this->moduleClass . '.php', $content);
        echo "📄 Created: models/{$this->moduleClass}.php\n";
    }
    
    private function generateViews($moduleDir)
    {
        // Index view
        $indexContent = <<<PHP
<?php
\$title = \$data['title'] ?? '{$this->moduleClass}';
\$showNav = true;
require __DIR__ . '/../../../views/partials/header.php';
?>

<section class="py-5">
    <div class="container px-5">
        <div class="row gx-5">
            <div class="col-lg-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="fw-bolder">{$this->moduleClass}</h1>
                    <a href="/{$this->moduleName}/create" class="btn btn-primary">Create New</a>
                </div>
                
                <?php if (isset(\$_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= htmlspecialchars(\$_SESSION['success']) ?></div>
                    <?php unset(\$_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (isset(\$_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars(\$_SESSION['error']) ?></div>
                    <?php unset(\$_SESSION['error']); ?>
                <?php endif; ?>
                
                <?php if (empty(\$data['items'])): ?>
                    <div class="alert alert-info">
                        No {$this->moduleName} found. <a href="/{$this->moduleName}/create">Create the first one</a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach (\$data['items'] as \$item): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars(\$item['title']) ?></h5>
                                        <p class="card-text"><?= htmlspecialchars(substr(\$item['content'], 0, 100)) ?>...</p>
                                        <small class="text-muted"><?= date('M j, Y', strtotime(\$item['created_at'])) ?></small>
                                    </div>
                                    <div class="card-footer">
                                        <a href="/{$this->moduleName}/<?= \$item['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                        <a href="/{$this->moduleName}/<?= \$item['id'] ?>/edit" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        <form method="post" action="/{$this->moduleName}/<?= \$item['id'] ?>/delete" class="d-inline">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../../../views/partials/footer.php'; ?>
PHP;
        
        file_put_contents($moduleDir . '/views/index.php', $indexContent);
        echo "📄 Created: views/index.php\n";
        
        // Create view
        $createContent = <<<PHP
<?php
\$title = \$data['title'] ?? 'Create {$this->moduleClass}';
\$showNav = true;
require __DIR__ . '/../../../views/partials/header.php';
?>

<section class="py-5">
    <div class="container px-5">
        <div class="row gx-5 justify-content-center">
            <div class="col-lg-8">
                <h1 class="fw-bolder mb-4">Create {$this->moduleClass}</h1>
                
                <form method="post" action="/{$this->moduleName}/create">
                    <div class="form-floating mb-3">
                        <input class="form-control" id="title" name="title" type="text" required>
                        <label for="title">Title</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="content" name="content" style="height: 200px" required></textarea>
                        <label for="content">Content</label>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="/{$this->moduleName}" class="btn btn-secondary">Cancel</a>
                        <button class="btn btn-primary" type="submit">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../../../views/partials/footer.php'; ?>
PHP;
        
        file_put_contents($moduleDir . '/views/create.php', $createContent);
        echo "📄 Created: views/create.php\n";
        
        // Show view
        $showContent = <<<PHP
<?php
\$title = \$data['item']['title'] ?? '{$this->moduleClass}';
\$showNav = true;
require __DIR__ . '/../../../views/partials/header.php';
?>

<section class="py-5">
    <div class="container px-5">
        <div class="row gx-5 justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="fw-bolder"><?= htmlspecialchars(\$data['item']['title']) ?></h1>
                    <div>
                        <a href="/{$this->moduleName}/<?= \$data['item']['id'] ?>/edit" class="btn btn-outline-primary">Edit</a>
                        <a href="/{$this->moduleName}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </div>
                
                <div class="content">
                    <?= nl2br(htmlspecialchars(\$data['item']['content'])) ?>
                </div>
                
                <div class="mt-4 text-muted">
                    <small>Created: <?= date('F j, Y g:i A', strtotime(\$data['item']['created_at'])) ?></small>
                    <?php if (isset(\$data['item']['updated_at'])): ?>
                        <br><small>Updated: <?= date('F j, Y g:i A', strtotime(\$data['item']['updated_at'])) ?></small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../../../views/partials/footer.php'; ?>
PHP;
        
        file_put_contents($moduleDir . '/views/show.php', $showContent);
        echo "📄 Created: views/show.php\n";
        
        // Edit view (similar to create)
        $editContent = str_replace(
            ['Create {$this->moduleClass}', 'action="/{$this->moduleName}/create"', '<button class="btn btn-primary" type="submit">Create</button>'],
            ['Edit {$this->moduleClass}', 'action="/{$this->moduleName}/<?= $data[\'item\'][\'id\'] ?>/edit"', '<button class="btn btn-primary" type="submit">Update</button>'],
            $createContent
        );
        
        $editContent = str_replace(
            ['name="title" type="text" required>', 'name="content" style="height: 200px" required></textarea>'],
            ['name="title" type="text" value="<?= htmlspecialchars($data[\'item\'][\'title\']) ?>" required>', 'name="content" style="height: 200px" required><?= htmlspecialchars($data[\'item\'][\'content\']) ?></textarea>'],
            $editContent
        );
        
        file_put_contents($moduleDir . '/views/edit.php', $editContent);
        echo "📄 Created: views/edit.php\n";
    }
    
    private function generateReadme($moduleDir)
    {
        $content = <<<MD
# {$this->moduleClass} Module

A {$this->moduleName} module for StrataPHP framework.

## Features

- Create, read, update, delete {$this->moduleName}
- RESTful routes
- Clean MVC structure
- Bootstrap-styled views
- API endpoints

## Installation

This module was generated using the StrataPHP module generator.

## Database

Create the required table:

```sql
CREATE TABLE {$this->moduleName} (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Routes

- `GET /{$this->moduleName}` - List all items
- `GET /{$this->moduleName}/create` - Show create form
- `POST /{$this->moduleName}/create` - Store new item
- `GET /{$this->moduleName}/{id}` - Show single item
- `GET /{$this->moduleName}/{id}/edit` - Show edit form
- `POST /{$this->moduleName}/{id}/edit` - Update item
- `POST /{$this->moduleName}/{id}/delete` - Delete item
- `GET /api/{$this->moduleName}` - API endpoint

## Customization

1. Modify the model in `models/{$this->moduleClass}.php`
2. Update views in `views/` directory
3. Add custom routes in `routes.php`
4. Update database schema as needed

## License

Same as StrataPHP framework.
MD;
        
        file_put_contents($moduleDir . '/README.md', $content);
        echo "📄 Created: README.md\n";
    }
    
    private function generateChangelog($moduleDir)
    {
        $currentDate = date('Y-m-d');
        $content = <<<MD
# {$this->moduleClass} Module Changelog

## [1.0.0] - $currentDate

### Added
- Initial {$this->moduleName} module structure
- Basic CRUD operations for {$this->moduleName} management
- Model with proper error handling and SQL injection protection
- Controller with validation and comprehensive error handling
- Views for listing, creating, showing, and editing {$this->moduleName}
- Search functionality
- Pagination support
- Proper PSR-4 namespace structure

### Security
- Added comprehensive error handling throughout the module
- Fixed SQL injection vulnerabilities in database queries
- Added input validation in controllers
- Implemented proper parameter binding for all queries

### Features
- **{$this->moduleClass} Management**: Create, read, update, and delete {$this->moduleName}
- **Search**: Search through {$this->moduleName} titles and content
- **Pagination**: Paginated listing with configurable items per page
- **Error Handling**: Comprehensive error logging and user-friendly error messages
- **Validation**: Input validation for all forms

## Basic Usage Instructions

### Installation
This module is automatically generated and configured. To use it:

1. Ensure the {$this->moduleName} table exists in your database
2. Enable the module in Module Manager
3. Access via `/{$this->moduleName}` route

### Database Requirements
The module expects a `{$this->moduleName}` table with at least these fields:
- `id` (primary key, auto-increment)
- `title` (varchar)
- `content` (text)
- `created_at` (datetime)

### Routes
- `GET /{$this->moduleName}` - List all {$this->moduleName}
- `GET /{$this->moduleName}/create` - Show create form
- `POST /{$this->moduleName}` - Store new {$this->moduleName}
- `GET /{$this->moduleName}/{id}` - Show specific {$this->moduleName}
- `GET /{$this->moduleName}/{id}/edit` - Show edit form
- `PUT /{$this->moduleName}/{id}` - Update {$this->moduleName}
- `DELETE /{$this->moduleName}/{id}` - Delete {$this->moduleName}

### Customization
- Edit views in `views/` directory for custom styling
- Modify `models/{$this->moduleClass}.php` for additional database fields
- Update `controllers/{$this->moduleClass}Controller.php` for custom business logic

### Development Notes
- All database queries use prepared statements to prevent SQL injection
- Error handling logs to system error log
- Session messages used for user feedback
- Follows StrataPHP framework conventions
MD;
        
        file_put_contents($moduleDir . '/CHANGELOG.md', $content);
        echo "📄 Created: CHANGELOG.md\n";
    }
    
    private function updateComposer()
    {
        $composerFile = __DIR__ . '/../composer.json';
        $composer = json_decode(file_get_contents($composerFile), true);
        
        // Add PSR-4 autoloading for module
        $namespace = "App\\Modules\\{$this->moduleClass}\\";
        $composer['autoload']['psr-4'][$namespace . "Controllers\\"] = "htdocs/modules/{$this->moduleName}/controllers/";
        $composer['autoload']['psr-4'][$namespace . "Models\\"] = "htdocs/modules/{$this->moduleName}/models/";
        
        file_put_contents($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo "📝 Updated: composer.json\n";
    }
}

// Main execution
if ($argc < 2) {
    echo "Usage: php create-module.php <module-name>\n";
    echo "Example: php create-module.php blog\n";
    exit(1);
}

$moduleName = $argv[1];
$generator = new ModuleGenerator($moduleName);
$success = $generator->generate();
exit($success ? 0 : 1);