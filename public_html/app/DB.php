<?php
declare(strict_types=1);

namespace App;

use PDO;
use PDOException;
use App\Logger;

class DB
{
    protected ?PDO $pdo = null;
    protected array $config;
    protected ?Logger $logger = null;

    public function __construct(array $config, ?Logger $logger = null)
    {
        $this->config = $config;
        $this->logger = $logger;

        // Fallback for legacy bootstrap
        if ($this->logger === null && class_exists('App\\Logger')) {
            $this->logger = new Logger($config);
        }

        $db = isset($config['db']) && is_array($config['db']) ? $config['db'] : $config;
        $host = $db['host'] ?? 'localhost';
        $database = $db['database'] ?? $db['name'] ?? '';
        $username = $db['username'] ?? '';
        $password = $db['password'] ?? '';
        $charset = $db['charset'] ?? 'utf8mb4';

        if ($database === '') {
            throw new \InvalidArgumentException('DB config must contain a database name.');
        }

        $dsn = "mysql:host={$host};dbname={$database};charset={$charset}";
        if (!empty($db['port'])) {
            $dsn .= ';port=' . (int) $db['port'];
        }
        if (!empty($db['unix_socket'])) {
            $dsn .= ';unix_socket=' . $db['unix_socket'];
        }

        try {
            $this->pdo = new PDO(
                $dsn,
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_STRINGIFY_FETCHES => false,
                ]
            );
        } catch (PDOException $e) {
            $this->logError('Database connection failed', ['error' => $e->getMessage(), 'host' => $host]);
            throw $e;
        }
    }

    protected function logError(string $message, array $context = []): void
    {
        if ($this->logger && method_exists($this->logger, 'error')) {
            $this->logger->error($message, $context);
        }
    }

    public function getPdo(): PDO
    {
        if (!$this->pdo) {
            throw new \RuntimeException('Database connection is not established.');
        }
        return $this->pdo;
    }

    /**
     * @throws \RuntimeException|\PDOException
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        if (!$this->pdo) {
            throw new \RuntimeException('Database connection is not established.');
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $this->logError('DB::query failed', [
                'message' => $e->getMessage(),
                'sql' => $sql,
                'params' => $params
            ]);
            throw $e;
        }
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetch(string $sql, array $params = []): array|false
    {
        return $this->query($sql, $params)->fetch();
    }

    public function beginTransaction(): bool
    {
        return $this->getPdo()->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->getPdo()->commit();
    }

    public function rollBack(): bool
    {
        return $this->getPdo()->rollBack();
    }

    public function insertId(): string
    {
        return $this->getPdo()->lastInsertId();
    }

    public function rowCount(\PDOStatement $stmt): int
    {
        return $stmt->rowCount();
    }

    // Backwards compat - prefer rowCount()
    public function affectedRows(\PDOStatement $stmt): int
    {
        return $this->rowCount($stmt);
    }
}