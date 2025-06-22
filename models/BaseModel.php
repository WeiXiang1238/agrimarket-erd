<?php

/**
 * Base Model Class
 * Contains basic database connection and common properties
 */
abstract class BaseModel
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = [];
    protected $timestamps = true;
    protected $softDeletes = false;
    
    public function __construct()
    {
        $this->db = $this->getConnection();
    }
    
    /**
     * Get database connection
     */
    protected function getConnection()
    {
        static $connection = null;
        
        if ($connection === null) {
            try {
                // Include database configuration
                $host = 'localhost';
                $user = 'root';
                $pass = '';
                $dbname = 'group_assignment';
                
                $connection = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                    $user,
                    $pass,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        
        return $connection;
    }
    
    /**
     * Get table name
     */
    public function getTable()
    {
        return $this->table;
    }
    
    /**
     * Get primary key
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }
    
    /**
     * Get fillable attributes
     */
    public function getFillable()
    {
        return $this->fillable;
    }
    
    /**
     * Get database connection
     */
    public function getDb()
    {
        return $this->db;
    }
    
    /**
     * Find record by ID
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $params = [$id];
        
        // Add soft delete condition only if softDeletes is enabled
        if ($this->softDeletes) {
            $sql .= " AND is_archive = 0";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    /**
     * Get all records
     */
    public function findAll($conditions = [], $limit = null, $offset = 0) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        // Add soft delete condition only if softDeletes is enabled
        if ($this->softDeletes) {
            $sql .= " WHERE is_archive = 0";
        } else {
            $sql .= " WHERE 1=1";
        }
        
        if (!empty($conditions)) {
            foreach ($conditions as $field => $value) {
                $sql .= " AND {$field} = ?";
                $params[] = $value;
            }
        }
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Create new record
     */
    public function create($data) {
        $fields = array_keys($data);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES ({$placeholders})";
        
        $stmt = $this->db->prepare($sql);
        $values = array_values($data);
        
        if ($stmt->execute($values)) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update record
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $field => $value) {
            $fields[] = "{$field} = ?";
            $values[] = $value;
        }
        
        $values[] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(',', $fields) . " WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($values);
    }
    
    /**
     * Soft delete record (set is_archive = 1)
     */
    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET is_archive = 1 WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Hard delete record (permanently remove)
     */
    public function hardDelete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Count records
     */
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $params = [];
        
        // Add soft delete condition only if softDeletes is enabled
        if ($this->softDeletes) {
            $sql .= " WHERE is_archive = 0";
        } else {
            $sql .= " WHERE 1=1";
        }
        
        if (!empty($conditions)) {
            foreach ($conditions as $field => $value) {
                $sql .= " AND {$field} = ?";
                $params[] = $value;
            }
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row['total'];
    }
    
    /**
     * Close database connection
     */
    public function __destruct() {
        // PDO connections are automatically closed when the object is destroyed
        $this->db = null;
    }
}

?> 