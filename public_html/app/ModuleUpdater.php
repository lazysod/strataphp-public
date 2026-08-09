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
            // Find the correct module path inside the zip (e.g., modules/links/)
            $moduleSubdir = null;
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                $name = $stat['name'];
                // Look for a directory like modules/links/ or modules/Links/
                if (preg_match('#^(.*modules/(' . preg_quote($moduleName, '#') . '))/index\\.php$#i', $name, $matches)) {
                    $moduleSubdir = $matches[1];
                    break;
                }
            }
            if ($moduleSubdir === null) {
                $zip->close();
                unlink($tmpZip);
                return false; // Module not found in zip
            }
            // Extract only the module directory
            if (is_dir($tmpDir)) self::rrmdir($tmpDir);
            mkdir($tmpDir);
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                $name = $stat['name'];
                if (stripos($name, $moduleSubdir . '/') === 0) {
                    $relPath = substr($name, strlen($moduleSubdir) + 1);
                    if ($relPath === '') continue;
                    $targetPath = $tmpDir . '/' . $relPath;
                    if (substr($name, -1) === '/') {
                        if (!is_dir($targetPath)) mkdir($targetPath, 0777, true);
                    } else {
                        $dir = dirname($targetPath);
                        if (!is_dir($dir)) mkdir($dir, 0777, true);
                        copy('zip://' . $tmpZip . '#' . $name, $targetPath);
                    }
                }
            }
            $zip->close();
            // Copy files to modulesDir
            // Find the original case directory name in the modules path
            $existingDirName = null;
            foreach (scandir($modulesDir) as $dir) {
                if (strcasecmp($dir, $moduleName) === 0) {
                    $existingDirName = $dir;
                    break;
                }
            }
            $dst = $modulesDir . '/' . ($existingDirName ?: $moduleName);
            if ($existingDirName && is_dir($dst)) {
                self::rrmdir($dst);
            }
            mkdir($dst, 0777, true);
            self::recurseCopy($tmpDir, $dst);
            self::rrmdir($tmpDir);
            unlink($tmpZip);
            return true;
        }
        return false;
    }

    // Recursively copy files from one directory to another
    private static function recurseCopy($src, $dst)
    {
        $dir = opendir($src);
        if (!is_dir($dst)) {
            mkdir($dst, 0777, true);
        }
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    self::recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
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
