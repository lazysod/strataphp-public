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
            'tempDirInfo' => $this->getTempDirectoryInfo(),
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
            // Debug: Log $_FILES information
            error_log('Upload attempt - $_FILES: ' . print_r($_FILES, true));
            
            // Check if file was uploaded
            if (!isset($_FILES['module_zip'])) {
                throw new \Exception('No file uploaded: module_zip field not found in request');
            }
            
            $uploadError = $_FILES['module_zip']['error'];
            if ($uploadError !== UPLOAD_ERR_OK) {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive',
                    UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
                    UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                    UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                    UPLOAD_ERR_EXTENSION => 'File upload stopped by PHP extension'
                ];
                
                $errorMsg = isset($errorMessages[$uploadError]) 
                    ? $errorMessages[$uploadError] 
                    : "Unknown upload error code: $uploadError";
                    
                throw new \Exception("Upload error: $errorMsg");
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
            
            // Clean up temporary files (including uploaded ZIP)
            $this->cleanupTempDirectory($tempDir);
            error_log("Module installation cleanup: removed temporary files from $tempDir");
            
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Module installed successfully!',
                'module' => $result
            ]);
            
        } catch (\Exception $e) {
            // Clean up on error (including uploaded ZIP)
            if (isset($tempDir)) {
                $this->cleanupTempDirectory($tempDir);
                error_log("Module installation error cleanup: removed temporary files from $tempDir");
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
            
            // Try to find PHP executable - works for both MAMP and cPanel
            $phpPath = $this->findPhpExecutable();
            $command = $phpPath . " " . escapeshellarg($generatorPath) . " " . escapeshellarg($moduleName) . " 2>&1";
            
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);
            
            if ($returnCode !== 0) {
                throw new \Exception('Generation failed: ' . implode("\n", $output));
            }
            
            // Add the module to the config file
            $this->addModuleToConfig($moduleName);
            
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
     * Add a newly generated module to the config file
     */
    private function addModuleToConfig($moduleName)
    {
        $configPath = dirname(__DIR__, 3) . '/app/config.php';
        
        if (!file_exists($configPath)) {
            throw new \Exception('Config file not found');
        }
        
        // Load current config
        $currentConfig = include $configPath;
        
        // Check if module already exists in config
        if (isset($currentConfig['modules'][$moduleName])) {
            return; // Already exists, no need to add
        }
        
        // Get module metadata to determine default settings
        $moduleIndexPath = dirname(__DIR__, 2) . "/{$moduleName}/index.php";
        $moduleData = [];
        
        if (file_exists($moduleIndexPath)) {
            $moduleData = include $moduleIndexPath;
        }
        
        // Add module to config with sensible defaults
        $currentConfig['modules'][$moduleName] = [
            'enabled' => $moduleData['enabled'] ?? false,
            'suitable_as_default' => $moduleData['suitable_as_default'] ?? false,
        ];
        
        // Write config back to file
        $configContent = "<?php\nreturn " . var_export($currentConfig, true) . ";\n";
        
        if (!file_put_contents($configPath, $configContent)) {
            throw new \Exception('Failed to update config file');
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
        // Clean up any old temp directories first (older than 1 hour)
        $this->cleanupOldTempDirectories();
        
        $tempDir = sys_get_temp_dir() . '/strataphp_module_' . uniqid();
        if (!mkdir($tempDir, 0755, true)) {
            throw new \Exception('Failed to create temporary directory');
        }
        error_log("Created temporary directory for module installation: $tempDir");
        return $tempDir;
    }
    
    /**
     * Clean up old temporary directories (older than 1 hour)
     */
    private function cleanupOldTempDirectories()
    {
        $tempBasePath = sys_get_temp_dir();
        $pattern = $tempBasePath . '/strataphp_module_*';
        $oldDirs = glob($pattern);
        
        if (!$oldDirs) {
            return;
        }
        
        $oneHourAgo = time() - 3600;
        $cleanedCount = 0;
        
        foreach ($oldDirs as $dir) {
            if (is_dir($dir) && filemtime($dir) < $oneHourAgo) {
                $this->cleanupTempDirectory($dir);
                $cleanedCount++;
            }
        }
        
        if ($cleanedCount > 0) {
            error_log("Cleaned up $cleanedCount old temporary directories");
        }
    }
    
    /**
     * Get information about temporary directories
     */
    private function getTempDirectoryInfo()
    {
        $tempBasePath = sys_get_temp_dir();
        $pattern = $tempBasePath . '/strataphp_module_*';
        $tempDirs = glob($pattern);
        
        return [
            'basePath' => $tempBasePath,
            'activeCount' => $tempDirs ? count($tempDirs) : 0,
            'freeSpace' => disk_free_space($tempBasePath),
            'totalSpace' => disk_total_space($tempBasePath)
        ];
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
     * Clean up temporary directory and all files (including ZIP uploads)
     */
    private function cleanupTempDirectory($tempDir)
    {
        if (!is_dir($tempDir)) {
            return;
        }
        
        $fileCount = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($tempDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item);
            } else {
                unlink($item);
                $fileCount++;
            }
        }
        
        rmdir($tempDir);
        error_log("Cleanup completed: removed $fileCount files and directories from $tempDir");
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
     * Find the PHP executable path - works for MAMP, cPanel, and standard installations
     */
    private function findPhpExecutable()
    {
        // Try common PHP paths in order of preference
        $possiblePaths = [
            PHP_BINARY, // Current PHP binary (most reliable)
            '/usr/bin/php', // Standard Linux/cPanel path
            '/usr/local/bin/php', // Alternative Linux path
            '/Applications/MAMP/bin/php/php8.2.0/bin/php', // MAMP 8.2
            '/Applications/MAMP/bin/php/php8.1.0/bin/php', // MAMP 8.1
            '/Applications/MAMP/bin/php/php8.0.0/bin/php', // MAMP 8.0
            'php' // Fallback to system PATH
        ];
        
        foreach ($possiblePaths as $path) {
            if ($path === 'php') {
                // Test if php command is available in PATH
                $output = [];
                $returnCode = 0;
                exec('which php 2>/dev/null', $output, $returnCode);
                if ($returnCode === 0 && !empty($output[0])) {
                    return 'php';
                }
            } else {
                // Test if the specific path exists and is executable
                if (file_exists($path) && is_executable($path)) {
                    return $path;
                }
            }
        }
        
        // If nothing found, fall back to 'php' and hope for the best
        return 'php';
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