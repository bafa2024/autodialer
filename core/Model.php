<?php

namespace Core;

use Core\Database;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

abstract class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = ['password', 'token'];
    protected $casts = [];
    protected $rules = [];
    protected $messages = [];
    protected $logger;
    protected $timestamps = true;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->logger = new Logger(get_class($this));
        $this->logger->pushHandler(new StreamHandler('logs/models.log', Logger::DEBUG));
    }

    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $result = $this->db->fetch($sql, [$id]);
        
        if ($result) {
            return $this->castAttributes($result);
        }
        
        return null;
    }

    public function findBy($column, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        $result = $this->db->fetch($sql, [$value]);
        
        if ($result) {
            return $this->castAttributes($result);
        }
        
        return null;
    }

    public function findAll($conditions = [], $orderBy = null, $limit = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $column => $value) {
                $whereClause[] = "{$column} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $results = $this->db->fetchAll($sql, $params);
        
        return array_map([$this, 'castAttributes'], $results);
    }

    public function create($data)
    {
        $data = $this->filterFillable($data);
        
        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        if (!$this->validate($data)) {
            return false;
        }
        
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        try {
            $this->db->query($sql, $data);
            $id = $this->db->lastInsertId();
            
            $this->logger->info("Record created in {$this->table}", ['id' => $id, 'data' => $data]);
            
            return $this->find($id);
        } catch (\Exception $e) {
            $this->logger->error("Failed to create record in {$this->table}: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data)
    {
        $data = $this->filterFillable($data);
        
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        if (!$this->validate($data, $id)) {
            return false;
        }
        
        $setClause = [];
        foreach (array_keys($data) as $column) {
            $setClause[] = "{$column} = :{$column}";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClause) . " WHERE {$this->primaryKey} = :id";
        $data['id'] = $id;
        
        try {
            $affected = $this->db->execute($sql, $data);
            
            if ($affected > 0) {
                $this->logger->info("Record updated in {$this->table}", ['id' => $id, 'data' => $data]);
                return $this->find($id);
            }
            
            return false;
        } catch (\Exception $e) {
            $this->logger->error("Failed to update record in {$this->table}: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        
        try {
            $affected = $this->db->execute($sql, [$id]);
            
            if ($affected > 0) {
                $this->logger->info("Record deleted from {$this->table}", ['id' => $id]);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            $this->logger->error("Failed to delete record from {$this->table}: " . $e->getMessage());
            return false;
        }
    }

    public function count($conditions = [])
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $column => $value) {
                $whereClause[] = "{$column} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        return $this->db->fetchColumn($sql, $params);
    }

    public function exists($id)
    {
        return $this->find($id) !== null;
    }

    protected function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }

    protected function castAttributes($data)
    {
        foreach ($this->casts as $attribute => $cast) {
            if (isset($data[$attribute])) {
                switch ($cast) {
                    case 'int':
                    case 'integer':
                        $data[$attribute] = (int) $data[$attribute];
                        break;
                    case 'float':
                    case 'double':
                        $data[$attribute] = (float) $data[$attribute];
                        break;
                    case 'bool':
                    case 'boolean':
                        $data[$attribute] = (bool) $data[$attribute];
                        break;
                    case 'array':
                    case 'json':
                        $data[$attribute] = json_decode($data[$attribute], true);
                        break;
                    case 'datetime':
                        $data[$attribute] = new \DateTime($data[$attribute]);
                        break;
                }
            }
        }
        
        return $data;
    }

    protected function validate($data, $id = null)
    {
        if (empty($this->rules)) {
            return true;
        }
        
        $errors = [];
        
        foreach ($this->rules as $field => $rules) {
            $rules = explode('|', $rules);
            
            foreach ($rules as $rule) {
                $params = [];
                
                if (strpos($rule, ':') !== false) {
                    list($rule, $param) = explode(':', $rule, 2);
                    $params = explode(',', $param);
                }
                
                $value = $data[$field] ?? null;
                
                if (!$this->validateRule($rule, $value, $params, $data, $id)) {
                    $message = $this->messages["{$field}.{$rule}"] ?? "Validation failed for {$field}";
                    $errors[$field][] = $message;
                }
            }
        }
        
        if (!empty($errors)) {
            $this->logger->warning("Validation failed", ['errors' => $errors]);
            return false;
        }
        
        return true;
    }

    protected function validateRule($rule, $value, $params, $data, $id)
    {
        switch ($rule) {
            case 'required':
                return !empty($value) || $value === '0';
            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
            case 'min':
                return strlen($value) >= $params[0];
            case 'max':
                return strlen($value) <= $params[0];
            case 'unique':
                $table = $params[0] ?? $this->table;
                $column = $params[1] ?? 'id';
                $except = $id ? [$id] : [];
                return !$this->existsInTable($table, $column, $value, $except);
            case 'numeric':
                return is_numeric($value);
            case 'alpha':
                return ctype_alpha($value);
            case 'alphanumeric':
                return ctype_alnum($value);
            default:
                return true;
        }
    }

    protected function existsInTable($table, $column, $value, $except = [])
    {
        $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?";
        $params = [$value];
        
        if (!empty($except)) {
            $sql .= " AND id NOT IN (" . implode(',', array_fill(0, count($except), '?')) . ")";
            $params = array_merge($params, $except);
        }
        
        return $this->db->fetchColumn($sql, $params) > 0;
    }

    public function toArray($data)
    {
        return array_diff_key($data, array_flip($this->hidden));
    }

    public function paginate($page = 1, $perPage = 15, $conditions = [], $orderBy = null)
    {
        $offset = ($page - 1) * $perPage;
        
        $countSql = "SELECT COUNT(*) FROM {$this->table}";
        $dataSql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $column => $value) {
                $whereClause[] = "{$column} = ?";
                $params[] = $value;
            }
            $where = " WHERE " . implode(' AND ', $whereClause);
            $countSql .= $where;
            $dataSql .= $where;
        }
        
        if ($orderBy) {
            $dataSql .= " ORDER BY {$orderBy}";
        }
        
        $dataSql .= " LIMIT {$perPage} OFFSET {$offset}";
        
        $total = $this->db->fetchColumn($countSql, $params);
        $data = $this->db->fetchAll($dataSql, $params);
        
        return [
            'data' => array_map([$this, 'castAttributes'], $data),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }
} 