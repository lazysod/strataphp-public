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
        return $this->db->query("DELETE FROM sites WHERE id = ?", [$id]);
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
        return $this->db->fetchAll("SELECT * FROM sites ORDER BY created_at DESC");
    }

    /**
     * Create a new site
     */
    public function create($name, $apiKey)
    {
        return $this->db->query("INSERT INTO sites (name, api_key, status) VALUES (?, ?, 'active')", [$name, $apiKey]);
    }

    /**
     * Update API key for a site
     */
    public function updateApiKey($id, $apiKey)
    {
        return $this->db->query("UPDATE sites SET api_key = ?, updated_at = NOW() WHERE id = ?", [$apiKey, $id]);
    }

    /**
     * Get site by API key (active only)
     */
        public function getByApiKey($apiKey)
        {
            return $this->db->fetch("SELECT * FROM sites WHERE api_key = ? AND status = 'active'", [$apiKey]);
        }

}
