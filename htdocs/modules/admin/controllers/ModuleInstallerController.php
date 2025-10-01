<?php
namespace App\Modules\Admin\Controllers;

use App\DB;
use App\View;
use App\App;

class ModuleInstallerController
{
    private $db;
    private $config;
    private $view;
    
    public function __construct()
    {
        $this->config = include dirname(__DIR__, 3) . '/app/config.php';
        $this->db = new DB($this->config);
        $this->view = new View($this->config);
    }
    
    /**
     * Show the module installer interface
     */
    public function index()
    {
        // Check admin authentication
        if (!$this->isAuthenticated()) {
            header('Location: /admin/admin_login.php');
            exit;
        }
        
        $data = [
            'title' => 'Module Installer',
            'modules' => $this->getInstalledModules(),
            'maxFileSize' => $this->getMaxUploadSize(),
            'tempDir' => sys_get_temp_dir(),
            'controller' => $this
        ];
        
        // Include the view directly since it's in the module's views directory
        extract($data);
        include dirname(__DIR__) . '/views/module-installer.php';
    }
    
    /**
     * Handle ZIP file upload and installation
     */
    public function uploadInstall()
    {
        if (!$this->isAuthenticated()) {
            return $this->jsonResponse(['success' => false, 'message' => 'Unauthorized']);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }
        
        // Validate CSRF token
        if (!$this->validateCsrfToken()) {
            return $this->jsonResponse(['success' => false, 'message' => 'Invalid CSRF token']);
        }
        
        try {
            // Check if file was uploaded
            if (!isset($_FILES['module_zip']) || $_FILES['module_zip']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('No file uploaded or upload error occurred');
            }
            
            $uploadedFile = $_FILES['module_zip'];
            
            // Validate file type
            if (!$this->isValidZipFile($uploadedFile)) {
                throw new \Exception('Invalid file type. Only ZIP files are allowed.');
            }
            
            // Create temporary directory for extraction
            $tempDir = $this->createTempDirectory();
            $zipPath = $tempDir . '/' . basename($uploadedFile['name']);
            
            // Move uploaded file to temp directory
            if (!move_uploaded_file($uploadedFile['tmp_name'], $zipPath)) {
                throw new \Exception('Failed to move uploaded file');
            }
            
            // Extract ZIP file
            $extractDir = $tempDir . '/extracted';
            if (!$this->extractZipFile($zipPath, $extractDir)) {
                throw new \Exception('Failed to extract ZIP file');
            }
            
            // Find module directory in extracted files
            $modulePath = $this->findModuleDirectory($extractDir);
            if (!$modulePath) {
                throw new \Exception('Invalid module structure. Could not find module directory.');
            }
            
            // Install the module using our existing installer
            $result = $this->installModuleFromPath($modulePath);
            
            // Clean up temporary files
            $this->cleanupTempDirectory($tempDir);
            
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Module installed successfully!',
                'module' => $result
            ]);
            
        } catch (\Exception $e) {
            // Clean up on error
            if (isset($tempDir)) {
                $this->cleanupTempDirectory($tempDir);
            }
            
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Installation failed: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Install module from URL (GitHub, ZIP URL, etc.)
     */
    public function urlInstall()
    {
        if (!$this->isAuthenticated()) {
            return $this->jsonResponse(['success' => false, 'message' => 'Unauthorized']);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }
        
        // Validate CSRF token
        if (!$this->validateCsrfToken()) {
            return $this->jsonResponse(['success' => false, 'message' => 'Invalid CSRF token']);
        }
        
        try {
            $sourceUrl = $_POST['source_url'] ?? '';
            
            if (empty($sourceUrl)) {
                throw new \Exception('Source URL is required');
            }
            
            // Validate URL format
            if (!filter_var($sourceUrl, FILTER_VALIDATE_URL)) {
                throw new \Exception('Invalid URL format');
            }
            
            // Use the CLI installer script
            $installerPath = dirname(__DIR__, 4) . '/bin/install-module.php';
            $command = "php " . escapeshellarg($installerPath) . " " . escapeshellarg($sourceUrl) . " 2>&1";
            
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);
            
            if ($returnCode !== 0) {
                throw new \Exception('Installation failed: ' . implode("\n", $output));
            }
            
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Module installed successfully!',
                'output' => implode("\n", $output)
            ]);
            
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Installation failed: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Generate a new module using the CLI generator
     */
    public function generateModule()
    {
        if (!$this->isAuthenticated()) {
            return $this->jsonResponse(['success' => false, 'message' => 'Unauthorized']);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }
        
        // Validate CSRF token
        if (!$this->validateCsrfToken()) {
            return $this->jsonResponse(['success' => false, 'message' => 'Invalid CSRF token']);
        }
        
        try {
            $moduleName = $_POST['module_name'] ?? '';
            
            if (empty($moduleName)) {
                throw new \Exception('Module name is required');
            }
            
            // Validate module name
            if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_-]*$/', $moduleName)) {
                throw new \Exception('Invalid module name. Use only letters, numbers, underscores, and hyphens.');
            }
            
            // Use the CLI generator script
            $generatorPath = dirname(__DIR__, 4) . '/bin/create-module.php';
            $command = "php " . escapeshellarg($generatorPath) . " " . escapeshellarg($moduleName) . " 2>&1";
            
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);
            
            if ($returnCode !== 0) {
                throw new \Exception('Generation failed: ' . implode("\n", $output));
            }
            
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Module generated successfully!',
                'output' => implode("\n", $output)
            ]);
            
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Generation failed: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get list of installed modules
     */
    private function getInstalledModules()
    {
        $modules = [];
        $modulesDir = dirname(__DIR__, 3) . '/modules';
        
        if (is_dir($modulesDir)) {
            $dirs = scandir($modulesDir);
            foreach ($dirs as $dir) {
                if ($dir === '.' || $dir === '..') continue;
                
                $moduleDir = $modulesDir . '/' . $dir;
                $indexFile = $moduleDir . '/index.php';
                
                if (is_dir($moduleDir) && file_exists($indexFile)) {
                    $moduleData = include $indexFile;
                    $moduleData['directory'] = $dir;
                    $moduleData['enabled'] = $this->config['modules'][$dir]['enabled'] ?? false;
                    $modules[] = $moduleData;
                }
            }
        }
        
        return $modules;
    }
    
    /**
     * Check if user is authenticated as admin
     */
    private function isAuthenticated()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $sessionPrefix = $this->config['session_prefix'] ?? ($this->config['prefix'] ?? 'app_');
        return isset($_SESSION[$sessionPrefix . 'admin']) && $_SESSION[$sessionPrefix . 'admin'] > 0;
    }
    
    /**
     * Validate CSRF token
     */
    private function validateCsrfToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $token = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        
        return !empty($token) && !empty($sessionToken) && hash_equals($sessionToken, $token);
    }
    
    /**
     * Generate CSRF token
     */
    public function getCsrfToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validate ZIP file
     */
    private function isValidZipFile($file)
    {
        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($extension !== 'zip') {
            return false;
        }
        
        // Check MIME type
        $allowedMimes = ['application/zip', 'application/x-zip-compressed'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        return in_array($mimeType, $allowedMimes);
    }
    
    /**
     * Create temporary directory
     */
    private function createTempDirectory()
    {
        $tempDir = sys_get_temp_dir() . '/strataphp_module_' . uniqid();
        if (!mkdir($tempDir, 0755, true)) {
            throw new \Exception('Failed to create temporary directory');
        }
        return $tempDir;
    }
    
    /**
     * Extract ZIP file
     */
    private function extractZipFile($zipPath, $extractDir)
    {
        $zip = new \ZipArchive();
        $result = $zip->open($zipPath);
        
        if ($result !== TRUE) {
            return false;
        }
        
        if (!mkdir($extractDir, 0755, true)) {
            $zip->close();
            return false;
        }
        
        $success = $zip->extractTo($extractDir);
        $zip->close();
        
        return $success;
    }
    
    /**
     * Find module directory in extracted files
     */
    private function findModuleDirectory($extractDir)
    {
        // Look for index.php in the root of extracted directory
        if (file_exists($extractDir . '/index.php')) {
            return $extractDir;
        }
        
        // Look for index.php in subdirectories (common with GitHub archives)
        $dirs = scandir($extractDir);
        foreach ($dirs as $dir) {
            if ($dir === '.' || $dir === '..') continue;
            
            $subDir = $extractDir . '/' . $dir;
            if (is_dir($subDir) && file_exists($subDir . '/index.php')) {
                return $subDir;
            }
        }
        
        return null;
    }
    
    /**
     * Install module from local path
     */
    private function installModuleFromPath($modulePath)
    {
        // Load module metadata
        $indexFile = $modulePath . '/index.php';
        if (!file_exists($indexFile)) {
            throw new \Exception('Module index.php not found');
        }
        
        $moduleData = include $indexFile;
        $moduleName = $moduleData['slug'] ?? basename($modulePath);
        
        // Check if module already exists
        $targetDir = dirname(__DIR__, 3) . '/modules/' . $moduleName;
        if (is_dir($targetDir)) {
            throw new \Exception("Module '{$moduleName}' already exists");
        }
        
        // Copy module files
        if (!$this->copyDirectory($modulePath, $targetDir)) {
            throw new \Exception('Failed to copy module files');
        }
        
        // Update composer autoload
        $this->updateComposerAutoload($moduleName);
        
        // Add to config
        $this->addModuleToConfig($moduleName);
        
        return $moduleData;
    }
    
    /**
     * Copy directory recursively
     */
    private function copyDirectory($source, $destination)
    {
        if (!is_dir($source)) {
            return false;
        }
        
        if (!mkdir($destination, 0755, true)) {
            return false;
        }
        
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            $relativePath = str_replace($source . DIRECTORY_SEPARATOR, '', $item->getPathname());
            $targetPath = $destination . DIRECTORY_SEPARATOR . $relativePath;
            
            if ($item->isDir()) {
                if (!mkdir($targetPath, 0755, true)) {
                    return false;
                }
            } else {
                if (!copy($item, $targetPath)) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Update composer autoload
     */
    private function updateComposerAutoload($moduleName)
    {
        $composerPath = dirname(__DIR__, 4) . '/composer.json';
        exec("cd " . dirname($composerPath) . " && composer dump-autoload --optimize");
    }
    
    /**
     * Add module to config
     */
    private function addModuleToConfig($moduleName)
    {
        $configPath = dirname(__DIR__, 3) . '/app/config.php';
        
        if (!file_exists($configPath)) {
            return;
        }
        
        $config = include $configPath;
        
        if (!isset($config['modules'])) {
            $config['modules'] = [];
        }
        
        if (!isset($config['modules'][$moduleName])) {
            $config['modules'][$moduleName] = [
                'enabled' => false,
                'suitable_as_default' => false
            ];
            
            // Write updated config back to file
            $configContent = "<?php\nreturn " . var_export($config, true) . ";\n";
            file_put_contents($configPath, $configContent);
        }
    }
    
    /**
     * Clean up temporary directory
     */
    private function cleanupTempDirectory($tempDir)
    {
        if (!is_dir($tempDir)) {
            return;
        }
        
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($tempDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item);
            } else {
                unlink($item);
            }
        }
        
        rmdir($tempDir);
    }
    
    /**
     * Get maximum upload file size
     */
    private function getMaxUploadSize()
    {
        $maxUpload = $this->parseSize(ini_get('upload_max_filesize'));
        $maxPost = $this->parseSize(ini_get('post_max_size'));
        $memoryLimit = $this->parseSize(ini_get('memory_limit'));
        
        return min($maxUpload, $maxPost, $memoryLimit);
    }
    
    /**
     * Parse size string to bytes
     */
    private function parseSize($size)
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);
        
        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        
        return round($size);
    }
    
    /**
     * Format bytes to human readable format
     */
    public function formatBytes($size)
    {
        if ($size == 0) return '0 Bytes';
        $unit = array('Bytes', 'KB', 'MB', 'GB', 'TB');
        $i = floor(log($size, 1024));
        return round($size / pow(1024, $i), 2) . ' ' . $unit[$i];
    }
    
    /**
     * Return JSON response
     */
    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}