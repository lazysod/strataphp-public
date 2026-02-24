<?php
namespace App\Modules\User\Events\Controllers;

use App\Modules\User\Events\Models\Event;

class EventsController
{
    public function index()
    {
        $db = $this->getDb();
        $userId = $this->getUserId();
        $event = Event::getByUserId($userId, $db);
        include __DIR__ . '/../views/index.php';
    }

    public function show($id = null)
    {
        $db = $this->getDb();
        $userId = $this->getUserId();
        $event = Event::getByUserId($userId, $db);
        include __DIR__ . '/../views/view.php';
    }

    public function create()
    {
        $db = $this->getDb();
        $userId = $this->getUserId();
        $event = Event::getByUserId($userId, $db);
        include __DIR__ . '/../views/form.php';
    }

    public function store($data)
    {
        $db = $this->getDb();
        $userId = $this->getUserId();
        $event = new Event(
            null,
            $data['title'] ?? '',
            $data['content'] ?? '',
            $data['ev_link'] ?? '',
            $data['status'] ?? 'inactive'
        );
        $event->save($userId, $db);
        $_SESSION['event_saved'] = true;
        header('Location: /user/events');
        exit;
    }

    private function getDb()
    {
        // Adjust as needed for your framework
        $config = require $_SERVER['DOCUMENT_ROOT'] . '/app/config.php';
        return new \App\DB($config['db']);
    }

    private function getUserId()
    {
        // Adjust session prefix as needed
        $config = require $_SERVER['DOCUMENT_ROOT'] . '/app/config.php';
        $sessionPrefix = $config['session_prefix'] ?? 'app_';
        return $_SESSION[$sessionPrefix . 'user_id'] ?? null;
    }
}
