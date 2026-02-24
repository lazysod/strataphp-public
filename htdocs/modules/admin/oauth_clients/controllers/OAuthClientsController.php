<?php
namespace App\Modules\admin\OAuthClients\Controllers;

use App\DB;

class OAuthClientsController
{
    protected $db;
    public function __construct($db)
    {
        $this->db = $db;
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
