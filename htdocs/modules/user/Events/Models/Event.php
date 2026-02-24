<?php
namespace App\Modules\User\Events\Models;

class Event
{
    public $id;
    public $title;
    public $content;
    public $status;
    public $link;

    public function __construct($id = null, $title = '', $content = '', $link = '', $status = 0)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->link = $link;
        // Accept 'active'/'inactive' or 1/0, always store as int
        if ($status === 'active' || $status === 1 || $status === '1') {
            $this->status = 1;
        } else {
            $this->status = 0;
        }
    }

    // Database-backed event storage
    public static function getByUserId($userId, $db)
    {
        $row = $db->fetch('SELECT * FROM  events WHERE user_id = ? LIMIT 1', [$userId]);
        if ($row) {
            return new self($row['ev_id'], $row['ev_title'], $row['ev_content'], $row['ev_link'], $row['status']);
        }
        return null;
    }

    public function save($userId, $db)
    {
        $existing = self::getByUserId($userId, $db);
        error_log('Event::save called for user_id ' . $userId . ' | existing: ' . ($existing ? 'yes' : 'no') . ' | title: ' . $this->title . ' | content: ' . $this->content . ' | status: ' . $this->status);
        if ($existing) {
            // Update
            $result = $db->query('UPDATE events SET ev_title = ?, ev_content = ?, ev_link = ?, status = ? WHERE user_id = ?', [
                $this->title, $this->content, $this->link, $this->status, $userId
            ]);
            if ($result === false) {
                error_log('Event update failed for user_id ' . $userId . ': ' . print_r($db->errorInfo(), true));
            }
        } else {
            // Insert
            $result = $db->query('INSERT INTO events (user_id, ev_title, ev_content, ev_link, status) VALUES (?, ?, ?, ?, ?)', [
                $userId, $this->title, $this->content, $this->link, $this->status
            ]);
            if ($result === false) {
                error_log('Event insert failed for user_id ' . $userId . ': ' . print_r($db->errorInfo(), true));
            } else {
                $this->id = $db->insertId();
            }
        }
        return $this;
    }
}
