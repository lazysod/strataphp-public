<?php
// Base API controller for consistent JSON responses and error handling
class ApiController
{
    protected function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    protected function error($message, $status = 400)
    {
        $this->json([
            'error' => true,
            'code' => $status,
            'message' => $message
        ], $status);
    }
}
