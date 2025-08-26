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
                $dsn, $db['username'], $db['password'], [
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
                exit(1);
            }
            $errorPage = __DIR__ . '/../../views/errors/500.php';
            if (file_exists($errorPage)) {
                http_response_code(500);
                include $errorPage;
                exit;
            } else {
                // Fallback: plain error
                http_response_code(500);
                exit('500 Internal Server Error: Database connection failed.');
            }
        }
    }

    public function getPdo()
    {
        return $this->pdo;
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function fetch($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    public function commit()
    {
        return $this->pdo->commit();
    }

    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

    public function insertId()
    {
        return $this->pdo->lastInsertId();
    }

    public function affectedRows($stmt)
    {
        return $stmt->rowCount();
    }

    public function escapeString($str)
    {
        return substr($this->pdo->quote($str), 1, -1);
    }

    public function errorInfo()
    {
        return $this->pdo->errorInfo();
    }

    public function errorCode()
    {
        return $this->pdo->errorCode();
    }
}
