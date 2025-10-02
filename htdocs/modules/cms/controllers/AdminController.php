<?php
namespace App\Modules\Cms\Controllers;

use App\DB;
use App\Modules\Cms\Models\Page;
use App\SessionManager;

class AdminController
{
    private $db;
    private $config;
    
    public function __construct()
    {
        // Define the constant to allow access to CMS view files
        if (!defined('STRPHP_ROOT')) {
            define('STRPHP_ROOT', true);
        }
        
        $this->config = include dirname(__DIR__, 3) . '/app/config.php';
        $this->db = new DB($this->config);
        
        // Ensure user is authenticated and has admin access
        $this->requireAuth();
    }
    
    /**
     * Require authentication for admin access
     */
    private function requireAuth()
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Get session prefix from config
        $sessionPrefix = $this->config['session_prefix'] ?? 'app_';
        
        // Check if user is logged in as admin using StrataPHP's session structure
        if (!isset($_SESSION[$sessionPrefix . 'admin']) || $_SESSION[$sessionPrefix . 'admin'] < 1) {
            header('Location: /admin/admin_login.php');
            exit;
        }
    }
    
    /**
     * CMS Dashboard
     */
    public function dashboard()
    {
        try {
            $pageModel = new Page($this->config);
            
            // Get statistics
            $stats = [
                'total_pages' => $this->getPageCount(),
                'published_pages' => $this->getPageCount('published'),
                'draft_pages' => $this->getPageCount('draft'),
                'recent_pages' => $pageModel->getAll(5)
            ];
            
            $data = [
                'title' => 'CMS Dashboard',
                'stats' => $stats
            ];
            
            $this->renderAdminView('dashboard', $data);
        } catch (\Exception $e) {
            error_log("AdminController dashboard error: " . $e->getMessage());
            $this->showError('Unable to load the CMS dashboard.');
        }
    }
    
    /**
     * List all pages
     */
    public function pages()
    {
        try {
            $pageModel = new Page($this->config);
            $pages = $pageModel->getAll();
            
            $data = [
                'title' => 'Manage Pages',
                'pages' => $pages
            ];
            
            $this->renderAdminView('pages', $data);
        } catch (\Exception $e) {
            error_log("AdminController pages error: " . $e->getMessage());
            $this->showError('Unable to load pages.');
        }
    }
    
    /**
     * Show create page form
     */
    public function createPage()
    {
        $data = [
            'title' => 'Create New Page',
            'page' => null
        ];
        
        $this->renderAdminView('page_form', $data);
    }
    
    /**
     * Store new page
     */
    public function storePage()
    {
        try {
            $pageModel = new Page($this->config);
            
            $data = [
                'title' => $_POST['title'] ?? '',
                'slug' => $_POST['slug'] ?? '',
                'content' => $_POST['content'] ?? '',
                'excerpt' => $_POST['excerpt'] ?? '',
                'meta_title' => $_POST['meta_title'] ?? '',
                'meta_description' => $_POST['meta_description'] ?? '',
                'og_image' => $_POST['og_image'] ?? '',
                'og_type' => $_POST['og_type'] ?? 'article',
                'twitter_card' => $_POST['twitter_card'] ?? 'summary_large_image',
                'canonical_url' => $_POST['canonical_url'] ?? '',
                'noindex' => isset($_POST['noindex']) ? 1 : 0,
                'status' => $_POST['status'] ?? 'draft',
                'template' => $_POST['template'] ?? 'default'
            ];
            
            // Validate required fields
            if (empty($data['title'])) {
                throw new \Exception('Page title is required.');
            }
            
            $pageId = $pageModel->create($data);
            
            if ($pageId) {
                $_SESSION['success'] = 'Page created successfully.';
                header('Location: /admin/cms/pages');
            } else {
                throw new \Exception('Failed to create page.');
            }
        } catch (\Exception $e) {
            error_log("AdminController storePage error: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/cms/pages/create');
        }
        exit;
    }
    
    /**
     * Show edit page form
     */
    public function editPage($id)
    {
        try {
            $pageModel = new Page($this->config);
            $page = $pageModel->getById($id);
            
            if (!$page) {
                $_SESSION['error'] = 'Page not found.';
                header('Location: /admin/cms/pages');
                exit;
            }

            $data = [
                'title' => 'Edit Page',
                'page' => $page
            ];
            
            $this->renderAdminView('page_form', $data);
        } catch (\Exception $e) {
            error_log("AdminController editPage error: " . $e->getMessage());
            $this->showError('Unable to load page for editing.');
        }
    }    /**
     * Update existing page
     */
    public function updatePage($id)
    {
        try {
            $pageModel = new Page($this->config);
            
            $data = [
                'title' => $_POST['title'] ?? '',
                'slug' => $_POST['slug'] ?? '',
                'content' => $_POST['content'] ?? '',
                'excerpt' => $_POST['excerpt'] ?? '',
                'meta_title' => $_POST['meta_title'] ?? '',
                'meta_description' => $_POST['meta_description'] ?? '',
                'og_image' => $_POST['og_image'] ?? '',
                'og_type' => $_POST['og_type'] ?? 'article',
                'twitter_card' => $_POST['twitter_card'] ?? 'summary_large_image',
                'canonical_url' => $_POST['canonical_url'] ?? '',
                'noindex' => isset($_POST['noindex']) ? 1 : 0,
                'status' => $_POST['status'] ?? 'draft',
                'template' => $_POST['template'] ?? 'default',
                'menu_order' => $_POST['menu_order'] ?? 0,
                'author_id' => $_SESSION['user_id'] ?? 1
            ];
            
            // Validate required fields
            if (empty($data['title'])) {
                throw new \Exception('Page title is required.');
            }
            
            $success = $pageModel->update($id, $data);
            
            if ($success) {
                $_SESSION['success'] = 'Page updated successfully.';
                header('Location: /admin/cms/pages');
            } else {
                throw new \Exception('Failed to update page.');
            }
        } catch (\Exception $e) {
            error_log("AdminController updatePage error: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/cms/pages/' . $id . '/edit');
        }
        exit;
    }
    
    /**
     * Delete page
     */
    public function deletePage($id)
    {
        try {
            $pageModel = new Page($this->config);
            $success = $pageModel->delete($id);
            
            if ($success) {
                $_SESSION['success'] = 'Page deleted successfully.';
            } else {
                $_SESSION['error'] = 'Failed to delete page.';
            }
        } catch (\Exception $e) {
            error_log("AdminController deletePage error: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while deleting the page.';
        }
        
        header('Location: /admin/cms/pages');
        exit;
    }
    
    /**
     * Get page count by status
     */
    private function getPageCount($status = null)
    {
        if ($status) {
            $result = $this->db->fetch("SELECT COUNT(*) as count FROM cms_pages WHERE status = ?", [$status]);
        } else {
            $result = $this->db->fetch("SELECT COUNT(*) as count FROM cms_pages");
        }
        
        return $result ? $result['count'] : 0;
    }
    
    /**
     * Render admin view template
     */
    private function renderAdminView($template, $data = [])
    {
        $templatePath = dirname(__DIR__) . '/views/admin/' . $template . '.php';
        
        if (file_exists($templatePath)) {
            // Extract data for template
            extract($data);
            include $templatePath;
        } else {
            // Fallback to simple output
            echo $this->renderSimpleAdminPage($data);
        }
    }
    
    /**
     * Render simple admin page as fallback
     */
    private function renderSimpleAdminPage($data)
    {
        $title = htmlspecialchars($data['title'] ?? 'CMS Admin');
        
        $content = '<h1>' . $title . '</h1>';
        
        if (isset($data['pages'])) {
            $content .= '<div class="pages-list">';
            $content .= '<a href="/admin/cms/pages/create" class="btn btn-primary">Create New Page</a>';
            $content .= '<table class="table">';
            $content .= '<thead><tr><th>Title</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>';
            $content .= '<tbody>';
            
            foreach ($data['pages'] as $page) {
                $content .= '<tr>';
                $content .= '<td>' . htmlspecialchars($page['title']) . '</td>';
                $content .= '<td>' . htmlspecialchars($page['status']) . '</td>';
                $content .= '<td>' . htmlspecialchars($page['created_at']) . '</td>';
                $content .= '<td>';
                $content .= '<a href="/admin/cms/pages/' . $page['id'] . '/edit">Edit</a> | ';
                $content .= '<form method="POST" action="/admin/cms/pages/' . $page['id'] . '/delete" style="display:inline;">';
                $content .= '<button type="submit" onclick="return confirm(\'Are you sure?\')">Delete</button>';
                $content .= '</form>';
                $content .= '</td>';
                $content .= '</tr>';
            }
            
            $content .= '</tbody></table>';
            $content .= '</div>';
        }
        
        return "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{$title}</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; line-height: 1.6; }
        .btn { padding: 8px 16px; background: #007cba; color: white; text-decoration: none; border-radius: 4px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        .table th { background: #f5f5f5; }
    </style>
</head>
<body>
    {$content}
</body>
</html>";
    }
    
    /**
     * Show error page
     */
    private function showError($message)
    {
        http_response_code(500);
        
        $data = [
            'title' => 'Admin Error',
            'content' => '<h1>Error</h1><p>' . htmlspecialchars($message) . '</p>'
        ];
        
        echo $this->renderSimpleAdminPage($data);
    }
    
    /**
     * Check if slug is available (AJAX endpoint)
     */
    public function checkSlug()
    {
        header('Content-Type: application/json');
        
        try {
            $slug = $_GET['slug'] ?? '';
            $excludeId = $_GET['exclude_id'] ?? null;
            
            if (empty($slug)) {
                echo json_encode(['available' => false, 'message' => 'Slug cannot be empty']);
                return;
            }
            
            $pageModel = new Page($this->config);
            
            // Check for route conflicts with existing static routes
            $conflictRoutes = [
                'admin', 'user', 'api', 'about', 'contact', 'login', 'logout',
                'register', 'dashboard', 'modules', 'links', 'forum'
            ];
            
            if (in_array($slug, $conflictRoutes)) {
                echo json_encode([
                    'available' => false, 
                    'message' => 'This slug conflicts with system routes'
                ]);
                return;
            }
            
            $available = $pageModel->isSlugAvailable($slug, $excludeId);
            
            echo json_encode([
                'available' => $available,
                'message' => $available ? 'Slug is available' : 'Slug already exists'
            ]);
            
        } catch (\Exception $e) {
            echo json_encode(['available' => false, 'message' => 'Error checking slug']);
        }
    }
}