<?php
namespace App\Modules\admin\OAuthClients\Controllers;

use App\DB;

class OAuthClientsController
{
    protected $db;
    public function __construct($db = null)
    {
        if ($db) {
            die('OAuthClientsController: using injected DB instance');
            $this->db = $db;
        } else {
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
        }
    }

    // List all OAuth clients
    public function index()
    {

        $clients = $this->db->fetchAll('SELECT * FROM oauth_clients ORDER BY id DESC');
        include __DIR__ . '/../views/list.php';
    }

    // Show add client form and handle POST
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
                $this->db->query('INSERT INTO oauth_clients (name, redirect_uri, client_id, client_secret) VALUES (?, ?, ?, ?)', [$name, $redirect, $client_id, $client_secret]);
                $success = 'Client registered!';
            } else {
                $error = 'Name and Redirect URI required.';
            }
        }
        include __DIR__ . '/../views/add.php';
    }
}
