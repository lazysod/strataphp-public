#!/usr/bin/env php
<?php
/**
 * StrataPHP Module Installer
 * 
 * Usage:
 *   php bin/install-module.php <source>
 * 
 * Examples:
 *   php bin/install-module.php https://github.com/user/strataphp-blog.git
 *   php bin/install-module.php /path/to/local/module
 *   php bin/install-module.php https://example.com/module.zip
 */

require_once __DIR__ . '/../vendor/autoload.php';

class ModuleInstaller
{
    private $modulesPath;
    private $config;
    
    public function __construct()
    {
        $this->modulesPath = __DIR__ . '/../htdocs/modules/';
        $this->config = include __DIR__ . '/../htdocs/app/config.php';
    }
    
    public function install($source)
    {
        echo "🚀 StrataPHP Module Installer\n";
        echo "Source: $source\n\n";
        
        // Determine source type
        if (filter_var($source, FILTER_VALIDATE_URL)) {
            if (strpos($source, '.git') !== false) {
                return $this->installFromGit($source);
            } elseif (strpos($source, '.zip') !== false) {
                return $this->installFromZip($source);
            } else {
                echo "❌ Unsupported URL format\n";
                return false;
            }
        } elseif (is_dir($source)) {
            return $this->installFromDirectory($source);
        } else {
            echo "❌ Invalid source: $source\n";
            return false;
        }
    }
    
    private function installFromGit($gitUrl)
    {
        $tempDir = sys_get_temp_dir() . '/strataphp-module-' . uniqid();
        
        echo "📥 Cloning from Git...\n";
        $result = shell_exec("git clone $gitUrl $tempDir 2>&1");
        
        if (!is_dir($tempDir)) {
            echo "❌ Failed to clone repository\n";
            echo $result;
            return false;
        }
        
        return $this->processModule($tempDir);
    }
    
    private function installFromZip($zipUrl)
    {
        $tempFile = sys_get_temp_dir() . '/strataphp-module-' . uniqid() . '.zip';
        $tempDir = sys_get_temp_dir() . '/strataphp-module-' . uniqid();
        
        echo "📥 Downloading zip...\n";
        file_put_contents($tempFile, file_get_contents($zipUrl));
        
        $zip = new ZipArchive();
        if ($zip->open($tempFile) === TRUE) {
            echo "📦 Extracting zip...\n";
            $zip->extractTo($tempDir);
            $zip->close();
            unlink($tempFile);
            
            // Handle GitHub-style folder structure
            $contents = scandir($tempDir);
            if (count($contents) === 3 && is_dir($tempDir . '/' . $contents[2])) {
                $tempDir = $tempDir . '/' . $contents[2];
            }
            
            return $this->processModule($tempDir);
        } else {
            echo "❌ Failed to extract zip\n";
            return false;
        }
    }
    
    private function installFromDirectory($sourceDir)
    {
        echo "📁 Installing from directory...\n";
        return $this->processModule($sourceDir);
    }
    
    private function processModule($sourceDir)
    {
        // Validate module structure
        if (!$this->validateModule($sourceDir)) {
            $this->cleanup($sourceDir);
            return false;
        }
        
        // Get module metadata
        $metadata = include $sourceDir . '/index.php';
        $moduleName = $metadata['slug'] ?? basename($sourceDir);
        
        echo "📋 Module: {$metadata['name']} v{$metadata['version']}\n";
        echo "📋 Author: {$metadata['author']}\n";
        echo "📋 Description: {$metadata['description']}\n\n";
        
        // Check if module already exists
        $targetDir = $this->modulesPath . $moduleName;
        if (is_dir($targetDir)) {
            echo "⚠️  Module '$moduleName' already exists. Overwrite? [y/N]: ";
            $response = trim(fgets(STDIN));
            if (strtolower($response) !== 'y') {
                echo "❌ Installation cancelled\n";
                $this->cleanup($sourceDir);
                return false;
            }
            $this->removeDirectory($targetDir);
        }
        
        // Copy module files
        echo "📂 Installing to: $targetDir\n";
        $this->copyDirectory($sourceDir, $targetDir);
        
        // Update composer autoload if needed
        $this->updateComposerAutoload($moduleName, $metadata);
        
        // Add to config
        $this->addToConfig($moduleName, $metadata);
        
        // Run module install script if exists
        $this->runInstallScript($targetDir);
        
        // Cleanup temp files
        $this->cleanup($sourceDir);
        
        echo "\n✅ Module '$moduleName' installed successfully!\n";
        echo "🔧 Visit /admin/modules to enable the module\n";
        
        return true;
    }
    
