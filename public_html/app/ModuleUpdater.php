<?php
namespace App;

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
        if (!$localJson || !$remoteJson) {
            return false;
        }
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
            // Copy files to modulesDir
            $modulePath = $modulesDir . '/' . $moduleName;
            if (is_dir($modulePath)) {
                self::rrmdir($modulePath);
            }
            rename($tmpDir, $modulePath);
            unlink($tmpZip);
            return true;
        }
        return false;
    }

    private static function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    $path = $dir . "/" . $object;
                    if (is_dir($path)) {
                        self::rrmdir($path);
                    } else {
                        unlink($path);
                    }
                }
            }
            rmdir($dir);
        }
    }
}
