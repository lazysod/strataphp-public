<?php
/**
 * Migration: Add PKCE, scope, and public client fields to OAuth tables
 */

return [
    'up' => function($db) {
        $db->query("ALTER TABLE oauth_codes ADD COLUMN code_challenge VARCHAR(128) NULL, ADD COLUMN code_challenge_method VARCHAR(10) DEFAULT 'plain', ADD COLUMN scope VARCHAR(255) DEFAULT 'basic', ADD INDEX code_lookup (code, expires_at)");
        $db->query("ALTER TABLE oauth_clients ADD COLUMN is_public TINYINT(1) DEFAULT 0, ADD COLUMN allowed_scopes VARCHAR(255) DEFAULT 'basic'");
    },
    'down' => function($db) {
        $db->query("ALTER TABLE oauth_codes DROP COLUMN code_challenge, DROP COLUMN code_challenge_method, DROP COLUMN scope, DROP INDEX code_lookup");
        $db->query("ALTER TABLE oauth_clients DROP COLUMN is_public, DROP COLUMN allowed_scopes");
    }
];
