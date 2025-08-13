<?php
// Simple AJAX endpoint for testing
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}
// Optional: CSRF check, authentication, etc.
echo json_encode(['status' => 'success', 'message' => 'pong']);
