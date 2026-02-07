<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $connection;
    private $config;

    private function __construct()
    {
        $this->config = require __DIR__ . '/../../config/database.php';
        $this->connect();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect(): void
    {
        $config = $this->config['connections']['mysql'];
        
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
        
        try {
            $this->connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function insert(string $table, array $data): int
    {
        // Build INSERT query with proper escaping
        $columns = [];
        $placeholders = [];
        $values = [];

        foreach ($data as $key => $value) {
            // Escape column names with backticks to avoid reserved keywords
            $columns[] = "`{$key}`";
            $placeholders[] = "?";
            $values[] = $value;
        }

        $sql = "INSERT INTO `{$table}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        error_log("Database::insert - SQL: $sql");
        error_log("Database::insert - Values: " . json_encode($values));
        
        $stmt = $this->connection->prepare($sql);
        $result = $stmt->execute($values);
        
        error_log("Database::insert - Execute result: " . ($result ? 'true' : 'false'));
        
        $lastId = (int)$this->connection->lastInsertId();
        error_log("Database::insert - Last insert ID: $lastId");
        
        return $lastId;
    }

    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $set = [];
        $values = [];
        
        foreach ($data as $field => $value) {
            $set[] = "`{$field}` = ?";
            $values[] = $value;
        }
        
        $sql = "UPDATE `{$table}` SET " . implode(', ', $set) . " WHERE {$where}";
        
        // Combine SET values with WHERE parameters
        $allParams = array_merge($values, $whereParams);
        
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($allParams);
        return $stmt->rowCount();
    }

    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->connection->commit();
    }

    public function rollback(): bool
    {
        return $this->connection->rollback();
    }
}