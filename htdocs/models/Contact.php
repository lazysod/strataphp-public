<?php

namespace App\Models;

use App\DB;

class Contact
{
    public function getAllContacts()
    {
        $config = isset($config) ? $config : (file_exists(__DIR__ . '/../../app/config.php') ? include __DIR__ . '/../../app/config.php' : []);
        $db = new DB($config['db']);
        $contacts = $db->fetchAll('SELECT * FROM users');
        return $contacts;
    }

    public function addContact($name, $email, $avatar = '')
    {
        
        $config = isset($config) ? $config : (file_exists(__DIR__ . '/../../app/config.php') ? include __DIR__ . '/../../app/config.php' : []);
        $db = new DB($config['db']);
        $sql = "INSERT INTO users (name, email, avatar) VALUES (?, ?, ?)";
        $result = $db->query($sql, [$name, $email, $avatar]);
        return $result;
    }
}
