<?php
namespace App;

/**
 * Secure File Upload Handler
 * 
 * Handles file uploads with security validation, image processing,
 * and storage management for the StrataPHP CMS.
 */
class FileUpload
{
    /**
     * Allowed image MIME types
     */
    private static $allowedImageTypes = [
        'image/jpeg',
        'image/jpg', 
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml'
    ];

    /**
     * Allowed file extensions
     */
    private static $allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
        'pdf', 'doc', 'docx', 'txt', 'zip'
    ];

    /**
     * Maximum file size (10MB)
     */
    private static $maxFileSize = 10485760;

    /**
     * Upload directory relative to htdocs
     */
    private static $uploadDir = 'storage/uploads';

    /**
     * Handle file upload with security validation
     */
    public static function upload($file, $options = [])
    {
        try {
            // Validate upload
            $validation = self::validateUpload($file, $options);
            if (!$validation['valid']) {
                return ['success' => false, 'error' => $validation['error']];
            }

            // Create upload directory if it doesn't exist
            $uploadPath = self::getUploadPath($options['subdir'] ?? 'general');
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generate secure filename
            $filename = self::generateFilename($file['name'], $options);
            $filepath = $uploadPath . '/' . $filename;

            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Process image if needed
                if (self::isImage($file['type'])) {
                    self::processImage($filepath, $options);
                }

                return [
                    'success' => true,
                    'filename' => $filename,
                    'filepath' => $filepath,
                    'url' => self::getFileUrl($filename, $options['subdir'] ?? 'general'),
                    'size' => filesize($filepath),
                    'type' => $file['type']
                ];
            } else {
                return ['success' => false, 'error' => 'Failed to save uploaded file'];
            }

        } catch (\Exception $e) {
            error_log("File upload error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Upload failed: ' . $e->getMessage()];
        }
    }

    /**
     * Validate uploaded file
     */
    private static function validateUpload($file, $options = [])
    {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => self::getUploadErrorMessage($file['error'])];
        }

        // Check file size
        if ($file['size'] > self::$maxFileSize) {
            return ['valid' => false, 'error' => 'File too large. Maximum size: ' . (self::$maxFileSize / 1024 / 1024) . 'MB'];
        }

        // Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, self::getAllowedMimeTypes($options))) {
            return ['valid' => false, 'error' => 'File type not allowed: ' . $mimeType];
        }

        // Validate file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, self::$allowedExtensions)) {
            return ['valid' => false, 'error' => 'File extension not allowed: ' . $extension];
        }

        // Additional image validation
        if (self::isImage($mimeType)) {
            $imageInfo = getimagesize($file['tmp_name']);
            if ($imageInfo === false) {
                return ['valid' => false, 'error' => 'Invalid image file'];
            }

            // Check image dimensions if specified
            if (isset($options['max_width']) && $imageInfo[0] > $options['max_width']) {
                return ['valid' => false, 'error' => 'Image width too large. Maximum: ' . $options['max_width'] . 'px'];
            }

            if (isset($options['max_height']) && $imageInfo[1] > $options['max_height']) {
                return ['valid' => false, 'error' => 'Image height too large. Maximum: ' . $options['max_height'] . 'px'];
            }
        }

        return ['valid' => true];
    }

    /**
     * Generate secure filename
     */
    private static function generateFilename($originalName, $options = [])
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $basename = pathinfo($originalName, PATHINFO_FILENAME);
        
        // Sanitize filename
        $basename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $basename);
        $basename = trim($basename, '_');
        
        if (empty($basename)) {
            $basename = 'upload';
        }

        // Add timestamp to prevent conflicts
        $timestamp = date('Y-m-d_H-i-s');
        $randomString = substr(bin2hex(random_bytes(4)), 0, 8);
        
        return $basename . '_' . $timestamp . '_' . $randomString . '.' . $extension;
    }

    /**
     * Get upload directory path
     */
    private static function getUploadPath($subdir = 'general')
    {
        $basePath = dirname(__DIR__) . '/' . self::$uploadDir;
        return $basePath . '/' . $subdir . '/' . date('Y/m');
    }

    /**
     * Get file URL for web access
     */
    private static function getFileUrl($filename, $subdir = 'general')
    {
        return '/' . self::$uploadDir . '/' . $subdir . '/' . date('Y/m') . '/' . $filename;
    }

    /**
     * Check if file is an image
     */
    private static function isImage($mimeType)
    {
        return in_array($mimeType, self::$allowedImageTypes);
    }

    /**
     * Process image (resize, optimize)
     */
    private static function processImage($filepath, $options = [])
    {
        if (!extension_loaded('gd')) {
            return; // Skip processing if GD not available
        }

        $imageInfo = getimagesize($filepath);
        if ($imageInfo === false) {
            return;
        }

        $maxWidth = $options['resize_width'] ?? 1920;
        $maxHeight = $options['resize_height'] ?? 1080;
        $quality = $options['quality'] ?? 85;

        list($width, $height, $type) = $imageInfo;

        // Skip if already small enough
        if ($width <= $maxWidth && $height <= $maxHeight) {
            return;
        }

        // Calculate new dimensions
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = (int)($width * $ratio);
        $newHeight = (int)($height * $ratio);

        // Create image resources
        $source = null;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($filepath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($filepath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($filepath);
                break;
            default:
                return; // Unsupported type
        }

        if (!$source) {
            return;
        }

        // Create resized image
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG/GIF
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
            imagefill($resized, 0, 0, $transparent);
        }

        // Resize image
        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save resized image
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($resized, $filepath, $quality);
                break;
            case IMAGETYPE_PNG:
                imagepng($resized, $filepath, 9);
                break;
            case IMAGETYPE_GIF:
                imagegif($resized, $filepath);
                break;
        }

        // Clean up
        imagedestroy($source);
        imagedestroy($resized);
    }

    /**
     * Get allowed MIME types based on options
     */
    private static function getAllowedMimeTypes($options = [])
    {
        if (isset($options['images_only']) && $options['images_only']) {
            return self::$allowedImageTypes;
        }

        // Extend with document types
        return array_merge(self::$allowedImageTypes, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'application/zip'
        ]);
    }

    /**
     * Get upload error message
     */
    private static function getUploadErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'File exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'File exceeds the MAX_FILE_SIZE directive in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload';
            default:
                return 'Unknown upload error';
        }
    }

    /**
     * Delete uploaded file
     */
    public static function delete($filepath)
    {
        if (file_exists($filepath) && is_file($filepath)) {
            return unlink($filepath);
        }
        return false;
    }

    /**
     * Get file info
     */
    public static function getFileInfo($filepath)
    {
        if (!file_exists($filepath)) {
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filepath);
        finfo_close($finfo);

        return [
            'filename' => basename($filepath),
            'size' => filesize($filepath),
            'type' => $mimeType,
            'is_image' => self::isImage($mimeType),
            'modified' => filemtime($filepath)
        ];
    }
}