<?php
namespace App\Modules\Admin\Controllers;

use App\DB;

class OAuthClientController
{
    protected $db;

    public function __construct()
    {
        global $config;
        // Use global $config if available and valid
        if (isset($config) && is_array($config) && isset($config['db'])) {
            $this->db = new DB($config);
            return;
        }
        // Fallback: try to load config file directly
        $configPath = dirname(__DIR__, 4) . '/app/config.php';
        $loadedConfig = file_exists($configPath) ? require $configPath : [];
        if (isset($loadedConfig['db'])) {
            $this->db = new DB($loadedConfig['db']);
            return;
        }
        // If still missing, throw clear error
        throw new \Exception('OAuthClientController: Unable to load database config');
    }

    public function index()
    {
        $clients = $this->db->fetchAll("SELECT * FROM oauth_clients ORDER BY id DESC");
        include __DIR__ . '/../views/oauth_clients/list.php';
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $redirect_uri = trim($_POST['redirect_uri'] ?? '');
            $data_shared = trim($_POST['data_shared'] ?? '');
            $client_id = bin2hex(random_bytes(16));
            $client_secret = bin2hex(random_bytes(32));
            $this->db->query(
                "INSERT INTO oauth_clients (name, client_id, client_secret, redirect_uri, data_shared) VALUES (?, ?, ?, ?, ?)",
                [$name, $client_id, $client_secret, $redirect_uri, $data_shared]
            );
            header('Location: /admin/oauth-clients');
            exit;
        }
        include __DIR__ . '/../views/oauth_clients/add.php';
    }

    public function edit($id)
    {
        $client = $this->db->fetch("SELECT * FROM oauth_clients WHERE id = ?", [$id]);
        if (!$client) {
            header('Location: /admin/oauth-clients');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $redirect_uri = trim($_POST['redirect_uri'] ?? '');
            $data_shared = trim($_POST['data_shared'] ?? '');
            $this->db->query(
                "UPDATE oauth_clients SET name = ?, redirect_uri = ?, data_shared = ? WHERE id = ?",
                [$name, $redirect_uri, $data_shared, $id]
            );
            header('Location: /admin/oauth-clients');
            exit;
        }
        include __DIR__ . '/../views/oauth_clients/edit.php';
    }

    public function delete($id)
    {
        // Debug: Output DB name and query context
        // Remove related approvals first
        $this->db->query("DELETE FROM oauth_user_approvals WHERE client_id = ?", [$id]);
        $this->db->query("DELETE FROM oauth_clients WHERE id = ?", [$id]);
        header('Location: /admin/oauth-clients');
        exit;
    }
}
