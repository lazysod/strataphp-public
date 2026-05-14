<?php
namespace App;

class Logger
{
    protected $logFile;
    protected $logDir;
    protected $minLevel;
    
    private static $instance = null;
    
    const LEVELS = [
        'DEBUG' => 0,
        'INFO' => 1, 
        'WARNING' => 2,
        'ERROR' => 3
    ];

    public function __construct($config)
    {
        $logPath = $config['log_path'] ?? (__DIR__ . '/../../storage/logs/app.log');
        $this->logDir = dirname($logPath);
        if (!is_dir($this->logDir)) {
            if (!@mkdir($this->logDir, 0777, true) && !is_dir($this->logDir)) {
                throw new \RuntimeException("Logger: Failed to create log directory: {$this->logDir}");
            }
        }
        $this->logFile = $logPath;
        $this->minLevel = self::LEVELS[$config['log_level'] ?? 'INFO'];
    }
    
    // Singleton so you can do Logger::getInstance()->info() or make static wrappers
    public static function getInstance($config = null)
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function log($level, $message, $context = [])
    {
        if (self::LEVELS[$level] < $this->minLevel) {
            return; // Skip debug logs in prod
        }
        
        $date = date('Y-m-d H:i:s');
        $contextStr = $context ? ' ' . json_encode($context, JSON_UNESCAPED_SLASHES) : '';
        $entry = "[$date] [$level] $message$contextStr" . PHP_EOL;
        file_put_contents($this->logFile, $entry, FILE_APPEND | LOCK_EX); // Added LOCK_EX
    }

    public function debug($message, $context = [])
    {
        $this->log('DEBUG', $message, $context);
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