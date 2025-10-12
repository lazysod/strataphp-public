<?php
// test_log_write.php
$logPath = __DIR__ . '/../storage/logs/app.log';
$user = get_current_user();
$whoami = trim(shell_exec('whoami'));
$logDir = dirname($logPath);

$result = [
    'php_user' => $user,
    'shell_whoami' => $whoami,
    'log_path' => $logPath,
    'log_dir_exists' => is_dir($logDir),
    'log_file_exists' => file_exists($logPath),
    'log_file_writable' => is_writable($logPath),
    'log_dir_writable' => is_writable($logDir),
    'write_result' => null,
    'error' => null
];

try {
    $test = file_put_contents($logPath, "Test log entry: " . date('c') . "\n", FILE_APPEND);
    $result['write_result'] = $test;
} catch (Throwable $e) {
    $result['error'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
