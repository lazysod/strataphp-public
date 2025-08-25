<?php
// Example helper class for API controllers
class ApiHelper
{
    // Example: Validate required parameters in an array
    public static function requireParams($params, $required)
    {
        $missing = [];
        foreach ($required as $key) {
            if (!isset($params[$key]) || $params[$key] === '') {
                $missing[] = $key;
            }
        }
        return $missing;
    }

    // Example: Format a standard success response
    public static function success($data = [], $message = 'OK')
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
    }

    // Example: Format a standard error response
    public static function error($message = 'Error', $code = 400)
    {
        return [
            'success' => false,
            'code' => $code,
            'message' => $message
        ];
    }
}