    private function validateModule($dir)
    {
        $required = ['index.php'];
        $recommended = ['routes.php', 'controllers', 'README.md'];
        
        echo "🔍 Validating module structure...\n";
        
        foreach ($required as $item) {
            if (!file_exists($dir . '/' . $item)) {
                echo "❌ Missing required file: $item\n";
                return false;
            }
        }
        
        foreach ($recommended as $item) {
            if (!file_exists($dir . '/' . $item)) {
                echo "⚠️  Missing recommended: $item\n";
            }
        }
        
        // Validate metadata
        $metadata = include $dir . '/index.php';
        if (!isset($metadata['name'], $metadata['version'])) {
            echo "❌ Invalid module metadata in index.php\n";
            return false;
        }
        
        echo "✅ Module validation passed\n\n";
        return true;
    }
    
    private function updateComposerAutoload($moduleName, $metadata)
    {
        $composerFile = __DIR__ . '/../composer.json';
        $composer = json_decode(file_get_contents($composerFile), true);
        
        // Add PSR-4 autoloading for module
        $namespace = "App\\Modules\\" . ucfirst($moduleName) . "\\";
        $composer['autoload']['psr-4'][$namespace . "Controllers\\"] = "htdocs/modules/$moduleName/controllers/";
        $composer['autoload']['psr-4'][$namespace . "Models\\"] = "htdocs/modules/$moduleName/models/";
        
        file_put_contents($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        
        echo "🔄 Updating Composer autoload...\n";
        shell_exec('composer dump-autoload');
    }
    
    private function addToConfig($moduleName, $metadata)
    {
        $configFile = __DIR__ . '/../htdocs/app/config.php';
        $config = include $configFile;
        
        if (!isset($config['modules'][$moduleName])) {
            $config['modules'][$moduleName] = [
                'enabled' => false, // Disabled by default, admin can enable
                'suitable_as_default' => $metadata['suitable_as_default'] ?? false
            ];
            
            $configExport = var_export($config, true);
            file_put_contents($configFile, "<?php\nreturn $configExport;");
            
            echo "📝 Added to config (disabled by default)\n";
        }
    }
    
    private function runInstallScript($moduleDir)
    {
        $installScript = $moduleDir . '/install.php';
        if (file_exists($installScript)) {
            echo "🔧 Running module install script...\n";
            include $installScript;
        }
    }
    
    private function copyDirectory($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                if (is_dir($src . '/' . $file)) {
                    $this->copyDirectory($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
    
    private function removeDirectory($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        $this->removeDirectory($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }
    
    private function cleanup($tempDir)
    {
        if (strpos($tempDir, sys_get_temp_dir()) === 0 && is_dir($tempDir)) {
            $this->removeDirectory($tempDir);
        }
    }
}

// Main execution
if ($argc < 2) {
    echo "Usage: php install-module.php <source>\n";
    echo "Examples:\n";
    echo "  php install-module.php https://github.com/user/strataphp-blog.git\n";
    echo "  php install-module.php /path/to/local/module\n";
    echo "  php install-module.php https://example.com/module.zip\n";
    exit(1);
}

$installer = new ModuleInstaller();
$success = $installer->install($argv[1]);
exit($success ? 0 : 1);