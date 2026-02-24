<?php
use PHPUnit\Framework\TestCase;
use App\DB;
use App\User;
require_once __DIR__ . '/../htdocs/app/DB.php';
require_once __DIR__ . '/../htdocs/app/User.php';
if (!defined('PREFIX')) {
    define('PREFIX', 'app_');
}
class UserLoginTest extends TestCase {
    protected $db;
    protected $config;
    protected $userModel;

    protected function setUp(): void {
        $this->config = [
            'db' => [
                'host' => '127.0.0.1',
                'username' => 'root',
                'password' => 'root',
                'database' => 'test_framework', // Use test DB
            ]
        ];
        $this->db = new DB($this->config);
        $this->userModel = new User($this->db, $this->config);
        // Ensure test user exists
        $this->db->query('DELETE FROM users WHERE username = ?', ['testuser']);
        $passwordHash = password_hash('testpass', PASSWORD_DEFAULT);
        $this->db->query('DELETE FROM users WHERE email = ?', ['testuser@example.com']);
        $this->db->query('INSERT INTO users (username, email, pwd, active) VALUES (?, ?, ?, ?)', ['testuser', 'testuser@example.com', $passwordHash, 1]);
    }

    public function testLoginSuccess() {
        $result = $this->userModel->login(['email' => 'testuser@example.com', 'pwd' => 'testpass']);
        $this->assertEquals('success', $result['status'], 'Login should succeed with correct credentials');
    }

    public function testLoginFailure() {
        $result = $this->userModel->login(['email' => 'testuser@example.com', 'pwd' => 'wrongpass']);
        $this->assertEquals('fail', $result['status'], 'Login should fail with incorrect password');
    }
}
