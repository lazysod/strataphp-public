<?php
// Down seed: Remove example users
return function($db) {
    $db->query("DELETE FROM users WHERE email IN ('alice@example.com', 'bob@example.com')");
};
