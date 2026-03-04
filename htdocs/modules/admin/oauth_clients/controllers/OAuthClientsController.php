<?php
namespace App\Modules\Admin\OAuthClients\Controllers;

use App\DB;

/**
 * Controller for managing OAuth clients.
 * Handles listing, adding, and error management for OAuth clients.
 */
class OAuthClientsController
{
    protected $db;
    /**
     * OAuthClientsController constructor.
     * Initializes DB connection from injected instance, global config, or config file.
     * Throws exception if DB config is missing.
     * @param DB|null $db Optional injected DB instance
     * @throws \Exception
     */
    /**
     * Constructor for OAuthClientsController.
     * Initializes DB connection from injected instance, global config, or config file.
     * Throws exception if DB config is missing.
     * @param DB|null $db Optional injected DB instance
     * @throws \Exception
     */
    public function __construct($db = null)
    {
        if ($db) {
            // Log usage of injected DB instance
            error_log('OAuthClientsController: using injected DB instance');
            $this->db = $db;
        } else {
            try {
                // Try global $config first
                global $config;
                if (isset($config) && isset($config['db'])) {
                    error_log('OAuthClientsController: using global $config');
                    $this->db = new DB($config);
                    return;
                }
                // fallback: load config from file
                $configPath = dirname(__DIR__, 4) . '/app/config.php';
                $configFile = file_exists($configPath) ? require $configPath : [];
                error_log('OAuthClientsController: loaded config file!: ');
                if (!isset($configFile['db'])) {
                    error_log('Database config missing in OAuthClientsController');
                    throw new \Exception('Database config missing');
                }
                $this->db = new DB($configFile);
            } catch (\Exception $e) {
                error_log('Error initializing DB in OAuthClientsController: ' . $e->getMessage());
                throw $e;
            }
        }
    }

    /**
     * List all OAuth clients.
     * Displays client list view.
     * Handles errors gracefully.
     */
    public function index()
    {
        $clients = [];
        try {
            $clients = $this->db->fetchAll('SELECT * FROM oauth_clients ORDER BY id DESC');
        } catch (\Exception $e) {
            error_log('Error fetching OAuth clients: ' . $e->getMessage());
            $clients = [];
        }
        try {
            include __DIR__ . '/../views/list.php';
        } catch (\Exception $e) {
            error_log('Error including list view: ' . $e->getMessage());
        }
    }

    /**
     * Show add client form and handle POST.
     * Registers new OAuth client if valid POST data.
     * Handles errors gracefully.
     */
    public function add()
    {
        $error = '';
        $success = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $redirect = trim($_POST['redirect_uri'] ?? '');
            if ($name && $redirect) {
                $client_id = bin2hex(random_bytes(16));
                $client_secret = bin2hex(random_bytes(32));
                try {
                    $this->db->query('INSERT INTO oauth_clients (name, redirect_uri, client_id, client_secret) VALUES (?, ?, ?, ?)', [$name, $redirect, $client_id, $client_secret]);
                    $success = 'Client registered!';
                } catch (\Exception $e) {
                    $error = 'Database error: ' . $e->getMessage();
                }
            } else {
                $error = 'Name and Redirect URI required.';
            }
        }
        try {
            include __DIR__ . '/../views/add.php';
        } catch (\Exception $e) {
            error_log('Error including add view: ' . $e->getMessage());
        }
    }
}
