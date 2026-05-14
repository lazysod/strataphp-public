<?php
/**
 * Migration: Add session_lookup index to user_sessions for fast session validation
 */

return [
    'up' => function($db) {
        $db->query("CREATE INDEX session_lookup ON user_sessions (session_token, device_id, revoked, expires_at)");
    },
    'down' => function($db) {
        $db->query("DROP INDEX session_lookup ON user_sessions");
    }
];
