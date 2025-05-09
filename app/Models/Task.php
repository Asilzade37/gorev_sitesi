<?php
namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;
use Exception;

class Task extends Model
{
    protected $table = 'tasks';

    public function getAll()
    {
        $sql = "SELECT t.*, c.name as category, c.image as category_image,
                (SELECT COUNT(*) FROM task_submissions WHERE task_id = t.id AND status = 'completed') as completed_count 
                FROM tasks t 
                LEFT JOIN categories c ON t.category_id = c.id 
                ORDER BY t.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function create($data)
    {
        // Şehirleri virgülle ayrılmış string'e çevir
        if (isset($data['cities']) && is_array($data['cities'])) {
            $data['city'] = implode(',', $data['cities']);
            unset($data['cities']);
        }

        return parent::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'reward' => $data['reward'],
            'quota' => $data['quota'] ?? 1,
            'gender' => $data['gender'] ?? 'all',
            'city' => $data['city'] ?? '',
            'daily_limit' => $data['daily_limit'] ?? 1,
            'user_limit' => $data['user_limit'] ?? 1,
            'guide_level' => $data['guide_level'] ?? 1,
            'status' => $data['status'] ?? 'active'
        ]);
    }

    public function update($data)
    {
        // Şehirleri virgülle ayrılmış string'e çevir
        if (isset($data['cities']) && is_array($data['cities'])) {
            $data['city'] = implode(',', $data['cities']);
            unset($data['cities']);
        }

        return parent::update([
            'id' => $data['id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'reward' => $data['reward'],
            'quota' => $data['quota'] ?? 1,
            'gender' => $data['gender'] ?? 'all',
            'city' => $data['city'] ?? '',
            'daily_limit' => $data['daily_limit'] ?? 1,
            'user_limit' => $data['user_limit'] ?? 1,
            'guide_level' => $data['guide_level'] ?? 1,
            'status' => $data['status'] ?? 'active'
        ]);
    }

    public function getById($id)
    {
        $sql = "SELECT t.*, c.name as category 
                FROM tasks t 
                LEFT JOIN categories c ON t.category_id = c.id 
                WHERE t.id = ?";
        return $this->db->query($sql, [$id])->fetch(PDO::FETCH_OBJ);
    }

    public function find($id)
    {
        $sql = "SELECT * FROM tasks WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAvailableTasks()
    {
        $sql = "SELECT t.*, c.name as category, c.image as category_image,
                (SELECT COUNT(*) FROM task_submissions WHERE task_id = t.id AND status = 'completed') as completed_count 
                FROM tasks t 
                LEFT JOIN categories c ON t.category_id = c.id 
                WHERE t.status = 'active' 
                ORDER BY t.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getUserTasks($userId)
    {
        $sql = "SELECT t.*, c.name as category, ts.status as submission_status 
                FROM tasks t 
                LEFT JOIN categories c ON t.category_id = c.id 
                LEFT JOIN task_submissions ts ON t.id = ts.task_id AND ts.user_id = ?
                WHERE ts.user_id = ?
                ORDER BY ts.created_at DESC";
        return $this->db->query($sql, [$userId, $userId])->fetchAll(PDO::FETCH_OBJ);
    }

    public function submitTask($data)
    {
        try {
            // Önce veritabanı bağlantısını kontrol et
            if (!$this->db) {
                error_log("Database connection is null");
                return false;
            }

            // SQL sorgusunu hazırla
            $sql = "INSERT INTO task_submissions (task_id, user_id, proof, image_path, status) 
                    VALUES (:task_id, :user_id, :proof, :image_path, :status)";
            
            // Parametreleri hazırla
            $params = [
                'task_id' => $data['task_id'],
                'user_id' => $data['user_id'],
                'proof' => $data['proof'],
                'image_path' => $data['image_path'] ?? null,
                'status' => $data['status']
            ];

            // Debug için parametreleri logla
            error_log("Task submission parameters: " . print_r($params, true));

            // Sorguyu çalıştır
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                error_log("Failed to prepare statement: " . print_r($this->db->errorInfo(), true));
                return false;
            }

            $result = $stmt->execute($params);
            if (!$result) {
                error_log("Failed to execute statement: " . print_r($stmt->errorInfo(), true));
                return false;
            }

            return true;
        } catch (PDOException $e) {
            error_log("Task submission error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        } catch (Exception $e) {
            error_log("Unexpected error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function getSubmission($taskId, $userId)
    {
        $sql = "SELECT * FROM task_submissions 
                WHERE task_id = ? AND user_id = ? 
                ORDER BY created_at DESC 
                LIMIT 1";
        return $this->db->query($sql, [$taskId, $userId])->fetch(PDO::FETCH_OBJ);
    }

    public function getTasksWithSubmissions($status = 'pending')
    {
        $sql = "SELECT ts.*, t.title as task_title, u.name as user_name, u.email as user_email
                FROM task_submissions ts
                JOIN tasks t ON ts.task_id = t.id
                JOIN users u ON ts.user_id = u.id
                WHERE ts.status = :status";

        // Eğer tamamlanan görevleri istiyorsak, onaylanmış olanları göster
        if ($status === 'completed') {
            $sql = "SELECT ts.*, t.title as task_title, u.name as user_name, u.email as user_email
                    FROM task_submissions ts
                    JOIN tasks t ON ts.task_id = t.id
                    JOIN users u ON ts.user_id = u.id
                    WHERE ts.status = 'approved'";
        }

        $sql .= " ORDER BY ts.created_at DESC";
        
        return $this->db->query($sql, ['status' => $status])->fetchAll(PDO::FETCH_OBJ);
    }

    public function updateSubmission($submissionId, $data)
    {
        try {
            $sql = "UPDATE task_submissions 
                    SET status = :status, 
                        rejection_reason = :rejection_reason,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id";

            $params = [
                'id' => $submissionId,
                'status' => $data['status'],
                'rejection_reason' => $data['rejection_reason']
            ];

            return $this->db->query($sql, $params)->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Update submission error: " . $e->getMessage());
            return false;
        }
    }

    public function getCompletedTasksCount($userId)
    {
        $sql = "SELECT COUNT(*) as count FROM task_submissions 
                WHERE user_id = ? AND status = 'approved'";
        $result = $this->db->query($sql, [$userId])->fetch(PDO::FETCH_OBJ);
        return $result->count ?? 0;
    }

    public function getPendingTasksCount($userId)
    {
        $sql = "SELECT COUNT(*) as count FROM task_submissions 
                WHERE user_id = ? AND status = 'pending'";
        $result = $this->db->query($sql, [$userId])->fetch(PDO::FETCH_OBJ);
        return $result->count ?? 0;
    }

    public function getTotalEarnings($userId)
    {
        $sql = "SELECT SUM(t.reward) as total 
                FROM task_submissions ts
                JOIN tasks t ON ts.task_id = t.id
                WHERE ts.user_id = ? AND ts.status = 'approved'";
        $result = $this->db->query($sql, [$userId])->fetch(PDO::FETCH_OBJ);
        return $result->total ?? 0;
    }

    public function getRecentTasks($userId, $limit = 5)
    {
        $sql = "SELECT t.*, ts.status, ts.created_at
                FROM task_submissions ts
                JOIN tasks t ON ts.task_id = t.id
                WHERE ts.user_id = ?
                ORDER BY ts.created_at DESC
                LIMIT ?";
        return $this->db->query($sql, [$userId, $limit])->fetchAll(PDO::FETCH_OBJ);
    }

    public function getRecentActivities($userId, $limit = 5)
    {
        $sql = "SELECT 
                    'success' as type,
                    CASE 
                        WHEN ts.status = 'approved' THEN CONCAT(t.title, ' görevi onaylandı')
                        WHEN ts.status = 'pending' THEN CONCAT(t.title, ' görevi gönderildi')
                        WHEN ts.status = 'rejected' THEN CONCAT(t.title, ' görevi reddedildi')
                    END as message,
                    ts.created_at,
                    CASE 
                        WHEN TIMESTAMPDIFF(MINUTE, ts.created_at, NOW()) < 60 
                        THEN CONCAT(TIMESTAMPDIFF(MINUTE, ts.created_at, NOW()), ' dakika önce')
                        WHEN TIMESTAMPDIFF(HOUR, ts.created_at, NOW()) < 24 
                        THEN CONCAT(TIMESTAMPDIFF(HOUR, ts.created_at, NOW()), ' saat önce')
                        ELSE DATE_FORMAT(ts.created_at, '%d.%m.%Y %H:%i')
                    END as time_ago
                FROM task_submissions ts
                JOIN tasks t ON ts.task_id = t.id
                WHERE ts.user_id = ?
                ORDER BY ts.created_at DESC
                LIMIT ?";
        return $this->db->query($sql, [$userId, $limit])->fetchAll(PDO::FETCH_OBJ);
    }

    public function getUserCompletedTasks($userId)
    {
        $sql = "SELECT t.*, ts.status, ts.created_at as completed_at
                FROM task_submissions ts
                JOIN tasks t ON ts.task_id = t.id
                WHERE ts.user_id = ? AND ts.status = 'approved'
                ORDER BY ts.created_at DESC";
        return $this->db->query($sql, [$userId])->fetchAll(PDO::FETCH_OBJ);
    }

    public function getUserPendingTasks($userId)
    {
        $sql = "SELECT t.*, ts.status, ts.created_at as submitted_at
                FROM task_submissions ts
                JOIN tasks t ON ts.task_id = t.id
                WHERE ts.user_id = ? AND ts.status = 'pending'
                ORDER BY ts.created_at DESC";
        return $this->db->query($sql, [$userId])->fetchAll(PDO::FETCH_OBJ);
    }

    public function getTasksByCategory($categoryId)
    {
        try {
            $sql = "SELECT t.*, c.name as category, c.image as category_image,
                    (SELECT COUNT(*) FROM task_submissions WHERE task_id = t.id AND status = 'completed') as completed_count 
                    FROM tasks t 
                    LEFT JOIN categories c ON t.category_id = c.id 
                    WHERE t.status = 'active' AND t.category_id = ?
                    ORDER BY t.created_at DESC";
            
            error_log("Executing getTasksByCategory query with category ID: " . $categoryId);
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$categoryId]);
            $results = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            error_log("Retrieved tasks for category: " . print_r($results, true));
            return $results;
        } catch (\Exception $e) {
            error_log("Error in getTasksByCategory: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return [];
        }
    }
} 