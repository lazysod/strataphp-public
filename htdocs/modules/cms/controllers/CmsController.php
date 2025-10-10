<?php
namespace App\Modules\Cms\Controllers;

    use App\DB;
    use App\Modules\Cms\Models\Cms;

    class CmsController
    {
        /**
         * Set a page as the root (home) page
         */
        public function setHome($id)
        {
            try {
                // Unset all other home pages
                $this->db->query('UPDATE cms_pages SET is_home = 0');
                // Set this page as home
                $this->db->query('UPDATE cms_pages SET is_home = 1 WHERE id = ?', [$id]);
                $_SESSION['success'] = 'Home page updated!';
                header('Location: /admin/cms/pages/' . $id . '/edit');
                exit;
            } catch (\Exception $e) {
                error_log('CmsController setHome error: ' . $e->getMessage());
                $_SESSION['error'] = 'Failed to set home page.';
                header('Location: /admin/cms/pages/' . $id . '/edit');
                exit;
            }
        }
    private $db;
    private $config;
    
    public function __construct()
    {
        $this->config = include dirname(__DIR__, 3) . '/app/config.php';
        $this->db = new DB($this->config);
    }
    
    /**
     * Display a listing of the resource
     */
    public function index()
    {
        try {
            $cmsModel = new Cms($this->db);
            $items = $cmsModel->getAll();
            
            $data = [
                'items' => $items,
                'title' => 'Cms'
            ];
            
            include __DIR__ . '/../views/index.php';
        } catch (\Exception $e) {
            error_log("CmsController index error: " . $e->getMessage());
            http_response_code(500);
            echo 'An error occurred while loading the cms.';
        }
    }
    
    /**
     * Show the form for creating a new resource
     */
    public function create()
    {
        try {
            $data = [
                'title' => 'Create Cms'
            ];
            
            include __DIR__ . '/../views/create.php';
        } catch (\Exception $e) {
            error_log("CmsController create error: " . $e->getMessage());
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
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /cms');
                exit;
            }
            
            // Basic validation
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            
            if (empty($title) || empty($content)) {
                $_SESSION['error'] = 'Title and content are required';
                header('Location: /cms/create');
                exit;
            }
            
            $cmsModel = new Cms($this->db);
            
            $data = [
                'title' => $title,
                'content' => $content,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $cmsModel->create($data);
            
            if ($result) {
                $_SESSION['success'] = 'Cms created successfully';
            } else {
                $_SESSION['error'] = 'Failed to create cms';
            }
            
            header('Location: /cms');
            exit;
        } catch (\Exception $e) {
            error_log("CmsController store error: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while creating the cms';
            header('Location: /cms/create');
            exit;
        }
    }
    
    /**
     * Display the specified resource
     */
    public function show($id)
    {
        try {
            // Validate ID
            if (!is_numeric($id) || $id <= 0) {
                header('HTTP/1.0 404 Not Found');
                echo '404 - Invalid cms ID';
                exit;
            }
            
            $cmsModel = new Cms($this->db);
            $item = $cmsModel->getById($id);
            
            if (!$item) {
                header('HTTP/1.0 404 Not Found');
                echo '404 - Cms not found';
                exit;
            }
            
            $data = [
                'item' => $item,
                'title' => $item['title']
            ];
            
            include __DIR__ . '/../views/show.php';
        } catch (\Exception $e) {
            error_log("CmsController show error: " . $e->getMessage());
            http_response_code(500);
            echo 'An error occurred while loading the cms.';
        }
    }
    
    /**
     * Show the form for editing the specified resource
     */
    public function edit($id)
    {
        try {
            // Validate ID
            if (!is_numeric($id) || $id <= 0) {
                header('Location: /cms');
                exit;
            }
            
            $cmsModel = new Cms($this->db);
            $item = $cmsModel->getById($id);
            
            if (!$item) {
                $_SESSION['error'] = 'Cms not found';
                header('Location: /cms');
                exit;
            }
            
            $data = [
                'item' => $item,
                'title' => 'Edit Cms'
            ];
            
            include __DIR__ . '/../views/edit.php';
        } catch (\Exception $e) {
            error_log("CmsController edit error: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while loading the edit form';
            header('Location: /cms');
            exit;
        }
    }
    
    /**
     * Update the specified resource
     */
    public function update($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /cms');
                exit;
            }
            
            // Validate ID
            if (!is_numeric($id) || $id <= 0) {
                $_SESSION['error'] = 'Invalid cms ID';
                header('Location: /cms');
                exit;
            }
            
            // Basic validation
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            
            if (empty($title) || empty($content)) {
                $_SESSION['error'] = 'Title and content are required';
                header('Location: /cms/{$id}/edit');
                exit;
            }
            
            $cmsModel = new Cms($this->db);
            
            $data = [
                'title' => $title,
                'content' => $content,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $cmsModel->update($id, $data);
            
            if ($result) {
                $_SESSION['success'] = 'Cms updated successfully';
            } else {
                $_SESSION['error'] = 'Failed to update cms';
            }
            
            header('Location: /cms');
            exit;
        } catch (\Exception $e) {
            error_log("CmsController update error: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while updating the cms';
            header('Location: /cms');
            exit;
        }
    }
    
    /**
     * Remove the specified resource
     */
    public function delete($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /cms');
                exit;
            }
            
            // Validate ID
            if (!is_numeric($id) || $id <= 0) {
                $_SESSION['error'] = 'Invalid cms ID';
                header('Location: /cms');
                exit;
            }
            
            $cmsModel = new Cms($this->db);
            $result = $cmsModel->delete($id);
            
            if ($result) {
                $_SESSION['success'] = 'Cms deleted successfully';
            } else {
                $_SESSION['error'] = 'Failed to delete cms';
            }
            
            header('Location: /cms');
            exit;
        } catch (\Exception $e) {
            error_log("CmsController delete error: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while deleting the cms';
            header('Location: /cms');
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
            
            $cmsModel = new Cms($this->db);
            $items = $cmsModel->getAll();
            
            echo json_encode([
                'success' => true,
                'data' => $items
            ]);
            exit;
        } catch (\Exception $e) {
            error_log("CmsController apiIndex error: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while fetching cms'
            ]);
            exit;
        }
    }
}