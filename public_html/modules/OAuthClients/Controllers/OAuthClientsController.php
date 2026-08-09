<?php
namespace App\Modules\OAuthClients\Controllers;

use App\DB;

/**
 * Controller for managing OAuth clients.
 * Handles listing, adding, and error management for OAuth clients.
 */
use App\Logger;
class OAuthClientsController
{
    protected $db;
    protected $logger;

    /**
     * OAuthClientsController constructor.
     * Initializes DB connection from injected instance, global config, or config file.
     * Throws exception if DB config is missing.
     * @param DB|null $db Optional injected DB instance
     * @throws \Exception
     */
    public function __construct($db = null)
    {
        $this->logger = Logger::getInstance();
        if ($db) {
            // Log usage of injected DB instance
            
            $this->logger->info('OAuthClientsController: using injected DB instance');
            $this->db = $db;
        } else {
            try {
                // Try global $config first
                global $config;
                if (isset($config) && isset($config['db'])) {
                    $this->logger->info('OAuthClientsController: using global $config');
                    $this->db = new DB($config);
                    return;
                }
                // fallback: load config from file
                $configPath = dirname(__DIR__, 4) . '/app/config.php';
                $configFile = file_exists($configPath) ? require $configPath : [];
                $this->logger->info('OAuthClientsController: loaded config file for DB initialization');
                if (!isset($configFile['db'])) {
                    $this->logger->error('Database config missing in OAuthClientsController');
                    throw new \Exception('Database config missing');
                }
                $this->db = new DB($configFile);
            } catch (\Exception $e) {
                $this->logger->error('Error initializing DB in OAuthClientsController: ' . $e->getMessage());
                throw $e;
            }
        }
    }
}
