<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model
{
    protected $table = 'users';

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        return $this->db->query($sql, [$email])->fetch(PDO::FETCH_OBJ);
    }

    public function create($data)
    {
        return parent::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => $data['password'],
            'role' => $data['role'] ?? 'user',
            'status' => $data['status'] ?? 'active'
        ]);
    }

    public function update($data)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return parent::update($data);
    }

    public function updateBalance($userId, $amount)
    {
        $sql = "UPDATE users SET balance = balance + ? WHERE id = ?";
        return $this->db->query($sql, [$amount, $userId]);
    }

    public function find($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        return $this->db->query($sql, [$id])->fetch(PDO::FETCH_OBJ);
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getUserLevel($userId)
    {
        $sql = "SELECT COUNT(*) as completed_tasks 
                FROM task_submissions 
                WHERE user_id = ? AND status = 'approved'";
        $result = $this->db->query($sql, [$userId])->fetch(PDO::FETCH_OBJ);
        $completedTasks = $result->completed_tasks ?? 0;
        
        // Her 10 tamamlanan görev için 1 seviye
        return floor($completedTasks / 10) + 1;
    }
} 