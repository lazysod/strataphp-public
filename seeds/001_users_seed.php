<?php
return function($db) {
    $db->query("INSERT INTO users (first_name, second_name, email, pwd, is_admin, active, security_hash) VALUES
        (?,?,?,?, 1, 1,?),
        (?,?,?,?, 0, 1,?)
    ON DUPLICATE KEY UPDATE email=email;", [
        'Alice', 'Admin', 'alice@example.com', password_hash('admin123', PASSWORD_DEFAULT), bin2hex(random_bytes(32)),
        'Bob', 'User', 'bob@example.com', password_hash('user123', PASSWORD_DEFAULT), bin2hex(random_bytes(32))
    ]);
};