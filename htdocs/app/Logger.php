<?php
namespace App;
// Simple Logger class for the framework

class Logger
{
    protected $logFile;
    protected $logDir;

    public function __construct($config)
    {
        // log_path may be a file, not a directory, so extract directory
        $logPath = $config['log_path'];
        $this->logDir = dirname($logPath);
        if (!is_dir($this->logDir)) {
            // Suppress warning if directory exists due to race condition
            if (!@mkdir($this->logDir, 0777, true) && !is_dir($this->logDir)) {
                throw new \RuntimeException("Logger: Failed to create log directory: {$this->logDir}");
            }
        }
        $this->logFile = $logPath;
    }

    public function log($level, $message, $context = [])
    {
        $date = date('Y-m-d H:i:s');
        $contextStr = $context ? json_encode($context) : '';
        $entry = "[$date] [$level] $message $contextStr" . PHP_EOL;
        file_put_contents($this->logFile, $entry, FILE_APPEND);
    }

    public function info($message, $context = [])
    {
        $this->log('INFO', $message, $context);
    }

    public function warning($message, $context = [])
    {
        $this->log('WARNING', $message, $context);
    }

    public function error($message, $context = [])
    {
        $this->log('ERROR', $message, $context);
    }
}
