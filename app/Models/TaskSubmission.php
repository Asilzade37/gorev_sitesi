<?php
namespace App\Models;

use App\Core\Database;

class TaskSubmission
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data)
    {
        $sql = "INSERT INTO task_submissions (task_id, user_id, proof) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['task_id'],
            $data['user_id'],
            $data['proof']
        ]);
    }

    public function updateStatus($id, $status, $rejectionReason = null)
    {
        $sql = "UPDATE task_submissions SET status = ?, rejection_reason = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $rejectionReason, $id]);
    }

    public function getPending()
    {
        $sql = "SELECT ts.*, t.title, u.username 
                FROM task_submissions ts
                JOIN tasks t ON ts.task_id = t.id
                JOIN users u ON ts.user_id = u.id
                WHERE ts.status = 'pending'
                ORDER BY ts.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
} 