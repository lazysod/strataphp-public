<?php
namespace App\Modules\Contact\Models;

use App\DB;
use Exception;

/**
 * Contact Model
 * 
 * Handles contact data operations including retrieving and storing contact information.
 * This model provides methods for managing user contacts in the database.
 * 
 * @package App\Modules\Contact\Models
 * @author  StrataPHP Framework
 * @version 1.0.0
 */
class Contact
{
    /**
     * Retrieve all contacts from the database
     * 
     * @return array List of all contacts with their information
     * @throws Exception If database connection or query fails
     */
    public function getAllContacts()
    {
        try {
            $config = isset($config) ? $config : (file_exists(__DIR__ . '/../../../app/config.php') ? include __DIR__ . '/../../../app/config.php' : []);
            $db = new DB($config);
            $contacts = $db->fetchAll('SELECT * FROM users');
            return $contacts;
        } catch (Exception $e) {
            throw new Exception('Failed to retrieve contacts: ' . $e->getMessage());
        }
    }

    /**
     * Add a new contact to the database
     * 
     * @param string $name   Contact's full name
     * @param string $email  Contact's email address
     * @param string $avatar Optional avatar URL or path
     * @return bool|int Result of the database operation or insert ID
     * @throws Exception If database connection or query fails
     */
    public function addContact($name, $email, $avatar = '')
    {
        try {
            $config = isset($config) ? $config : (file_exists(__DIR__ . '/../../../app/config.php') ? include __DIR__ . '/../../../app/config.php' : []);
            $db = new DB($config);
            $sql = "INSERT INTO users (name, email, avatar) VALUES (?, ?, ?)";
            $result = $db->query($sql, [$name, $email, $avatar]);
            return $result;
        } catch (Exception $e) {
            throw new Exception('Failed to add contact: ' . $e->getMessage());
        }
    }
}
