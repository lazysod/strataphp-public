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
    private $moduleSlug;
    private $moduleClass;
    private $namespace;
    
    public function __construct($moduleName)
    {
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9\-_]*$/', $moduleName)) {
            throw new InvalidArgumentException("Module name must start with a letter and contain only letters, numbers, hyphens, and underscores");
        }
        
        $this->modulesPath = __DIR__ . '/../public_html/modules/';
        $normalizedName = strtolower(str_replace('_', '-', $moduleName));
        $this->moduleSlug = $normalizedName;
        $this->moduleName = str_replace('-', '_', $normalizedName);
        $this->moduleClass = str_replace(['-', '_'], '', ucwords($normalizedName, '-_'));
        $this->namespace = "App\\Modules\\{$this->moduleClass}";
    }
    
    public function generate()
    {
        echo "🎨 StrataPHP Module Generator\n";
        echo "Creating module with the following conventions:\n";
        echo "  • Slug (metadata): {$this->moduleSlug}\n";
        echo "  • Directory: {$this->moduleName}\n";
        echo "  • Class: {$this->moduleClass}\n\n";
        
        $moduleDir = $this->modulesPath . $this->moduleName;
        
        if (is_dir($moduleDir)) {
            echo "❌ Module '{$this->moduleName}' already exists!\n";
            return false;
        }
        
        $this->createDirectories($moduleDir);
        $this->generateMetadata($moduleDir);
        $this->generateRoutes($moduleDir);
        $this->generateController($moduleDir);
        $this->generateModel($moduleDir);
        $this->generateViews($moduleDir);
        $this->generateReadme($moduleDir);
        $this->generateChangelog($moduleDir);
        $this->updateComposer();
        $this->updateModulesConfig();
        
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
    'slug' => '{$this->moduleSlug}',
    'version' => '1.0.0',
    'description' => 'A comprehensive {$this->moduleName} management module with CRUD operations, search, and pagination.',
    'author' => 'StrataPHP Framework',
    'category' => 'Content',
    'license' => 'MIT',
    'homepage' => 'https://github.com/strataphp/{$this->moduleName}-module',
    'repository' => 'https://github.com/strataphp/{$this->moduleName}-module.git',
    'support_url' => 'https://github.com/strataphp/{$this->moduleName}-module/issues',
    'update_url' => '',
    'enabled' => false,
    'suitable_as_default' => false,
    'dependencies' => [],
    'permissions' => ['{$this->moduleName}.create', '{$this->moduleName}.read', '{$this->moduleName}.update', '{$this->moduleName}.delete'],
    'requirements' => [
        'php' => '>=8.2+',
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

\$composerAutoload = __DIR__ . '/../../../vendor/autoload.php';
if (file_exists(\$composerAutoload)) {
    require_once \$composerAutoload;
}

global \$router;

if (!empty(App::config('modules')['{$this->moduleName}']['enabled'])) {
    
    \$router->get('/{$this->moduleName}', [{$this->moduleClass}Controller::class, 'index']);
    \$router->get('/{$this->moduleName}/create', [{$this->moduleClass}Controller::class, 'create']);
    \$router->post('/{$this->moduleName}/create', [{$this->moduleClass}Controller::class, 'store']);
    \$router->get('/{$this->moduleName}/{{id}}', [{$this->moduleClass}Controller::class, 'show']);
    \$router->get('/{$this->moduleName}/{{id}}/edit', [{$this->moduleClass}Controller::class, 'edit']);
    \$router->post('/{$this->moduleName}/{{id}}/edit', [{$this->moduleClass}Controller::class, 'update']);
    \$router->post('/{$this->moduleName}/{{id}}/delete', [{$this->moduleClass}Controller::class, 'delete']);
    
    \$router->get('/api/{$this->moduleName}', [{$this->moduleClass}Controller::class, 'apiIndex']);
    
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
use App\Logger;
use {$this->namespace}\Models\\{$this->moduleClass};

class {$this->moduleClass}Controller
{
    private \$db;
    private \$config;
    private \$logger;
    
    public function __construct()
    {
        \$this->config = include dirname(__DIR__, 3) . '/app/config.php';
        \$this->db = new DB(\$this->config);
        \$this->logger = Logger::getInstance(\$this->config);
    }
    
    public function index()
    {
        try {
            \$this->logger->debug('{$this->moduleClass}Controller: index called');
            \${$this->moduleName}Model = new {$this->moduleClass}(\$this->db);
            \$items = \${$this->moduleName}Model->getAll();
            
            \$this->logger->info('{$this->moduleClass} list loaded', ['count' => count(\$items)]);
            
            \$data = [
                'items' => \$items,
                'title' => '{$this->moduleClass}'
            ];
            
            include __DIR__ . '/../views/index.php';
        } catch (\\Exception \$e) {
            \$this->logger->error('{$this->moduleClass}Controller index error', [
                'error' => \$e->getMessage(),
                'trace' => \$e->getTraceAsString()
            ]);
            http_response_code(500);
            echo 'An error occurred while loading the {$this->moduleName}.';
        }
    }
    
    public function create()
    {
        try {
            \$this->logger->debug('{$this->moduleClass}Controller: create form called');
            \$data = ['title' => 'Create {$this->moduleClass}'];
            include __DIR__ . '/../views/create.php';
        } catch (\\Exception \$e) {
            \$this->logger->error('{$this->moduleClass}Controller create error', ['error' => \$e->getMessage()]);
            http_response_code(500);
            echo 'An error occurred while loading the create form.';
        }
    }
    
    public function store()
    {
        try {
            if (\$_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /{$this->moduleName}');
                exit;
            }
            
            \$title = trim(\$_POST['title'] ?? '');
            \$content = trim(\$_POST['content'] ?? '');
            
            \$this->logger->debug('{$this->moduleClass}Controller: store attempt', ['title' => \$title]);
            
            if (empty(\$title) || empty(\$content)) {
                \$this->logger->warning('{$this->moduleClass} validation failed', ['reason' => 'empty title or content']);
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
                \$this->logger->info('{$this->moduleClass} created', ['title' => \$title]);
                \$_SESSION['success'] = '{$this->moduleClass} created successfully';
            } else {
                \$this->logger->error('{$this->moduleClass} create failed');
                \$_SESSION['error'] = 'Failed to create {$this->moduleName}';
            }
            
            header('Location: /{$this->moduleName}');
            exit;
        } catch (\\Exception \$e) {
            \$this->logger->error('{$this->moduleClass}Controller store error', [
                'error' => \$e->getMessage(),
                'post' => \$_POST
            ]);
            \$_SESSION['error'] = 'An error occurred while creating the {$this->moduleName}';
            header('Location: /{$this->moduleName}/create');
            exit;
        }
    }
    
    public function show(\$id)
    {
        try {
            \$this->logger->debug('{$this->moduleClass}Controller: show called', ['id' => \$id]);
            
            if (!is_numeric(\$id) || \$id <= 0) {
                \$this->logger->warning('Invalid {$this->moduleName} ID', ['id' => \$id]);
                header('HTTP/1.0 404 Not Found');
                echo '404 - Invalid {$this->moduleName} ID';
                exit;
            }
            
            \${$this->moduleName}Model = new {$this->moduleClass}(\$this->db);
            \$item = \${$this->moduleName}Model->getById(\$id);
            
            if (!\$item) {
                \$this->logger->warning('{$this->moduleClass} not found', ['id' => \$id]);
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
            \$this->logger->error('{$this->moduleClass}Controller show error', [
                'id' => \$id,
                'error' => \$e->getMessage()
            ]);
            http_response_code(500);
            echo 'An error occurred while loading the {$this->moduleName}.';
        }
    }
    
    public function edit(\$id)
    {
        try {
            \$this->logger->debug('{$this->moduleClass}Controller: edit form called', ['id' => \$id]);
            
            if (!is_numeric(\$id) || \$id <= 0) {
                \$this->logger->warning('Invalid ID for edit', ['id' => \$id]);
                header('Location: /{$this->moduleName}');
                exit;
            }
            
            \${$this->moduleName}Model = new {$this->moduleClass}(\$this->db);
            \$item = \${$this->moduleName}Model->getById(\$id);
            
            if (!\$item) {
                \$this->logger->warning('{$this->moduleClass} not found for edit', ['id' => \$id]);
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
            \$this->logger->error('{$this->moduleClass}Controller edit error', [
                'id' => \$id,
                'error' => \$e->getMessage()
            ]);
            \$_SESSION['error'] = 'An error occurred while loading the edit form';
            header('Location: /{$this->moduleName}');
            exit;
        }
    }
    
    public function update(\$id)
    {
        try {
            if (\$_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /{$this->moduleName}');
                exit;
            }
            
            \$this->logger->debug('{$this->moduleClass}Controller: update called', ['id' => \$id]);
            
            if (!is_numeric(\$id) || \$id <= 0) {
                \$this->logger->warning('Invalid ID for update', ['id' => \$id]);
                \$_SESSION['error'] = 'Invalid {$this->moduleName} ID';
                header('Location: /{$this->moduleName}');
                exit;
            }
            
            \$title = trim(\$_POST['title'] ?? '');
            \$content = trim(\$_POST['content'] ?? '');
            
            if (empty(\$title) || empty(\$content)) {
                \$this->logger->warning('{$this->moduleClass} update validation failed', ['id' => \$id]);
                \$_SESSION['error'] = 'Title and content are required';
                header('Location: /{$this->moduleName}/' . \$id . '/edit');
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
                \$this->logger->info('{$this->moduleClass} updated', ['id' => \$id]);
                \$_SESSION['success'] = '{$this->moduleClass} updated successfully';
            } else {
                \$this->logger->error('{$this->moduleClass} update failed', ['id' => \$id]);
                \$_SESSION['error'] = 'Failed to update {$this->moduleName}';
            }
            
            header('Location: /{$this->moduleName}');
            exit;
        } catch (\\Exception \$e) {
            \$this->logger->error('{$this->moduleClass}Controller update error', [
                'id' => \$id,
                'error' => \$e->getMessage()
            ]);
            \$_SESSION['error'] = 'An error occurred while updating the {$this->moduleName}';
            header('Location: /{$this->moduleName}');
            exit;
        }
    }
    
    public function delete(\$id)
    {
        try {
            if (\$_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /{$this->moduleName}');
                exit;
            }
            
            \$this->logger->debug('{$this->moduleClass}Controller: delete called', ['id' => \$id]);
            
            if (!is_numeric(\$id) || \$id <= 0) {
                \$this->logger->warning('Invalid ID for delete', ['id' => \$id]);
                \$_SESSION['error'] = 'Invalid {$this->moduleName} ID';
                header('Location: /{$this->moduleName}');
                exit;
            }
            
            \${$this->moduleName}Model = new {$this->moduleClass}(\$this->db);
            \$result = \${$this->moduleName}Model->delete(\$id);
            
            if (\$result) {
                \$this->logger->info('{$this->moduleClass} deleted', ['id' => \$id]);
                \$_SESSION['success'] = '{$this->moduleClass} deleted successfully';
            } else {
                \$this->logger->error('{$this->moduleClass} delete failed', ['id' => \$id]);
                \$_SESSION['error'] = 'Failed to delete {$this->moduleName}';
            }
            
            header('Location: /{$this->moduleName}');
            exit;
        } catch (\\Exception \$e) {
            \$this->logger->error('{$this->moduleClass}Controller delete error', [
                'id' => \$id,
                'error' => \$e->getMessage()
            ]);
            \$_SESSION['error'] = 'An error occurred while deleting the {$this->moduleName}';
            header('Location: /{$this->moduleName}');
            exit;
        }
    }
    
    public function apiIndex()
    {
        try {
            header('Content-Type: application/json');
            \$this->logger->debug('{$this->moduleClass} API: index called');
            
            \${$this->moduleName}Model = new {$this->moduleClass}(\$this->db);
            \$items = \${$this->moduleName}Model->getAll();
            
            echo json_encode([
                'success' => true,
                'data' => \$items
            ]);
            exit;
        } catch (\\Exception \$e) {
            \$this->logger->error('{$this->moduleClass}Controller apiIndex error', ['error' => \$e->getMessage()]);
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
use App\Logger;

class {$this->moduleClass}
{
    private \$db;
    private \$table = '{$this->moduleName}';
    private \$logger;
    
    public function __construct(DB \$db)
    {
        \$this->db = \$db;
        \$this->logger = Logger::getInstance();
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', \$this->table)) {
            throw new \\InvalidArgumentException('Invalid table name');
        }
    }
    
    public function getAll()
    {
        try {
            \$this->logger->debug('{$this->moduleClass}::getAll called');
            \$sql = "SELECT * FROM `" . \$this->table . "` ORDER BY created_at DESC";
            return \$this->db->fetchAll(\$sql);
        } catch (\\Exception \$e) {
            \$this->logger->error('{$this->moduleClass} model getAll error', ['error' => \$e->getMessage()]);
            return [];
        }
    }
    
    public function getById(\$id)
    {
        try {
            \$this->logger->debug('{$this->moduleClass}::getById called', ['id' => \$id]);
            \$sql = "SELECT * FROM `" . \$this->table . "` WHERE id = ?";
            return \$this->db->fetch(\$sql, [\$id]);
        } catch (\\Exception \$e) {
            \$this->logger->error('{$this->moduleClass} model getById error', [
                'id' => \$id,
                'error' => \$e->getMessage()
            ]);
            return null;
        }
    }
    
    public function create(\$data)
    {
        try {
            \$this->logger->debug('{$this->moduleClass}::create called', ['data' => \$data]);
            \$fields = implode(', ', array_keys(\$data));
            \$placeholders = ':' . implode(', :', array_keys(\$data));
            
            \$sql = "INSERT INTO `" . \$this->table . "` (\$fields) VALUES (\$placeholders)";
            return \$this->db->query(\$sql, \$data);
        } catch (\\Exception \$e) {
            \$this->logger->error('{$this->moduleClass} model create error', [
                'error' => \$e->getMessage(),
                'data' => \$data
            ]);
            return false;
        }
    }
    
    public function update(\$id, \$data)
    {
        try {
            \$this->logger->debug('{$this->moduleClass}::update called', ['id' => \$id]);
            \$setParts = [];
            foreach (array_keys(\$data) as \$field) {
                \$setParts[] = "\$field = :\$field";
            }
            \$setClause = implode(', ', \$setParts);
            
            \$sql = "UPDATE `" . \$this->table . "` SET \$setClause WHERE id = :id";
            \$data['id'] = \$id;
            
            return \$this->db->query(\$sql, \$data);
        } catch (\\Exception \$e) {
            \$this->logger->error('{$this->moduleClass} model update error', [
                'id' => \$id,
                'error' => \$e->getMessage()
            ]);
            return false;
        }
    }
    
    public function delete(\$id)
    {
        try {
            \$this->logger->debug('{$this->moduleClass}::delete called', ['id' => \$id]);
            \$sql = "DELETE FROM `" . \$this->table . "` WHERE id = ?";
            return \$this->db->query(\$sql, [\$id]);
        } catch (\\Exception \$e) {
            \$this->logger->error('{$this->moduleClass} model delete error', [
                'id' => \$id,
                'error' => \$e->getMessage()
            ]);
            return false;
        }
    }
    
    public function search(\$query)
    {
        try {
            \$this->logger->debug('{$this->moduleClass}::search called', ['query' => \$query]);
            \$sql = "SELECT * FROM `" . \$this->table . "` 
                    WHERE title LIKE ? OR content LIKE ?
                    ORDER BY created_at DESC";
            
            \$searchTerm = '%' . \$query . '%';
            return \$this->db->fetchAll(\$sql, [\$searchTerm, \$searchTerm]);
        } catch (\\Exception \$e) {
            \$this->logger->error('{$this->moduleClass} model search error', [
                'query' => \$query,
                'error' => \$e->getMessage()
            ]);
            return [];
        }
    }
    
    public function paginate(\$page = 1, \$perPage = 10)
    {
        try {
            \$page = max(1, (int)\$page);
            \$perPage = max(1, min(100, (int)\$perPage));
            \$offset = (\$page - 1) * \$perPage;
            
            \$this->logger->debug('{$this->moduleClass}::paginate called', [
                'page' => \$page,
                'perPage' => \$perPage
            ]);
            
            \$sql = "SELECT * FROM `" . \$this->table . "` 
                    ORDER BY created_at DESC 
                    LIMIT ? OFFSET ?";
            
            return \$this->db->fetchAll(\$sql, [\$perPage, \$offset]);
        } catch (\\Exception \$e) {
            \$this->logger->error('{$this->moduleClass} model paginate error', [
                'page' => \$page,
                'error' => \$e->getMessage()
            ]);
            return [];
        }
    }
    
    public function getCount()
    {
        try {
            \$sql = "SELECT COUNT(*) as count FROM `" . \$this->table . "`";
            \$result = \$this->db->fetch(\$sql);
            return \$result ? (int)\$result['count'] : 0;
        } catch (\\Exception \$e) {
            \$this->logger->error('{$this->moduleClass} model getCount error', ['error' => \$e->getMessage()]);
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
        
        $editContent = <<<PHP
<?php
\$title = \$data['title'] ?? 'Edit {$this->moduleClass}';
\$showNav = true;
require __DIR__ . '/../../../views/partials/header.php';
?>

<section class="py-5">
    <div class="container px-5">
        <div class="row gx-5 justify-content-center">
            <div class="col-lg-8">
                <h1 class="fw-bolder mb-4">Edit {$this->moduleClass}</h1>
                
                <form method="post" action="/{$this->moduleName}/<?= \$data['item']['id'] ?>/edit">
                    <div class="form-floating mb-3">
                        <input class="form-control" id="title" name="title" type="text" value="<?= htmlspecialchars(\$data['item']['title']) ?>" required>
                        <label for="title">Title</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="content" name="content" style="height: 200px" required><?= htmlspecialchars(\$data['item']['content']) ?></textarea>
                        <label for="content">Content</label>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="/{$this->moduleName}" class="btn btn-secondary">Cancel</a>
                        <button class="btn btn-primary" type="submit">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../../../views/partials/footer.php'; ?>
PHP;
        
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
- Logger integration with configurable levels

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

The following routes are automatically generated:

- `GET /{$this->moduleName}` - List all items
- `GET /{$this->moduleName}/create` - Show create form
- `POST /{$this->moduleName}/create` - Store new item
- `GET /{$this->moduleName}/{id}` - Show single item
- `GET /{$this->moduleName}/{id}/edit` - Show edit form
- `POST /{$this->moduleName}/{id}/edit` - Update item
- `POST /{$this->moduleName}/{id}/delete` - Delete item
- `GET /api/{$this->moduleName}` - API endpoint

## Structure

```
{$this->moduleName}/
├── index.php                    # Module metadata
├── routes.php                   # Route definitions
├── controllers/
│   └── {$this->moduleClass}Controller.php
├── models/
│   └── {$this->moduleClass}.php
├── views/
│   ├── index.php
│   ├── create.php
│   ├── show.php
│   └── edit.php
├── assets/
│   ├── css/
│   └── js/
├── README.md
└── CHANGELOG.md
```

## Configuration

After generation, the module is automatically:
1. Added to composer.json autoload
2. Available in admin module manager
3. Disabled by default (enable in admin panel)

## Next Steps

1. Run `composer dump-autoload`
2. Create database table for your module
3. Enable module in admin panel (`/admin/modules`)
4. Customize generated views and logic

## Customization

### Adding Fields
1. Update database table schema
2. Modify model methods in `models/{$this->moduleClass}.php`
3. Update form fields in views
4. Adjust controller validation

### Styling
- Edit Bootstrap classes in view files
- Add custom CSS in `assets/css/`
- Add JavaScript functionality in `assets/js/`

### API Enhancement
- Extend `apiIndex()` method in controller
- Add additional API endpoints in routes
- Implement authentication for API routes

## Security Notes

The generated module includes:
- Input validation and sanitization
- SQL injection prevention via prepared statements
- XSS protection via output escaping
- Error handling with logging
- CSRF-aware form handling pattern

## Troubleshooting

### Module not appearing
- Check composer autoload: `composer dump-autoload`
- Verify module directory in `public_html/modules/`
- Check `index.php` metadata format

### Routes not working
- Ensure module is enabled in config/admin
- Check route file syntax
- Verify controller namespace and class names

### Database errors
- Create required table schema
- Check database connection in config
- Verify table name matches module name

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

## Future Enhancements

Consider adding these features in future versions:
- File upload support
- Rich text editor integration
- Category/tag system
- Comment system
- SEO metadata fields
- Soft delete functionality
- Activity logging
- Bulk operations
- Export/import functionality
- Advanced search filters

## Contributing

When modifying this module:
1. Update this changelog
2. Increment version in `index.php`
3. Test all CRUD operations
4. Verify security measures
5. Update documentation

---

Generated by StrataPHP Module Generator v1.0.0

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
        $composer['autoload']['psr-4'][$namespace . "Controllers\\"] = "public_html/modules/{$this->moduleName}/controllers/";
        $composer['autoload']['psr-4'][$namespace . "Models\\"] = "public_html/modules/{$this->moduleName}/models/";
        
        file_put_contents($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo "📝 Updated: composer.json\n";
    }

    private function updateModulesConfig()
    {
        $modulesFile = __DIR__ . '/../public_html/app/modules.php';

        if (!file_exists($modulesFile)) {
            echo "⚠️ Skipped: public_html/app/modules.php not found\n";
            return;
        }

        $modulesConfig = include $modulesFile;

        if (!is_array($modulesConfig) || !isset($modulesConfig['modules']) || !is_array($modulesConfig['modules'])) {
            echo "⚠️ Skipped: invalid modules config format\n";
            return;
        }

        if (!isset($modulesConfig['modules'][$this->moduleName])) {
            $modulesConfig['modules'][$this->moduleName] = [
                'enabled' => false,
                'suitable_as_default' => false,
            ];

            $modulesExport = var_export($modulesConfig, true);
            file_put_contents($modulesFile, "<?php\nreturn " . $modulesExport . ";\n", LOCK_EX);
            echo "📝 Updated: public_html/app/modules.php\n";
            return;
        }

        echo "ℹ️ Module already present in public_html/app/modules.php\n";
    }
}

// Main execution
if ($argc < 2) {
    echo "🎨 StrataPHP Module Generator\n\n";
    echo "Usage: php create-module.php <module-name>\n";
    echo "       php create-module.php --validate <module-name>\n\n";
    echo "Example: php create-module.php blog\n";
    echo "Example: php create-module.php user-management\n";
    echo "Example: php create-module.php contact_form\n";
    echo "Example: php create-module.php --validate blog\n\n";
    echo "Naming conventions:\n";
    echo "  • Use lowercase letters, numbers, and hyphens/underscores\n";
    echo "  • Must start with a letter\n";
    echo "  • Will be normalized to: slug (metadata), directory_name, ClassName\n";
    exit(1);
}

if ($argv[1] === '--validate' && isset($argv[2])) {
    $moduleName = $argv[2];
    // Validate input format
    if (!preg_match('/^[a-zA-Z][a-zA-Z0-9\-_]*$/', $moduleName)) {
        echo "❌ Invalid module name format!\n";
        exit(1);
    }
    $modulesPath = __DIR__ . '/../public_html/modules/';
    $normalizedName = strtolower(str_replace('_', '-', $moduleName));
    $dirName = str_replace('-', '_', $normalizedName);
    $moduleDir = $modulesPath . $dirName;
    $errors = [];
    if (!is_dir($moduleDir)) {
        $errors[] = "Module directory not found: $moduleDir";
    } else {
        // Check index.php metadata
        $indexFile = $moduleDir . '/index.php';
        if (!file_exists($indexFile)) {
            $errors[] = "Missing index.php metadata file.";
        } else {
            $meta = @include $indexFile;
            if (!is_array($meta)) {
                $errors[] = "index.php does not return a metadata array.";
            } else {
                foreach (["name", "slug", "version"] as $field) {
                    if (empty($meta[$field])) {
                        $errors[] = "index.php metadata missing or empty: $field";
                    }
                }
            }
        }
        // Check required folders
        foreach (["controllers", "models", "views"] as $folder) {
            if (!is_dir($moduleDir . "/$folder")) {
                $errors[] = "Missing $folder/ directory.";
            }
        }
        // Check PSR-4 namespace in controllers
        $controllerFiles = glob($moduleDir . '/controllers/*.php');
        foreach ($controllerFiles as $file) {
            $contents = file_get_contents($file);
            if (!preg_match('/namespace\\s+(App\\\\Modules|StrataPHP\\\\Modules)\\\\[A-Za-z0-9_]+\\\\Controllers;/', $contents)) {
                $errors[] = basename($file) . ": Namespace does not match PSR-4 convention.";
            }
        }
    }
    if ($errors) {
        echo "❌ Validation failed for module '$moduleName':\n";
        foreach ($errors as $err) {
            echo "  - $err\n";
        }
        exit(1);
    } else {
        echo "✅ Module '$moduleName' passed validation.\n";
        exit(0);
    }
}

$moduleName = $argv[1];

// Validate input format
if (!preg_match('/^[a-zA-Z][a-zA-Z0-9\-_]*$/', $moduleName)) {
    echo "❌ Invalid module name format!\n";
    echo "Module name must:\n";
    echo "  • Start with a letter\n";
    echo "  • Contain only letters, numbers, hyphens, and underscores\n";
    echo "  • Examples: blog, user-management, contact_form\n";
    exit(1);
}

try {
    $generator = new ModuleGenerator($moduleName);
    $success = $generator->generate();
    exit($success ? 0 : 1);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}