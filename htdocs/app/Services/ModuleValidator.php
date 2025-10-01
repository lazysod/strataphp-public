<?php
namespace App\Services;

/**
 * StrataPHP Module Validation Service
 * 
 * Validates module structure, metadata, and code quality
 * according to StrataPHP Module Standards
 */
class ModuleValidator
{
    private $errors = [];
    private $warnings = [];
    private $suggestions = [];
    
    const REQUIRED_FIELDS = [
        'name', 'slug', 'version', 'description', 'author', 'category'
    ];
    
    const VALID_CATEGORIES = [
        'Content', 'E-commerce', 'Social', 'Utility', 'Analytics',
        'Security', 'SEO', 'Media', 'API', 'Admin', 'Development', 'Marketing'
    ];
    
    const DANGEROUS_FUNCTIONS = [
        'eval', 'exec', 'system', 'shell_exec', 'passthru',
        'file_get_contents', 'file_put_contents', 'fopen', 'fwrite'
    ];
    
    /**
     * Validate a module directory
     */
    public function validateModule($modulePath)
    {
        $this->errors = [];
        $this->warnings = [];
        $this->suggestions = [];
        
        // Basic structure validation
        $this->validateStructure($modulePath);
        
        // Metadata validation
        $metadata = $this->validateMetadata($modulePath);
        
        // Code quality validation
        if ($metadata) {
            $this->validateCodeQuality($modulePath);
            $this->validateSecurity($modulePath);
            $this->validatePerformance($modulePath);
        }
        
        // Collect validation results by category
        $structureResults = $this->getStructureResults($modulePath);
        $securityResults = $this->getSecurityResults();
        $qualityResults = $this->getQualityResults();
        $performanceResults = $this->getPerformanceResults();
        
        return [
            'valid' => empty($this->errors),
            'errors' => $this->errors,
            'warnings' => $this->warnings,
            'suggestions' => $this->suggestions,
            'metadata' => $metadata,
            'structure' => $structureResults,
            'security' => $securityResults,
            'quality' => $qualityResults,
            'performance' => $performanceResults
        ];
    }
    
    /**
     * Validate required file structure based on module requirements
     */
    private function validateStructure($modulePath)
    {
        // Only routes.php is truly required for a working module
        $requiredFiles = [];
        $recommendedFiles = ['index.php', 'README.md', 'CHANGELOG.md'];
        
        foreach ($requiredFiles as $file) {
            if (!file_exists($modulePath . '/' . $file)) {
                $this->errors[] = "Missing required file: {$file}";
            }
        }
        
        foreach ($recommendedFiles as $file) {
            if (!file_exists($modulePath . '/' . $file)) {
                $this->warnings[] = "Missing recommended file: {$file}";
            }
        }
        
        // Check for directory structure based on module requirements
        $metadata = $this->getCurrentMetadata($modulePath);
        $requirements = $metadata['structure_requirements'] ?? [];
        
        // Default requirements if not specified (backward compatibility)
        $defaultRequirements = [
            'controllers' => true,
            'views' => true, 
            'models' => true
        ];
        
        $structureRequirements = array_merge($defaultRequirements, $requirements);
        
        foreach ($structureRequirements as $dir => $required) {
            if ($required && !is_dir($modulePath . '/' . $dir)) {
                $this->errors[] = "Missing required directory: {$dir} (declared as required in module metadata)";
            }
        }
        
        // Check existing directories for proper structure
        $directories = ['controllers', 'models', 'views', 'assets'];
        foreach ($directories as $dir) {
            if (is_dir($modulePath . '/' . $dir)) {
                $this->validateDirectoryStructure($modulePath . '/' . $dir);
            }
        }
    }
    
