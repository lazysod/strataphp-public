<?php
namespace App\Modules\ModuleDirectory\Controllers;

use App\DB;
use App\Modules\ModuleDirectory\Models\ModuleDirectory;

class ModuleDirectoryController
{
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
            $module_directoryModel = new ModuleDirectory($this->db);
            $items = $module_directoryModel->getAll();
            
            $data = [
                'items' => $items,
                'title' => 'ModuleDirectory'
            ];
            
            include __DIR__ . '/../views/index.php';
        } catch (\Exception $e) {
            error_log("ModuleDirectoryController index error: " . $e->getMessage());
            http_response_code(500);
            echo 'An error occurred while loading the module_directory.';
        }
    }
    
    /**
     * Show the form for creating a new resource
     */
    public function create()
    {
        try {
            $data = [
                'title' => 'Create ModuleDirectory'
            ];
            
            include __DIR__ . '/../views/create.php';
        } catch (\Exception $e) {
            error_log("ModuleDirectoryController create error: " . $e->getMessage());
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
                header('Location: /module_directory');
                exit;
            }
            
            // Basic validation
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            
            if (empty($title) || empty($content)) {
                $_SESSION['error'] = 'Title and content are required';
                header('Location: /module_directory/create');
                exit;
            }
            
            $module_directoryModel = new ModuleDirectory($this->db);
            
            $data = [
                'title' => $title,
                'content' => $content,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $module_directoryModel->create($data);
            
            if ($result) {
                $_SESSION['success'] = 'ModuleDirectory created successfully';
            } else {
                $_SESSION['error'] = 'Failed to create module_directory';
            }
            
            header('Location: /module_directory');
            exit;
        } catch (\Exception $e) {
            error_log("ModuleDirectoryController store error: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while creating the module_directory';
            header('Location: /module_directory/create');
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
                echo '404 - Invalid module_directory ID';
                exit;
            }
            
            $module_directoryModel = new ModuleDirectory($this->db);
            $item = $module_directoryModel->getById($id);
            
            if (!$item) {
                header('HTTP/1.0 404 Not Found');
                echo '404 - ModuleDirectory not found';
                exit;
            }
            
            $data = [
                'item' => $item,
                'title' => $item['title']
            ];
            
            include __DIR__ . '/../views/show.php';
        } catch (\Exception $e) {
            error_log("ModuleDirectoryController show error: " . $e->getMessage());
            http_response_code(500);
            echo 'An error occurred while loading the module_directory.';
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
                header('Location: /module_directory');
                exit;
            }
            
            $module_directoryModel = new ModuleDirectory($this->db);
            $item = $module_directoryModel->getById($id);
            
            if (!$item) {
                $_SESSION['error'] = 'ModuleDirectory not found';
                header('Location: /module_directory');
                exit;
            }
            
            $data = [
                'item' => $item,
                'title' => 'Edit ModuleDirectory'
            ];
            
            include __DIR__ . '/../views/edit.php';
        } catch (\Exception $e) {
            error_log("ModuleDirectoryController edit error: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while loading the edit form';
            header('Location: /module_directory');
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
                header('Location: /module_directory');
                exit;
            }
            
            // Validate ID
            if (!is_numeric($id) || $id <= 0) {
                $_SESSION['error'] = 'Invalid module_directory ID';
                header('Location: /module_directory');
                exit;
            }
            
            // Basic validation
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            
            if (empty($title) || empty($content)) {
                $_SESSION['error'] = 'Title and content are required';
                header('Location: /module_directory/{$id}/edit');
                exit;
            }
            
            $module_directoryModel = new ModuleDirectory($this->db);
            
            $data = [
                'title' => $title,
                'content' => $content,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $module_directoryModel->update($id, $data);
            
            if ($result) {
                $_SESSION['success'] = 'ModuleDirectory updated successfully';
            } else {
                $_SESSION['error'] = 'Failed to update module_directory';
            }
            
            header('Location: /module_directory');
            exit;
        } catch (\Exception $e) {
            error_log("ModuleDirectoryController update error: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while updating the module_directory';
            header('Location: /module_directory');
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
                header('Location: /module_directory');
                exit;
            }
            
            // Validate ID
            if (!is_numeric($id) || $id <= 0) {
                $_SESSION['error'] = 'Invalid module_directory ID';
                header('Location: /module_directory');
                exit;
            }
            
            $module_directoryModel = new ModuleDirectory($this->db);
            $result = $module_directoryModel->delete($id);
            
            if ($result) {
                $_SESSION['success'] = 'ModuleDirectory deleted successfully';
            } else {
                $_SESSION['error'] = 'Failed to delete module_directory';
            }
            
            header('Location: /module_directory');
            exit;
        } catch (\Exception $e) {
            error_log("ModuleDirectoryController delete error: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while deleting the module_directory';
            header('Location: /module_directory');
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
            
            $module_directoryModel = new ModuleDirectory($this->db);
            $items = $module_directoryModel->getAll();
            
            echo json_encode([
                'success' => true,
                'data' => $items
            ]);
            exit;
        } catch (\Exception $e) {
            error_log("ModuleDirectoryController apiIndex error: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while fetching module_directory'
            ]);
            exit;
        }
    }
}