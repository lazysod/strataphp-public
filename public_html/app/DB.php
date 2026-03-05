<?php
namespace App;

use PDO;
use PDOException;
use App\Logger;

class DB
{
    protected $pdo;

    public function __construct($config)
    {
        $db = $config['db'];
        $dsn = "mysql:host={$db['host']};dbname={$db['database']};charset=utf8mb4";
        try {
            $this->pdo = new PDO(
                $dsn,
                $db['username'],
                $db['password'],
                [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            // Log the error if Logger is available
            if (class_exists('Logger')) {
                $logger = new Logger($config);
                $logger->error('Database connection failed', ['error' => $e->getMessage()]);
            }
            // Show 500 error page if it exists
            // If running in CLI, do not include web error views
            if (php_sapi_name() === 'cli') {
                fwrite(STDERR, "Database connection failed: " . $e->getMessage() . "\n");
                // ...existing code...
            }
            $errorPage = __DIR__ . '/../../views/errors/500.php';
            if (file_exists($errorPage)) {
                http_response_code(500);
                include $errorPage;
                exit;
            } else {
                // Fallback: plain error
                http_response_code(500);
                // ...existing code...
            }
        }
    }

    public function getPdo()
    {
        return $this->pdo;
    }

    public function query($sql, $params = [])
    {
        if (!$this->pdo) {
            // Log the error if Logger is available
            if (class_exists('App\\Logger')) {
                $logger = new Logger($config ?? []);
                $logger->error('Database connection is not established.');
            }
            $errorPage = __DIR__ . '/../../views/errors/500.php';
            if (file_exists($errorPage)) {
                http_response_code(500);
                include $errorPage;
                exit;
            } else {
                http_response_code(500);
                echo 'Database connection is not established.';
                exit;
            }
        }
        try {
            $stmt = $this->pdo->prepare($sql);
            if (!$stmt) {
                error_log('DB::query prepare failed: ' . $sql);
                return null;
            }
            $result = $stmt->execute($params);
            if (!$result) {
                error_log('DB::query execute failed: ' . $sql . ' Params: ' . print_r($params, true));
                return null;
            }
            return $stmt;
        } catch (PDOException $e) {
            error_log('DB::query PDOException: ' . $e->getMessage() . ' SQL: ' . $sql . ' Params: ' . print_r($params, true));
            return null;
        }
    }

    public function fetchAll($sql, $params = [])
    {
        if (!$this->pdo) {
            if (class_exists('App\\Logger')) {
                $logger = new Logger($config ?? []);
                $logger->error('Database connection is not established.');
            }
            $errorPage = __DIR__ . '/../../views/errors/500.php';
            if (file_exists($errorPage)) {
                http_response_code(500);
                include $errorPage;
                exit;
            } else {
                http_response_code(500);
                echo 'Database connection is not established.';
                exit;
            }
        }
        $stmt = $this->query($sql, $params);
        if (!$stmt) {
            return [];
        }
        return $stmt->fetchAll();
    }

    public function fetch($sql, $params = [])
    {
        if (!$this->pdo) {
            if (class_exists('App\\Logger')) {
                $logger = new Logger($config ?? []);
                $logger->error('Database connection is not established.');
            }
            $errorPage = __DIR__ . '/../../views/errors/500.php';
            if (file_exists($errorPage)) {
                http_response_code(500);
                include $errorPage;
                exit;
            } else {
                http_response_code(500);
                echo 'Database connection is not established.';
                exit;
            }
        }
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetch() : false;
    }

    public function beginTransaction()
    {
        if (!$this->pdo) {
            throw new \RuntimeException('Database connection is not established.');
        }
        return $this->pdo->beginTransaction();
    }

    public function commit()
    {
        if (!$this->pdo) {
            throw new \RuntimeException('Database connection is not established.');
        }
        return $this->pdo->commit();
    }

    public function rollBack()
    {
        if (!$this->pdo) {
            throw new \RuntimeException('Database connection is not established.');
        }
        return $this->pdo->rollBack();
    }

    public function insertId()
    {
        if (!$this->pdo) {
            throw new \RuntimeException('Database connection is not established.');
        }
        return $this->pdo->lastInsertId();
    }

    public function affectedRows($stmt)
    {
        if (!$this->pdo) {
            throw new \RuntimeException('Database connection is not established.');
        }
        return $stmt->rowCount();
    }

    public function escapeString($str)
    {
        if (!$this->pdo) {
            throw new \RuntimeException('Database connection is not established.');
        }
        return substr($this->pdo->quote($str), 1, -1);
    }

    public function errorInfo()
    {
        if (!$this->pdo) {
            throw new \RuntimeException('Database connection is not established.');
        }
        return $this->pdo->errorInfo();
    }

    public function errorCode()
    {
        if (!$this->pdo) {
            throw new \RuntimeException('Database connection is not established.');
        }
        return $this->pdo->errorCode();
    }
}