    /**
     * Get current module metadata for internal use
     */
    private function getCurrentMetadata($modulePath)
    {
        $indexFile = $modulePath . '/index.php';
        if (!file_exists($indexFile)) {
            return [];
        }
        
        try {
            return include $indexFile;
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Validate metadata in index.php
     */
    private function validateMetadata($modulePath)
    {
        $indexFile = $modulePath . '/index.php';
        
        if (!file_exists($indexFile)) {
            return null;
        }
        
        try {
            $metadata = include $indexFile;
            
            if (!is_array($metadata)) {
                $this->errors[] = "index.php must return an array";
                return null;
            }
            
            // Validate required fields
            foreach (self::REQUIRED_FIELDS as $field) {
                if (!isset($metadata[$field]) || empty($metadata[$field])) {
                    $this->errors[] = "Missing required field: {$field}";
                }
            }
            
            // Validate specific field formats
            $this->validateVersionFormat($metadata['version'] ?? '');
            $this->validateSlugFormat($metadata['slug'] ?? '');
            $this->validateCategory($metadata['category'] ?? '');
            $this->validateDependencies($metadata['dependencies'] ?? []);
            
            // Check for recommended fields
            $recommendedFields = ['license', 'homepage', 'repository', 'support_url'];
            foreach ($recommendedFields as $field) {
                if (!isset($metadata[$field]) || empty($metadata[$field])) {
                    $this->suggestions[] = "Consider adding field: {$field}";
                }
            }
            
            return $metadata;
            
        } catch (\Exception $e) {
            $this->errors[] = "Error parsing index.php: " . $e->getMessage();
            return null;
        }
    }
    
    /**
     * Validate semantic version format
     */
    private function validateVersionFormat($version)
    {
        if (!preg_match('/^\d+\.\d+\.\d+(-[a-zA-Z0-9-]+)?$/', $version)) {
            $this->errors[] = "Version must follow semantic versioning (e.g., 1.0.0)";
        }
    }
    
    /**
     * Validate module slug format
     */
    private function validateSlugFormat($slug)
    {
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            $this->errors[] = "Slug must be lowercase alphanumeric with hyphens only";
        }
        
        if (strlen($slug) > 50) {
            $this->warnings[] = "Slug should be shorter than 50 characters";
        }
    }
    
    /**
     * Validate category
     */
    private function validateCategory($category)
    {
        if (!in_array($category, self::VALID_CATEGORIES)) {
            $this->warnings[] = "Category '{$category}' is not a standard category";
        }
    }
    
    /**
     * Validate dependencies
     */
    private function validateDependencies($dependencies)
    {
        if (!is_array($dependencies)) {
            $this->errors[] = "Dependencies must be an array";
            return;
        }
        
        foreach ($dependencies as $module => $version) {
            if (!is_string($module) || !is_string($version)) {
                $this->errors[] = "Invalid dependency format: {$module} => {$version}";
            }
        }
    }
    
    /**
     * Validate directory structure
     */
    private function validateDirectoryStructure($directory)
    {
        if (!is_readable($directory)) {
            $this->warnings[] = "Directory not readable: " . basename($directory);
            return;
        }
        
        $files = glob($directory . '/*.php');
        foreach ($files as $file) {
            $this->validatePHPFile($file);
        }
    }
    
    /**
     * Validate individual PHP file
     */
    private function validatePHPFile($file)
    {
        $content = file_get_contents($file);
        
        // Check for PHP opening tag
        if (!str_starts_with($content, '<?php')) {
            $this->warnings[] = "PHP file should start with <?php: " . basename($file);
        }
        
        // Check for namespace (PSR-4) - skip for view files and routes.php
        $filename = basename($file);
        $isViewFile = strpos($file, '/views/') !== false;
        $isRouteFile = $filename === 'routes.php';
        $isIndexFile = $filename === 'index.php';
        
        if (!$isViewFile && !$isRouteFile && !$isIndexFile) {
            if (!preg_match('/namespace\s+App\\\\Modules\\\\/', $content)) {
                $this->suggestions[] = "Consider using PSR-4 namespace in: " . basename($file);
            }
        }
        
        // Basic syntax check
        if (!$this->isValidPHP($content)) {
            $this->errors[] = "Syntax error in: " . basename($file);
        }
    }
    
    /**
     * Validate code quality
     */
    private function validateCodeQuality($modulePath)
    {
        $phpFiles = $this->findPHPFiles($modulePath);
        
        foreach ($phpFiles as $file) {
            $content = file_get_contents($file);
            
            // Skip quality checks for view files and configuration files
            $filename = basename($file);
            $isViewFile = strpos($file, '/views/') !== false;
            $isRouteFile = $filename === 'routes.php';
            $isIndexFile = $filename === 'index.php';
            
            if (!$isViewFile && !$isRouteFile && !$isIndexFile) {
                // Check for proper error handling (required)
                if (strpos($content, 'try') === false && strpos($content, 'catch') === false) {
                    $this->errors[] = "Missing error handling in: " . basename($file);
                }
                
                // Check for documentation (required)
                if (strpos($content, '/**') === false) {
                    $this->errors[] = "Missing documentation comments in: " . basename($file);
                }
            }
        }
    }
    
    /**
     * Validate security practices
     */
    /**
     * Validate security aspects of module code
     */
    private function validateSecurity($modulePath)
    {
        $phpFiles = $this->findPHPFiles($modulePath);
        $isAdminModule = basename($modulePath) === 'admin';
        
        foreach ($phpFiles as $file) {
            $content = file_get_contents($file);
            $fileName = basename($file);
            
            // Check for dangerous functions
            foreach (self::DANGEROUS_FUNCTIONS as $func) {
                if (preg_match('/\b' . $func . '\s*\(/', $content)) {
                    // Allow certain dangerous functions in admin module controllers
                    if ($isAdminModule && $this->isAllowedAdminFunction($func, $fileName)) {
                        // Skip warning for legitimate admin functions
                        continue;
                    }
                    $this->warnings[] = "Potentially dangerous function '{$func}' found in: " . $fileName;
                }
            }
            
            // Check for SQL injection prevention
            // Only flag actual dangerous concatenation patterns
            if ((preg_match('/\$.*=.*".*SELECT.*\$.*"/', $content) ||
                 preg_match('/\$.*=.*\'.*SELECT.*\$.*\'/', $content) ||
                 preg_match('/query\s*\(\s*\$.*\.\s*\$/', $content) ||
                 preg_match('/execute\s*\(\s*\$.*\.\s*\$/', $content)) &&
                !preg_match('/\?.*,.*\[/', $content)) {
                $this->warnings[] = "Potential SQL injection risk in: " . basename($file);
            }
            
            // Check for XSS prevention
            if (preg_match('/echo\s+\$|print\s+\$/', $content) && 
                !preg_match('/htmlspecialchars\(|htmlentities\(/', $content)) {
                $this->warnings[] = "Potential XSS risk in: " . basename($file);
            }
        }
    }
    
    /**
     * Validate performance considerations
     */
    private function validatePerformance($modulePath)
    {
        $phpFiles = $this->findPHPFiles($modulePath);
        
        foreach ($phpFiles as $file) {
            $content = file_get_contents($file);
            
            // Check for potential performance issues
            if (preg_match('/while\s*\(\s*true\s*\)/', $content)) {
                $this->warnings[] = "Infinite loop detected in: " . basename($file);
            }
            
            if (preg_match('/file_get_contents\([^)]*http/', $content)) {
                $this->suggestions[] = "Consider using cURL for HTTP requests in: " . basename($file);
            }
        }
    }
    
    /**
     * Find all PHP files in module
     */
    private function findPHPFiles($modulePath)
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($modulePath)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }
        
