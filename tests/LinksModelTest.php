<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../htdocs/modules/admin/models/Links.php';
require_once __DIR__ . '/../htdocs/app/DB.php';
use App\DB;
use App\Modules\Admin\Models\Links;

class LinksModelTest extends TestCase {
    protected $db;
    protected $config;
    protected $linksModel;

    protected function setUp(): void {
        $this->config = [
            'db' => [
                'host' => '127.0.0.1',
                'username' => 'root',
                'password' => 'root',
                'database' => 'test_framework', // Use a test DB
            ]
        ];
        $this->db = new DB($this->config);
        $this->linksModel = new Links($this->db, $this->config);
    }

    public function testAddLink() {
        $title = 'Test Link';
        $url = 'https://twitter.com/test';
        $icon = '';
        $this->linksModel->addLink($title, $url, $icon);
        $links = $this->linksModel->getAll();
        $found = false;
        foreach ($links as $link) {
            if ($link['title'] === $title && $link['url'] === $url) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Link was not added');
    }

    public function testIconDetection() {
        $icon = $this->linksModel->detectIcon('https://twitter.com/test');
        $this->assertEquals('fab fa-twitter', $icon);
    }
}
