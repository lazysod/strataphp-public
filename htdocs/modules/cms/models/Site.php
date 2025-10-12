<?php
namespace App\Modules\Cms\Models;

use App\DB;

/**
 * Site Model for API key validation and site info
 */
class Site
{
    /**
     * Delete a site by ID
     */
    public function delete($id)
    {
        try {
            return $this->db->query("DELETE FROM sites WHERE id = ?", [$id]);
        } catch (\Throwable $e) {
            error_log("Site::delete error: " . $e->getMessage());
            return false;
        }
    }
    private $db;
    public function __construct($config = null)
    {
        if ($config) {
            $this->db = new DB($config);
        } else {
            $config = include __DIR__ . '/../../../app/config.php';
            $this->db = new DB($config);
        }
    }

    /**
     * Get all sites
     */
    public function getAll()
    {
        try {
            return $this->db->fetchAll("SELECT * FROM sites ORDER BY created_at DESC");
        } catch (\Throwable $e) {
            error_log("Site::getAll error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Create a new site
     */
    public function create($name, $apiKey)
    {
        try {
            return $this->db->query("INSERT INTO sites (name, api_key, status) VALUES (?, ?, 'active')", [$name, $apiKey]);
        } catch (\Throwable $e) {
            error_log("Site::create error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update API key for a site
     */
    public function updateApiKey($id, $apiKey)
    {
        try {
            return $this->db->query("UPDATE sites SET api_key = ?, updated_at = NOW() WHERE id = ?", [$apiKey, $id]);
        } catch (\Throwable $e) {
            error_log("Site::updateApiKey error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get site by API key (active only)
     */
    public function getByApiKey($apiKey)
    {
        try {
            return $this->db->fetch("SELECT * FROM sites WHERE api_key = ? AND status = 'active'", [$apiKey]);
        } catch (\Throwable $e) {
            error_log("Site::getByApiKey error: " . $e->getMessage());
            return null;
        }
    }

}
