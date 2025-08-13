<?php
// Seed: Example users
return function($db) {
    $db->query("INSERT INTO users (first_name, second_name, email, pwd, is_admin, active, security_hash) VALUES
        ('Alice', 'Admin', 'alice@example.com', 'password_hash', 1, 1, 'hash1'),
        ('Bob', 'User', 'bob@example.com', 'password_hash', 0, 1, 'hash2')
    ON DUPLICATE KEY UPDATE email=email;");
};
