<?php
/**
 * ModuleUpdater: Checks for and updates modules from a remote repo
 */
class ModuleUpdater
{
    /**
     * Check if a module update is available
     */
    public static function checkUpdate($moduleName, $localPath, $remoteUrl)
    {
        $localJson = json_decode(@file_get_contents($localPath), true);
        $remoteJson = json_decode(@file_get_contents($remoteUrl), true);
        if (!$localJson || !$remoteJson) return false;
        return version_compare($remoteJson['version'], $localJson['version'], '>');
    }

    /**
     * Download and update the module from a remote zip
     */
    public static function updateModule($moduleName, $zipUrl, $modulesDir)
    {
        $tmpZip = sys_get_temp_dir() . "/{$moduleName}_update.zip";
        $tmpDir = sys_get_temp_dir() . "/{$moduleName}_update";
        // Download zip
        file_put_contents($tmpZip, file_get_contents($zipUrl));
        $zip = new \ZipArchive();
        if ($zip->open($tmpZip) === true) {
            $zip->extractTo($tmpDir);
            $zip->close();
            // Path to extracted module in repo zip
            $repoModulePath = $tmpDir . "/strataphp-public-master/htdocs/modules/$moduleName";
            if (!is_dir($repoModulePath)) {
                // Clean up
                unlink($tmpZip);
                self::rrmdir($tmpDir);
                return false;
            }
            // Remove old module
            self::rrmdir("$modulesDir/$moduleName");
            // Copy new module files
            self::copyDir($repoModulePath, "$modulesDir/$moduleName");
            // Clean up
            unlink($tmpZip);
            self::rrmdir($tmpDir);
            return true;
        }
        return false;
    }

    // Recursively copy a directory
    public static function copyDir($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    self::copyDir($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * Recursively remove a directory
     */
    public static function rrmdir($dir)
    {
        if (!is_dir($dir)) return;
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object == "." || $object == "..") continue;
            $path = "$dir/$object";
            if (is_dir($path)) self::rrmdir($path);
            else unlink($path);
        }
        rmdir($dir);
    }
}
