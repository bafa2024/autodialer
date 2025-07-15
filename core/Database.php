<?php

/**
 * Database Class for AutoDial Pro
 * Handles SQLite database connections and operations
 */
class Database
{
    private static $instance = null;
    private $pdo;
    private $databasePath;

    private function __construct()
    {
        $this->databasePath = __DIR__ . '/../database/autodialer.db';
        $this->connect();
    }

    /**
     * Get singleton instance
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Connect to SQLite database
     */
    private function connect()
    {
        try {
            // Create database directory if it doesn't exist
            $databaseDir = dirname($this->databasePath);
            if (!is_dir($databaseDir)) {
                mkdir($databaseDir, 0755, true);
            }

            // Create database file if it doesn't exist
            if (!file_exists($this->databasePath)) {
                $this->initializeDatabase();
            }

            $this->pdo = new PDO('sqlite:' . $this->databasePath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Enable foreign key constraints
            $this->pdo->exec('PRAGMA foreign_keys = ON');
            
        } catch (PDOException $e) {
            throw new Exception('Database connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Initialize database with schema
     */
    private function initializeDatabase()
    {
        $schemaFile = __DIR__ . '/../database/schema.sql';
        
        if (!file_exists($schemaFile)) {
            throw new Exception('Database schema file not found');
        }

        $schema = file_get_contents($schemaFile);
        $this->pdo = new PDO('sqlite:' . $this->databasePath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Execute schema
        $this->pdo->exec($schema);
    }

    /**
     * Get PDO instance
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * Execute a query
     */
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception('Query failed: ' . $e->getMessage());
        }
    }

    /**
     * Fetch a single row
     */
    public function fetchOne($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Fetch all rows
     */
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Insert a record and return the last insert ID
     */
    public function insert($table, $data)
    {
        $columns = array_keys($data);
        $placeholders = ':' . implode(', :', $columns);
        $columnList = implode(', ', $columns);
        
        $sql = "INSERT INTO {$table} ({$columnList}) VALUES ({$placeholders})";
        
        $this->query($sql, $data);
        return $this->pdo->lastInsertId();
    }

    /**
     * Update a record
     */
    public function update($table, $data, $where, $whereParams = [])
    {
        $setParts = [];
        foreach (array_keys($data) as $column) {
            $setParts[] = "{$column} = :{$column}";
        }
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        
        $params = array_merge($data, $whereParams);
        $stmt = $this->query($sql, $params);
        
        return $stmt->rowCount();
    }

    /**
     * Delete a record
     */
    public function delete($table, $where, $params = [])
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->query($sql, $params);
        
        return $stmt->rowCount();
    }

    /**
     * Count records
     */
    public function count($table, $where = '1', $params = [])
    {
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$where}";
        $result = $this->fetchOne($sql, $params);
        
        return (int) $result['count'];
    }

    /**
     * Check if a record exists
     */
    public function exists($table, $where, $params = [])
    {
        return $this->count($table, $where, $params) > 0;
    }

    /**
     * Begin a transaction
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit a transaction
     */
    public function commit()
    {
        return $this->pdo->commit();
    }

    /**
     * Rollback a transaction
     */
    public function rollback()
    {
        return $this->pdo->rollback();
    }

    /**
     * Execute a transaction
     */
    public function transaction($callback)
    {
        try {
            $this->beginTransaction();
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Get table information
     */
    public function getTableInfo($table)
    {
        $sql = "PRAGMA table_info({$table})";
        return $this->fetchAll($sql);
    }

    /**
     * Get all tables
     */
    public function getTables()
    {
        $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'";
        $tables = $this->fetchAll($sql);
        
        return array_column($tables, 'name');
    }

    /**
     * Backup database
     */
    public function backup($backupPath)
    {
        $backupDir = dirname($backupPath);
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        return copy($this->databasePath, $backupPath);
    }

    /**
     * Restore database from backup
     */
    public function restore($backupPath)
    {
        if (!file_exists($backupPath)) {
            throw new Exception('Backup file not found');
        }
        
        // Close current connection
        $this->pdo = null;
        
        // Copy backup to database location
        $result = copy($backupPath, $this->databasePath);
        
        // Reconnect
        $this->connect();
        
        return $result;
    }

    /**
     * Optimize database
     */
    public function optimize()
    {
        $this->pdo->exec('VACUUM');
        $this->pdo->exec('ANALYZE');
    }

    /**
     * Get database size
     */
    public function getSize()
    {
        if (file_exists($this->databasePath)) {
            return filesize($this->databasePath);
        }
        return 0;
    }

    /**
     * Get database statistics
     */
    public function getStats()
    {
        $tables = $this->getTables();
        $stats = [];
        
        foreach ($tables as $table) {
            $count = $this->count($table);
            $stats[$table] = $count;
        }
        
        return $stats;
    }

    /**
     * Escape a string for LIKE queries
     */
    public function escapeLike($string)
    {
        return str_replace(['%', '_'], ['\\%', '\\_'], $string);
    }

    /**
     * Build a WHERE clause for search
     */
    public function buildSearchWhere($searchTerm, $columns)
    {
        $searchTerm = $this->escapeLike($searchTerm);
        $conditions = [];
        
        foreach ($columns as $column) {
            $conditions[] = "{$column} LIKE '%{$searchTerm}%'";
        }
        
        return implode(' OR ', $conditions);
    }

    /**
     * Paginate results
     */
    public function paginate($sql, $params = [], $page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM ({$sql}) as subquery";
        $totalResult = $this->fetchOne($countSql, $params);
        $total = (int) $totalResult['total'];
        
        // Get paginated results
        $paginatedSql = $sql . " LIMIT {$perPage} OFFSET {$offset}";
        $results = $this->fetchAll($paginatedSql, $params);
        
        return [
            'data' => $results,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'pages' => ceil($total / $perPage),
                'has_next' => $page < ceil($total / $perPage),
                'has_prev' => $page > 1
            ]
        ];
    }
} 