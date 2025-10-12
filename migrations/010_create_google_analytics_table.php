<?php
/**
 * Migration: Create google_analytics_settings table
 *
 * This migration creates a table to store Google Analytics settings (e.g., Measurement ID).
 */

/**
 * Accepts App\DB and uses ->pdo() for PDO connection
 */
return function($db) {
    $pdo = method_exists($db, 'getPdo') ? $db->getPdo() : $db;
    $sql = "CREATE TABLE IF NOT EXISTS google_analytics_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        measurement_id VARCHAR(32) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql);
};
