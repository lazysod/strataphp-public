<?php
/**
 * Image Upload Handler for TinyMCE
 * 
 * Handles AJAX image uploads from the TinyMCE editor
 * Returns JSON response with image URL for insertion
 */

// Prevent direct access
if (!defined('STRPHP_ROOT')) {
    define('STRPHP_ROOT', dirname(__DIR__, 2));
}

require_once STRPHP_ROOT . '/htdocs/app/start.php';
require_once STRPHP_ROOT . '/htdocs/app/FileUpload.php';

use App\FileUpload;

// Set JSON header
header('Content-Type: application/json');

try {
    // Check if user is authenticated admin (use session prefix from config)
    session_start();
    $config = file_exists(__DIR__ . '/../app/config.php') ? require __DIR__ . '/../app/config.php' : [];
    $sessionPrefix = $config['session_prefix'] ?? 'app_';
    if (!isset($_SESSION[$sessionPrefix . 'admin']) || $_SESSION[$sessionPrefix . 'admin'] < 1) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized access']);
        exit;
    }

    // Check if file was uploaded
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['error' => 'No file uploaded or upload error']);
        exit;
    }

    // Upload options for images and PDFs
    $uploadOptions = [
        'images_only' => false, // allow images and pdfs
        'subdir' => 'cms/files',
        'resize_width' => 1920,
        'resize_height' => 1080,
        'quality' => 85,
        'max_width' => 3000,
        'max_height' => 3000
    ];

    // Handle the upload
    $result = FileUpload::upload($_FILES['file'], $uploadOptions);

    if ($result['success']) {
        // TinyMCE expects a specific JSON format
        echo json_encode([
            'location' => $result['url']
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            'error' => $result['error']
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error'
    ]);
}