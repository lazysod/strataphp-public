<?php
// htdocs/app/class/Logger.php
// Simple Logger class for the framework

class Logger
{
    protected $logFile;
    protected $logDir;

    public function __construct($config)
    {
        $this->logDir = rtrim($config['log_path'], '/');
        if (!is_dir($this->logDir)) {
            if (!@mkdir($this->logDir, 0777, true) && !is_dir($this->logDir)) {
                throw new \RuntimeException("Logger: Failed to create log directory: {$this->logDir}");
            }
        }
        $this->logFile = $this->logDir . '/app.log';
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