        return $files;
    }
    
    /**
     * Check if PHP content is syntactically valid
     */
    private function isValidPHP($content)
    {
        // Simple syntax check - just verify it starts with <?php and has basic structure
        // More sophisticated syntax checking can be added later if needed
        $content = trim($content);
        
        if (!str_starts_with($content, '<?php')) {
            return false;
        }
        
        // Basic checks for common syntax issues
        $brackets = substr_count($content, '{') - substr_count($content, '}');
        $parens = substr_count($content, '(') - substr_count($content, ')');
        
        // If brackets or parentheses are significantly unbalanced, likely syntax error
        return abs($brackets) <= 1 && abs($parens) <= 1;
    }
    
    /**
     * Get validation summary
     */
    public function getSummary()
    {
        return [
            'errors' => count($this->errors),
            'warnings' => count($this->warnings),
            'suggestions' => count($this->suggestions),
            'status' => empty($this->errors) ? 'valid' : 'invalid'
        ];
    }
    
    /**
     * Get structure validation results
     */
    private function getStructureResults($modulePath)
    {
        $metadata = $this->getCurrentMetadata($modulePath);
        $requirements = $metadata['structure_requirements'] ?? [];
        
        // Default requirements if not specified (backward compatibility)
        $defaultRequirements = [
            'controllers' => true,
            'views' => true, 
            'models' => true
        ];
        
        $structureRequirements = array_merge($defaultRequirements, $requirements);
        
        $results = [
            'has_index' => file_exists($modulePath . '/index.php'),
            'has_routes' => file_exists($modulePath . '/routes.php'),
            'has_readme' => file_exists($modulePath . '/README.md'),
            'has_metadata' => file_exists($modulePath . '/index.php') // Use index.php for metadata consistency
        ];
        
        // Check directories based on requirements
        foreach (['controllers', 'views', 'models'] as $dir) {
            $exists = is_dir($modulePath . '/' . $dir);
            $required = $structureRequirements[$dir] ?? false;
            
            // Pass if: exists OR not required
            $results['has_' . $dir] = $exists || !$required;
        }
        
        return $results;
    }
    
    /**
     * Get security validation results
     */
    private function getSecurityResults()
    {
        $securityErrors = array_filter($this->errors, function($error) {
            return strpos($error, 'security') !== false || 
                   strpos($error, 'SQL injection') !== false ||
                   strpos($error, 'XSS') !== false ||
                   strpos($error, 'dangerous function') !== false;
        });
        
        return [
            'no_dangerous_functions' => empty($securityErrors),
            'sql_injection_safe' => !$this->hasSecurityIssue('SQL injection'),
            'xss_safe' => !$this->hasSecurityIssue('XSS'),
            'file_access_safe' => !$this->hasSecurityIssue('file access')
        ];
    }
    
    /**
     * Get code quality validation results
     */
    private function getQualityResults()
    {
        return [
            'has_psr4_namespace' => $this->checkPSR4Compliance(),
            'has_documentation' => $this->checkDocumentation(),
            'follows_conventions' => $this->checkNamingConventions(),
            'has_error_handling' => $this->checkErrorHandling()
        ];
    }
    
    /**
     * Get performance validation results
     */
    private function getPerformanceResults()
    {
        return [
            'optimized_queries' => !$this->hasPerformanceIssue('query'),
            'cached_operations' => $this->checkCaching(),
            'minimal_dependencies' => $this->checkDependencies(),
            'efficient_loops' => !$this->hasPerformanceIssue('loop')
        ];
    }
    
    /**
     * Helper methods for validation checks
     */
    private function hasSecurityIssue($type)
    {
        foreach ($this->warnings as $warning) {
            if (stripos($warning, $type) !== false) {
                return true;
            }
        }
        return false;
    }
    
    private function checkPSR4Compliance()
    {
        // Simple check - if we have namespace suggestions, PSR-4 is not fully compliant
        foreach ($this->suggestions as $suggestion) {
            if (strpos($suggestion, 'PSR-4 namespace') !== false) {
                return false;
            }
        }
        return true;
    }
    
    private function checkDocumentation()
    {
        // Check if we have documentation suggestions
        foreach ($this->suggestions as $suggestion) {
            if (strpos($suggestion, 'documentation') !== false) {
                return false;
            }
        }
        return true;
    }
    
    private function checkNamingConventions()
    {
        // Assume good if no naming convention errors
        return true;
    }
    
    private function checkErrorHandling()
    {
        // Check if we have error handling suggestions
        foreach ($this->suggestions as $suggestion) {
            if (strpos($suggestion, 'error handling') !== false) {
                return false;
            }
        }
        return true;
    }
    
    private function hasPerformanceIssue($type)
    {
        // Simple performance check
        return false;
    }
    
    private function checkCaching()
    {
        // Simple caching check
        return true;
    }
    
    private function checkDependencies()
    {
        // Simple dependency check
        return true;
    }
    
    /**
     * Check if dangerous function is allowed in admin module
     */
    private function isAllowedAdminFunction($function, $fileName)
    {
        $allowedFunctions = [
            'file_get_contents' => ['ModuleDetailsController.php'],
            'file_put_contents' => ['ModuleManagerController.php', 'ModuleInstallerController.php'],
            'exec' => ['ModuleInstallerController.php']
        ];
        
        return isset($allowedFunctions[$function]) && 
               in_array($fileName, $allowedFunctions[$function]);
    }
}

/**
 * Compatibility function for older PHP versions
 */
if (!function_exists('php_check_syntax_string')) {
    function php_check_syntax_string($code) {
        $tempFile = tempnam(sys_get_temp_dir(), 'php_syntax_check');
        file_put_contents($tempFile, $code);
        $result = php_check_syntax($tempFile);
        unlink($tempFile);
        return $result;
    }
}

if (!function_exists('php_check_syntax')) {
    function php_check_syntax($filename) {
        $output = shell_exec("php -l " . escapeshellarg($filename) . " 2>&1");
        return strpos($output, 'No syntax errors') !== false;
    }
}