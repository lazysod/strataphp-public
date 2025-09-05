#!/usr/bin/env php
<?php
// CLI script to create the first admin user for the framework
// Usage: php bin/create_admin.php [--first "First"] [--second "Second"] [--email "email@domain"] [--password "pass"] [--display "Display Name"]

require_once __DIR__ . '/../htdocs/app/config.php';
require_once __DIR__ . '/../htdocs/app/DB.php';
require_once __DIR__ . '/../htdocs/app/User.php';

use App\DB;
use App\User;
function prompt($prompt, $hidden = false) {
    if ($hidden && strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        // Hide input (UNIX only)
        echo $prompt;
        system('stty -echo');
        $input = rtrim(fgets(STDIN), "\n");
        system('stty echo');
        echo "\n";
        return $input;
    } else {
        echo $prompt;
        return rtrim(fgets(STDIN), "\n");
    }
}

// Parse CLI args
$options = getopt('', ['first:', 'second:', 'email:', 'password:', 'display::']);

$first = $options['first'] ?? null;
$second = $options['second'] ?? null;
$email = $options['email'] ?? null;
$password = $options['password'] ?? null;
$display = $options['display'] ?? '';

if (!$first) $first = prompt('First name: ');
if (!$second) $second = prompt('Second name: ');
if (!$email) $email = prompt('Email: ');
if (!$password) $password = prompt('Password: ', true);
if ($display === null) $display = prompt('Display name (optional): ');

if ($first === '' || $second === '' || $email === '' || $password === '') {
    fwrite(STDERR, "All fields except display name are required.\n");
    exit(1);
}

$config = $config ?? require __DIR__ . '/../htdocs/app/config.php';
$db = new DB($config);
$userModel = new User($db, $config);

$userModel->createUser([
    'first_name' => $first,
    'second_name' => $second,
    'email' => $email,
    'is_admin' => 1,
    'pwd' => $password
]);

echo "Admin user created successfully!\n";
