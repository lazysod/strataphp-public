<?php
// Admin Links Management Controller
class AdminLinksController
{
    private function requireAdmin() {
        global $config;
        $sessionPrefix = $config['session_prefix'];
        if (!isset($_SESSION[$sessionPrefix . 'admin']) || $_SESSION[$sessionPrefix . 'admin'] < 1) {
            header('Location: /admin/admin_login.php');
            exit;
        }
    }

    public function order()
    {
        $this->requireAdmin();
        global $config;
        $db = new DB($config);
        $linksModel = new Links($db, $config);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            $direction = $_POST['direction'] ?? '';
            $links = $linksModel->getAll();
            $index = array_search($id, array_column($links, 'id'));
            if ($index !== false) {
                if ($direction === 'up' && $index > 0) {
                    $prev = $links[$index-1];
                    $linksModel->swapOrder($id, $prev['id']);
                } elseif ($direction === 'down' && $index < count($links)-1) {
                    $next = $links[$index+1];
                    $linksModel->swapOrder($id, $next['id']);
                }
            }
        }
        header('Location: /admin/links');
        exit;
    }
    public function index()
    {
        $this->requireAdmin();
        global $config;
        $db = new DB($config);
        $linksModel = new Links($db, $config);
        $links = $linksModel->getAll();
        include __DIR__ . '/../links/views/list.php';
    }
    public function add()
    {
        $this->requireAdmin();
        global $config;
        $db = new DB($config);
        $linksModel = new Links($db, $config);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $url = trim($_POST['url'] ?? '');
            $icon = trim($_POST['icon'] ?? '');
            $nsfw = !empty($_POST['nsfw']) ? 1 : 0;
            $linksModel->addLink($title, $url, $icon, $nsfw);
            header('Location: /admin/links');
            exit;
        }
        include __DIR__ . '/../links/views/add.php';
    }
    public function edit($id)
    {
        $this->requireAdmin();
        global $config;
        $db = new DB($config);
        $linksModel = new Links($db, $config);
        $link = $linksModel->getById($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $url = trim($_POST['url'] ?? '');
            $icon = trim($_POST['icon'] ?? '');
            $nsfw = !empty($_POST['nsfw']) ? 1 : 0;
            $linksModel->updateLink($id, $title, $url, $icon, $nsfw);
            header('Location: /admin/links');
            exit;
        }
        include __DIR__ . '/../links/views/edit.php';
    }
    public function delete($id)
    {
        $this->requireAdmin();
        global $config;
        $db = new DB($config);
        $linksModel = new Links($db, $config);
        $linksModel->deleteLink($id);
        header('Location: /admin/links');
        exit;
    }
}
