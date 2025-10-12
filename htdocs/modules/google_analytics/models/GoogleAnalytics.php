<?php
namespace App\Modules\GoogleAnalytics\Models;

use App\DB;

class GoogleAnalytics
{
    private $db;
    private $table = 'google_analytics';
    
    public function __construct(DB $db)
    {
        $this->db = $db;
        // Validate table name for security
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $this->table)) {
            throw new \InvalidArgumentException('Invalid table name');
        }
    }
    
    /**
     * Get all records
     */
    public function getAll()
    {
        try {
            // Table name is validated in constructor, safe to use here
            $sql = "SELECT * FROM `" . $this->table . "` ORDER BY created_at DESC";
            return $this->db->fetchAll($sql);
        } catch (\Exception $e) {
            error_log("GoogleAnalytics model getAll error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get a record by ID
     */
    public function getById($id)
    {
        try {
            $sql = "SELECT * FROM `" . $this->table . "` WHERE id = ?";
            return $this->db->fetch($sql, [$id]);
        } catch (\Exception $e) {
            error_log("GoogleAnalytics model getById error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create a new record
     */
    public function create($data)
    {
        try {
            $fields = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));
            
            $sql = "INSERT INTO `" . $this->table . "` ($fields) VALUES ($placeholders)";
            
            return $this->db->query($sql, $data);
        } catch (\Exception $e) {
            error_log("GoogleAnalytics model create error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update a record
     */
    public function update($id, $data)
    {
        try {
            $setParts = [];
            foreach (array_keys($data) as $field) {
                $setParts[] = "$field = :$field";
            }
            $setClause = implode(', ', $setParts);
            
            $sql = "UPDATE `" . $this->table . "` SET $setClause WHERE id = :id";
            $data['id'] = $id;
            
            return $this->db->query($sql, $data);
        } catch (\Exception $e) {
            error_log("GoogleAnalytics model update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a record
     */
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM `" . $this->table . "` WHERE id = ?";
            return $this->db->query($sql, [$id]);
        } catch (\Exception $e) {
            error_log("GoogleAnalytics model delete error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Search records
     */
    public function search($query)
    {
        try {
            $sql = "SELECT * FROM `" . $this->table . "` 
                    WHERE title LIKE ? OR content LIKE ?
                    ORDER BY created_at DESC";
            
            $searchTerm = '%' . $query . '%';
            return $this->db->fetchAll($sql, [$searchTerm, $searchTerm]);
        } catch (\Exception $e) {
            error_log("GoogleAnalytics model search error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get records with pagination
     */
    public function paginate($page = 1, $perPage = 10)
    {
        try {
            // Validate and sanitize input
            $page = max(1, (int)$page);
            $perPage = max(1, min(100, (int)$perPage)); // Limit max per page
            $offset = ($page - 1) * $perPage;
            
            $sql = "SELECT * FROM `" . $this->table . "` 
                    ORDER BY created_at DESC 
                    LIMIT ? OFFSET ?";
            
            return $this->db->fetchAll($sql, [$perPage, $offset]);
        } catch (\Exception $e) {
            error_log("GoogleAnalytics model paginate error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get total count
     */
    public function getCount()
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM `" . $this->table . "`";
            $result = $this->db->fetch($sql);
            return $result ? (int)$result['count'] : 0;
        } catch (\Exception $e) {
            error_log("GoogleAnalytics model getCount error: " . $e->getMessage());
            return 0;
        }
    }
}