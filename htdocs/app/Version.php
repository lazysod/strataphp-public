<?php

/**
 * StrataPHP Framework Version Manager
 * 
 * Provides centralized version information for the framework.
 * Reads version from composer.json as the canonical source.
 * 
 * @package StrataPHP
 * @version 1.0.0
 */
class Version
{
    /**
     * @var string|null Cached version number
     */
    private static $version = null;
    
    /**
     * @var string|null Cached composer.json path
     */
    private static $composerPath = null;
    
    /**
     * Get the framework version
     * 
     * @return string The version number (e.g., "1.0.0")
     */
    public static function get()
    {
        if (self::$version === null) {
            self::$version = self::readFromComposer();
        }
        
        return self::$version;
    }
    
    /**
     * Get detailed version information
     * 
     * @return array Version details including source
     */
    public static function getDetails()
    {
        $version = self::get();
        $source = self::getVersionSource();
        
        return [
            'version' => $version,
            'source' => $source,
            'composer_path' => self::getComposerPath(),
            'last_modified' => self::getComposerLastModified()
        ];
    }
    
    /**
     * Get the version source description
     * 
     * @return string Description of where version was read from
     */
    public static function getVersionSource()
    {
        if (file_exists(self::getComposerPath())) {
            return 'composer.json';
        }
        
        return 'fallback (composer.json not found)';
    }
    
    /**
     * Get the path to composer.json
     * 
     * @return string Path to composer.json
     */
    private static function getComposerPath()
    {
        if (self::$composerPath === null) {
            // Start from current file location and work backwards to find composer.json
            $currentDir = __DIR__;
            
            // Try current directory first (htdocs/app/)
            $composerPath = $currentDir . '/../../composer.json';
            
            if (!file_exists($composerPath)) {
                // Try parent directory (project root)
                $composerPath = dirname(dirname($currentDir)) . '/composer.json';
            }
            
            if (!file_exists($composerPath)) {
                // Try working backwards from document root
                $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? $currentDir;
                $composerPath = dirname($docRoot) . '/composer.json';
            }
            
            self::$composerPath = $composerPath;
        }
        
        return self::$composerPath;
    }
    
    /**
     * Read version from composer.json
     * 
     * @return string Version number or fallback
     */
    private static function readFromComposer()
    {
        $composerPath = self::getComposerPath();
        
        if (!file_exists($composerPath)) {
            return self::getFallbackVersion();
        }
        
        $composerContent = file_get_contents($composerPath);
        if ($composerContent === false) {
            return self::getFallbackVersion();
        }
        
        $composerData = json_decode($composerContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return self::getFallbackVersion();
        }
        
        return $composerData['version'] ?? self::getFallbackVersion();
    }
    
    /**
     * Get fallback version when composer.json is unavailable
     * 
     * @return string Fallback version
     */
    private static function getFallbackVersion()
    {
        return '1.0.0';
    }
    
    /**
     * Get when composer.json was last modified
     * 
     * @return string|null Last modified date or null if unavailable
     */
    private static function getComposerLastModified()
    {
        $composerPath = self::getComposerPath();
        
        if (!file_exists($composerPath)) {
            return null;
        }
        
        $timestamp = filemtime($composerPath);
        if ($timestamp === false) {
            return null;
        }
        
        return date('Y-m-d H:i:s', $timestamp);
    }
    
    /**
     * Check if we're running a development version
     * 
     * @return bool True if this appears to be a development version
     */
    public static function isDevelopment()
    {
        $version = self::get();
        
        // Check for development indicators
        return (
            strpos($version, 'dev') !== false ||
            strpos($version, 'alpha') !== false ||
            strpos($version, 'beta') !== false ||
            strpos($version, 'rc') !== false ||
            strpos($version, '-') !== false
        );
    }
    
    /**
     * Get semantic version parts
     * 
     * @return array Array with 'major', 'minor', 'patch' keys
     */
    public static function getSemanticParts()
    {
        $version = self::get();
        
        // Remove any development suffixes
        $cleanVersion = preg_replace('/[-+].*$/', '', $version);
        
        $parts = explode('.', $cleanVersion);
        
        return [
            'major' => (int)($parts[0] ?? 0),
            'minor' => (int)($parts[1] ?? 0),
            'patch' => (int)($parts[2] ?? 0)
        ];
    }
    
    /**
     * Compare with another version
     * 
     * @param string $otherVersion Version to compare against
     * @return int -1 if current < other, 0 if equal, 1 if current > other
     */
    public static function compare($otherVersion)
    {
        return version_compare(self::get(), $otherVersion);
    }
    
    /**
     * Check if current version is at least the specified version
     * 
     * @param string $minimumVersion Minimum required version
     * @return bool True if current version meets requirement
     */
    public static function isAtLeast($minimumVersion)
    {
        return self::compare($minimumVersion) >= 0;
    }
    
    /**
     * Clear cached version (useful for testing)
     * 
     * @return void
     */
    public static function clearCache()
    {
        self::$version = null;
        self::$composerPath = null;
    }
}