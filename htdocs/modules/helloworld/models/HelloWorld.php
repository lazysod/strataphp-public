<?php
namespace App\Modules\HelloWorld\Models;

/**
 * Hello World Model
 * 
 * Simple demonstration model for the Hello World module
 */
class HelloWorld
{
    /**
     * Get a hello world message
     * 
     * @return string The hello world message
     */
    public function getMessage()
    {
        try {
            return "Hello, world!";
        } catch (\Exception $e) {
            error_log("Error getting hello world message: " . $e->getMessage());
            return "Error loading message";
        }
    }
}
