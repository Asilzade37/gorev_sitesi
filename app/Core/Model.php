<?php
namespace App\Core;

abstract class Model
{
    protected $db;
    protected $table;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->query($sql)->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        return $this->db->query($sql, [$id])->fetch();
    }

    public function create($data)
    {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';
        
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES ($placeholders)";
        
        return $this->db->query($sql, $values);
    }

    public function update($data)
    {
        if (!isset($data['id'])) {
            return false;
        }

        $id = $data['id'];
        unset($data['id']);

        $fields = array_keys($data);
        $values = array_values($data);
        
        $set = implode('=?,', $fields) . '=?';
        $sql = "UPDATE {$this->table} SET $set WHERE id=?";
        
        $values[] = $id;
        return $this->db->query($sql, $values);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }

    public function count($where = '')
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        if ($where) {
            $sql .= " WHERE $where";
        }
        $result = $this->db->query($sql)->fetch();
        return $result->count;
    }
} 