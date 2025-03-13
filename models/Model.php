<?php
/**
 * Base Model Class
 * Abstract class for all models with common CRUD operations
 */
abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct() {
        $this->db = Db::getInstance();
    }

    // Generic CRUD Operations
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function all() {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        // Remove any keys that are not actual database columns
        $filteredData = array_filter($data, function ($key) {
            // Exclude 'type' and any other non-database columns
            return $key !== 'type' &&
                $key !== 'type_label' &&
                $key !== 'type_color';
        }, ARRAY_FILTER_USE_KEY);

        // Prepare the SQL query
        $columns = implode(', ', array_keys($filteredData));
        $placeholders = ':' . implode(', :', array_keys($filteredData));

        $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        try {
            $stmt = $this->db->prepare($query);

            // Bind parameters
            foreach ($filteredData as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }

            // Execute the query
            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }

            return false;
        } catch (PDOException $e) {
            // Log the error
            error_log('Create error in ' . get_class($this) . ': ' . $e->getMessage());
            return false;
        }
    }

    public function update($id, array $data) {
        $setClause = '';
        foreach ($data as $column => $value) {
            $setClause .= "$column = :$column, ";
        }
        $setClause = rtrim($setClause, ', ');

        $data[$this->primaryKey] = $id;

        $stmt = $this->db->prepare("UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = :{$this->primaryKey}");
        return $stmt->execute($data);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function where($column, $value) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = :value");
        $stmt->execute(['value' => $value]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new record
     * @param array $data
     * @return int|false
     */

}

