<?php
namespace App\Modules\Cms\Controllers;

/**
 * Image Upload Handler for CMS
 */
class ImageController
{
    private $uploadDir;
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private $maxFileSize = 5 * 1024 * 1024; // 5MB
    private $config;
    
    public function __construct()
    {
        $this->uploadDir = __DIR__ . '/../../../storage/uploads/cms/';
        
        // Load config
        $this->config = include __DIR__ . '/../../../app/config.php';
        
        // Create directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    /**
     * Require admin authentication
     */
    private function requireAuth()
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Get session prefix from config
        $sessionPrefix = $this->config['session_prefix'] ?? 'app_';
        
        // Check if user is logged in as admin using StrataPHP's session structure
        if (!isset($_SESSION[$sessionPrefix . 'admin']) || $_SESSION[$sessionPrefix . 'admin'] < 1) {
            // Return JSON error instead of redirecting
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => 'Authentication required'
            ]);
            exit;
        }
    }
    
    /**
     * Handle image upload via AJAX
     */
    public function upload()
    {
        // Start output buffering to catch any stray output
        ob_start();
        
        header('Content-Type: application/json');
        
        // Check authentication
        $this->requireAuth();
        
        try {
            if (!isset($_FILES['image'])) {
                throw new \Exception('No image file provided');
            }
            
            $file = $_FILES['image'];
            
            // Validate file
            $this->validateFile($file);
            
            // Generate unique filename
            $filename = $this->generateFilename($file['name']);
            $filepath = $this->uploadDir . $filename;
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new \Exception('Failed to upload file');
            }
            
            // Generate thumbnail (with improved error handling)
            $thumbnailPath = $this->createThumbnail($filepath, $filename);
            
            // Clean any output that might have been generated
            ob_clean();
            
            // Generate absolute URLs for social media compatibility
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $baseUrl = $protocol . $host;
            
            // Return success response
            echo json_encode([
                'success' => true,
                'filename' => $filename,
                'url' => $baseUrl . '/storage/uploads/cms/' . $filename,
                'thumbnail' => $thumbnailPath ? $baseUrl . '/storage/uploads/cms/thumbs/' . $filename : $baseUrl . '/storage/uploads/cms/' . $filename,
                'size' => filesize($filepath)
            ]);
            
        } catch (\Exception $e) {
            // Clean any output that might have been generated
            ob_clean();
            
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        
        // End output buffering
        ob_end_flush();
    }
    
    /**
     * Validate uploaded file
     */
    private function validateFile($file)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('Upload failed with error code: ' . $file['error']);
        }
        
        if ($file['size'] > $this->maxFileSize) {
            throw new \Exception('File size exceeds maximum allowed size of 5MB');
        }
        
        $mimeType = mime_content_type($file['tmp_name']);
        if (!in_array($mimeType, $this->allowedTypes)) {
            throw new \Exception('Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed');
        }
        
        // Additional security check
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            throw new \Exception('Invalid image file');
        }
    }
    
    /**
     * Generate unique filename
     */
    private function generateFilename($originalName)
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $basename = pathinfo($originalName, PATHINFO_FILENAME);
        $basename = preg_replace('/[^a-zA-Z0-9_-]/', '', $basename);
        
        return date('Y-m-d_H-i-s') . '_' . $basename . '.' . strtolower($extension);
    }
    
    /**
     * Create thumbnail
     */
    private function createThumbnail($filepath, $filename)
    {
        $thumbDir = $this->uploadDir . 'thumbs/';
        
        if (!is_dir($thumbDir)) {
            mkdir($thumbDir, 0755, true);
        }
        
        $thumbPath = $thumbDir . $filename;
        
        try {
            // Temporarily disable error reporting to prevent PNG warnings from breaking JSON response
            $originalErrorReporting = error_reporting();
            error_reporting(E_ERROR | E_PARSE);
            
            $imageInfo = getimagesize($filepath);
            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];
            $mimeType = $imageInfo['mime'];
            
            // Calculate thumbnail dimensions (max 300x200)
            $maxWidth = 300;
            $maxHeight = 200;
            
            $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
            $thumbWidth = intval($originalWidth * $ratio);
            $thumbHeight = intval($originalHeight * $ratio);
            
            // Create source image (suppress warnings for invalid color profiles)
            switch ($mimeType) {
                case 'image/jpeg':
                    $source = @imagecreatefromjpeg($filepath);
                    break;
                case 'image/png':
                    $source = @imagecreatefrompng($filepath);
                    break;
                case 'image/gif':
                    $source = @imagecreatefromgif($filepath);
                    break;
                case 'image/webp':
                    $source = @imagecreatefromwebp($filepath);
                    break;
                default:
                    throw new \Exception('Unsupported image type for thumbnail');
            }
            
            if (!$source) {
                throw new \Exception('Failed to create image resource');
            }
            
            // Create thumbnail
            $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);
            
            // Preserve transparency for PNG and GIF
            if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
                $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
                imagefill($thumbnail, 0, 0, $transparent);
            }
            
            imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $originalWidth, $originalHeight);
            
            // Save thumbnail
            switch ($mimeType) {
                case 'image/jpeg':
                    imagejpeg($thumbnail, $thumbPath, 85);
                    break;
                case 'image/png':
                    imagepng($thumbnail, $thumbPath, 8);
                    break;
                case 'image/gif':
                    imagegif($thumbnail, $thumbPath);
                    break;
                case 'image/webp':
                    imagewebp($thumbnail, $thumbPath, 85);
                    break;
            }
            
            imagedestroy($source);
            imagedestroy($thumbnail);
            
            // Restore original error reporting
            error_reporting($originalErrorReporting);
            
            return $thumbPath;
            
        } catch (\Exception $e) {
            // Restore original error reporting
            if (isset($originalErrorReporting)) {
                error_reporting($originalErrorReporting);
            }
            
            // If thumbnail creation fails, continue without it (don't break the upload)
            error_log("Thumbnail creation failed: " . $e->getMessage());
            return null;
        }
    }
}