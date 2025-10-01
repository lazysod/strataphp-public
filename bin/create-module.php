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
    'description' => 'A {$this->moduleName} module for StrataPHP.',
    'author' => 'Your Name',
    'category' => 'Content',
    'update_url' => '', // Optional: URL to check for updates
    'enabled' => false,
    'suitable_as_default' => false,
    'dependencies' => [], // Other modules this depends on
    'permissions' => [], // Required permissions
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
        \${$this->moduleName}Model = new {$this->moduleClass}(\$this->db);
        \$items = \${$this->moduleName}Model->getAll();
        
        \$data = [
            'items' => \$items,
            'title' => '{$this->moduleClass}'
        ];
        
        include __DIR__ . '/../views/index.php';
    }
    
    /**
     * Show the form for creating a new resource
     */
    public function create()
    {
        \$data = [
            'title' => 'Create {$this->moduleClass}'
        ];
        
        include __DIR__ . '/../views/create.php';
    }
    
    /**
     * Store a newly created resource
     */
    public function store()
    {
        if (\$_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /{$this->moduleName}');
            exit;
        }
        
        \${$this->moduleName}Model = new {$this->moduleClass}(\$this->db);
        
        \$data = [
            'title' => \$_POST['title'] ?? '',
            'content' => \$_POST['content'] ?? '',
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
    }
    
    /**
     * Display the specified resource
     */
    public function show(\$id)
    {
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
    }
    
    /**
     * Show the form for editing the specified resource
     */
    public function edit(\$id)
    {
        \${$this->moduleName}Model = new {$this->moduleClass}(\$this->db);
        \$item = \${$this->moduleName}Model->getById(\$id);
        
        if (!\$item) {
            header('Location: /{$this->moduleName}');
            exit;
        }
        
        \$data = [
            'item' => \$item,
            'title' => 'Edit {$this->moduleClass}'
        ];
        
        include __DIR__ . '/../views/edit.php';
    }
    
    /**
     * Update the specified resource
     */
    public function update(\$id)
    {
        if (\$_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /{$this->moduleName}');
            exit;
        }
        
        \${$this->moduleName}Model = new {$this->moduleClass}(\$this->db);
        
        \$data = [
            'title' => \$_POST['title'] ?? '',
            'content' => \$_POST['content'] ?? '',
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
    }
    
    /**
     * Remove the specified resource
     */
    public function delete(\$id)
    {
        if (\$_SERVER['REQUEST_METHOD'] !== 'POST') {
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
    }
    
    /**
     * API endpoint for listing resources
     */
    public function apiIndex()
    {
        header('Content-Type: application/json');
        
        \${$this->moduleName}Model = new {$this->moduleClass}(\$this->db);
        \$items = \${$this->moduleName}Model->getAll();
        
        echo json_encode([
            'success' => true,
            'data' => \$items
        ]);
        exit;
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
        \$sql = "SELECT * FROM {\$this->table} ORDER BY created_at DESC";
        return \$this->db->fetchAll(\$sql);
    }
    
    /**
     * Get a record by ID
     */
    public function getById(\$id)
    {
        \$sql = "SELECT * FROM {\$this->table} WHERE id = ?";
        return \$this->db->fetch(\$sql, [\$id]);
    }
    
    /**
     * Create a new record
     */
    public function create(\$data)
    {
        \$fields = implode(', ', array_keys(\$data));
        \$placeholders = ':' . implode(', :', array_keys(\$data));
        
        \$sql = "INSERT INTO {\$this->table} (\$fields) VALUES (\$placeholders)";
        
        return \$this->db->query(\$sql, \$data);
    }
    
    /**
     * Update a record
     */
    public function update(\$id, \$data)
    {
        \$setParts = [];
        foreach (array_keys(\$data) as \$field) {
            \$setParts[] = "\$field = :\$field";
        }
        \$setClause = implode(', ', \$setParts);
        
        \$sql = "UPDATE {\$this->table} SET \$setClause WHERE id = :id";
        \$data['id'] = \$id;
        
        return \$this->db->query(\$sql, \$data);
    }
    
    /**
     * Delete a record
     */
    public function delete(\$id)
    {
        \$sql = "DELETE FROM {\$this->table} WHERE id = ?";
        return \$this->db->query(\$sql, [\$id]);
    }
    
    /**
     * Search records
     */
    public function search(\$query)
    {
        \$sql = "SELECT * FROM {\$this->table} 
                WHERE title LIKE ? OR content LIKE ?
                ORDER BY created_at DESC";
        
        \$searchTerm = '%' . \$query . '%';
        return \$this->db->fetchAll(\$sql, [\$searchTerm, \$searchTerm]);
    }
    
    /**
     * Get records with pagination
     */
    public function paginate(\$page = 1, \$perPage = 10)
    {
        \$offset = (\$page - 1) * \$perPage;
        
        \$sql = "SELECT * FROM {\$this->table} 
                ORDER BY created_at DESC 
                LIMIT \$perPage OFFSET \$offset";
        
        return \$this->db->fetchAll(\$sql);
    }
    
    /**
     * Get total count
     */
    public function getCount()
    {
        \$sql = "SELECT COUNT(*) as count FROM {\$this->table}";
        \$result = \$this->db->fetch(\$sql);
        return \$result ? (int)\$result['count'] : 0;
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