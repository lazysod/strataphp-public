<?php
declare(strict_types=1);

namespace App;

class Logger
{
    protected string $logFile;
    protected string $logDir;
    protected int $minLevel;

    private static ?self $instance = null;

    public const LEVELS = [
        'DEBUG' => 0,
        'INFO' => 1,
        'NOTICE' => 1,
        'WARNING' => 2,
        'ERROR' => 3,
        'CRITICAL' => 3,
        'ALERT' => 3,
        'EMERGENCY' => 3,
    ];

    public function __construct(array $config = [])
    {
        $logPath = $config['log_path'] 
            ?? $config['log']['path'] 
            ?? $config['db']['log_path'] 
            ?? (__DIR__ . '/../../storage/logs/app.log');

        $this->logDir = dirname($logPath);
        if (!is_dir($this->logDir)) {
            if (!@mkdir($this->logDir, 0755, true) && !is_dir($this->logDir)) {
                throw new \RuntimeException("Logger: Failed to create log directory: {$this->logDir}");
            }
        }
        if (!is_writable($this->logDir)) {
            throw new \RuntimeException("Logger: Log directory not writable: {$this->logDir}");
        }

        $this->logFile = $logPath;

        $levelName = strtoupper($config['log_level'] ?? $config['log']['level'] ?? 'INFO');
        $this->minLevel = self::LEVELS[$levelName] ?? self::LEVELS['INFO'];
    }

    public static function getInstance(array $config = []): self
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public static function setInstance(?self $logger): void
    {
        self::$instance = $logger;
    }

    public static function clearInstance(): void
    {
        self::$instance = null;
    }

    public function log($level, $message, array $context = []): void
    {
        $level = strtoupper((string)$level);
        $levelValue = self::LEVELS[$level] ?? 999;

        if ($levelValue < $this->minLevel) {
            return;
        }

        $date = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        
        $contextStr = '';
        if (!empty($context)) {
            $json = json_encode($context, JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE);
            $contextStr = ' ' . ($json !== false ? $json : '[context encoding failed]');
        }

        // PSR-3 style {key} interpolation
        if (!empty($context)) {
            foreach ($context as $k => $v) {
                if (is_scalar($v) || (is_object($v) && method_exists($v, '__toString'))) {
                    $message = str_replace('{' . $k . '}', (string)$v, $message);
                }
            }
        }

        $entry = "[$date] [$level] $message$contextStr" . PHP_EOL;
        @file_put_contents($this->logFile, $entry, FILE_APPEND | LOCK_EX);
    }

    public function emergency($message, array $context = []): void { $this->log('EMERGENCY', $message, $context); }
    public function alert($message, array $context = []): void { $this->log('ALERT', $message, $context); }
    public function critical($message, array $context = []): void { $this->log('CRITICAL', $message, $context); }
    public function error($message, array $context = []): void { $this->log('ERROR', $message, $context); }
    public function warning($message, array $context = []): void { $this->log('WARNING', $message, $context); }
    public function notice($message, array $context = []): void { $this->log('NOTICE', $message, $context); }
    public function info($message, array $context = []): void { $this->log('INFO', $message, $context); }
    public function debug($message, array $context = []): void { $this->log('DEBUG', $message, $context); }
}