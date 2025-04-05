<?php
/**
 * Base Model Class
 */
class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Db::getInstance();
    }

    /**
     * Find a record by ID
     * @param int $id ID
     * @return array|false
     */
    public function find($id) {
        if (empty($this->table)) {
            throw new Exception("Table name not defined in model");
        }

        $query = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all records
     * @return array
     */
    public function all() {
        if (empty($this->table)) {
            throw new Exception("Table name not defined in model");
        }

        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new record
     * @param array $data Data to create
     * @return int|false Last insert ID or false on failure
     */
    public function create($data) {
        if (empty($this->table)) {
            throw new Exception("Table name not defined in model");
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($query);

        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Update a record
     * @param int $id ID
     * @param array $data Data to update
     * @return bool
     */
    public function update($id, $data) {
        if (empty($this->table)) {
            throw new Exception("Table name not defined in model");
        }

        $setStatements = [];
        foreach (array_keys($data) as $key) {
            $setStatements[] = "{$key} = :{$key}";
        }

        $query = "UPDATE {$this->table} SET " . implode(', ', $setStatements) . " WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($query);

        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    /**
     * Delete a record
     * @param int $id ID
     * @return bool
     */
    public function delete($id) {
        if (empty($this->table)) {
            throw new Exception("Table name not defined in model");
        }

        $query = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Find records where column equals value
     * @param string $column Column name
     * @param mixed $value Value
     * @return array
     */
    public function where($column, $value) {
        if (empty($this->table)) {
            throw new Exception("Table name not defined in model");
        }

        $query = "SELECT * FROM {$this->table} WHERE {$column} = :value";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['value' => $value]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count records
     * @param string $column Column name for COUNT
     * @return int
     */
    public function count($column = '*') {
        if (empty($this->table)) {
            throw new Exception("Table name not defined in model");
        }

        $query = "SELECT COUNT({$column}) FROM {$this->table}";
        $stmt = $this->db->query($query);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Execute a raw SQL query
     * @param string $query SQL query
     * @param array $params Parameters
     * @param int $fetchMode PDO fetch mode
     * @return mixed
     */
    public function query($query, $params = [], $fetchMode = PDO::FETCH_ASSOC) {
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll($fetchMode);
    }
}