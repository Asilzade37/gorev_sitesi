<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Category extends Model
{
    protected $table = 'categories';

    public function create($data)
    {
        try {
            error_log("Starting category creation with data: " . print_r($data, true));
            
            // Resim yükleme işlemi
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/gorev_sitesi/public/uploads/categories/';
                error_log("Upload directory: " . $uploadDir);
                
                // Dizin yoksa oluştur
                if (!file_exists($uploadDir)) {
                    error_log("Creating upload directory");
                    mkdir($uploadDir, 0777, true);
                }

                $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $fileName = uniqid() . '.' . $fileExtension;
                $uploadPath = $uploadDir . $fileName;
                
                error_log("Attempting to upload file: " . $uploadPath);
                error_log("File details: " . print_r($_FILES['image'], true));

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $data['image'] = $fileName;
                    error_log("File uploaded successfully: " . $fileName);
                } else {
                    error_log("Failed to move uploaded file. Upload error code: " . $_FILES['image']['error']);
                    error_log("PHP upload errors: " . print_r(error_get_last(), true));
                }
            }

            $sql = "INSERT INTO {$this->table} (name, description, image, status) VALUES (?, ?, ?, ?)";
            error_log("Executing SQL: " . $sql);
            error_log("With parameters: " . print_r([
                $data['name'],
                $data['description'] ?? '',
                $data['image'] ?? null,
                $data['status'] ?? 'active'
            ], true));

            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['name'],
                $data['description'] ?? '',
                $data['image'] ?? null,
                $data['status'] ?? 'active'
            ]);

            if (!$result) {
                error_log("Database error: " . print_r($stmt->errorInfo(), true));
            }
            
            return $result;
        } catch (\Exception $e) {
            error_log("Category create error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function update($data)
    {
        try {
            error_log("Starting category update with data: " . print_r($data, true));
            
            $id = $data['id'];
            $currentCategory = $this->find($id);
            error_log("Current category data: " . print_r($currentCategory, true));

            // Resim yükleme işlemi
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/gorev_sitesi/public/uploads/categories/';
                error_log("Upload directory: " . $uploadDir);
                
                // Eski resmi sil
                if ($currentCategory && $currentCategory->image) {
                    $oldImagePath = $_SERVER['DOCUMENT_ROOT'] . '/gorev_sitesi/public/uploads/categories/' . $currentCategory->image;
                    error_log("Attempting to delete old image: " . $oldImagePath);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                        error_log("Old image deleted successfully");
                    }
                }

                $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $fileName = uniqid() . '.' . $fileExtension;
                $uploadPath = $uploadDir . $fileName;
                
                error_log("Attempting to upload new file: " . $uploadPath);
                error_log("File details: " . print_r($_FILES['image'], true));

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $data['image'] = $fileName;
                    error_log("New file uploaded successfully: " . $fileName);
                } else {
                    error_log("Failed to move uploaded file. Upload error code: " . $_FILES['image']['error']);
                    error_log("PHP upload errors: " . print_r(error_get_last(), true));
                }
            } else {
                $data['image'] = $currentCategory->image;
                error_log("Keeping existing image: " . $data['image']);
            }

            $sql = "UPDATE {$this->table} SET name = ?, description = ?, image = ?, status = ? WHERE id = ?";
            error_log("Executing SQL: " . $sql);
            error_log("With parameters: " . print_r([
                $data['name'],
                $data['description'] ?? '',
                $data['image'],
                $data['status'] ?? 'active',
                $id
            ], true));

            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['name'],
                $data['description'] ?? '',
                $data['image'],
                $data['status'] ?? 'active',
                $id
            ]);

            if (!$result) {
                error_log("Database error: " . print_r($stmt->errorInfo(), true));
            }
            
            return $result;
        } catch (\Exception $e) {
            error_log("Category update error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getAll()
    {
        try {
            $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
            error_log("Executing getAll query: " . $sql);
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            error_log("Retrieved categories: " . print_r($results, true));
            return $results;
        } catch (\Exception $e) {
            error_log("Error in getAll: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return [];
        }
    }

    public function delete($id)
    {
        try {
            // Önce kategoriyi bul
            $category = $this->find($id);
            if ($category && $category->image) {
                // Resmi sil
                $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/gorev_sitesi/public/uploads/categories/' . $category->image;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            // Kategoriyi sil
            $sql = "DELETE FROM {$this->table} WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (\Exception $e) {
            error_log("Category delete error: " . $e->getMessage());
            return false;
        }
    }

    public function getActiveCategories()
    {
        try {
            $sql = "SELECT c.*, COUNT(t.id) as task_count 
                    FROM {$this->table} c
                    LEFT JOIN tasks t ON c.id = t.category_id AND t.status = 'active'
                    WHERE c.status = 'active'
                    GROUP BY c.id
                    HAVING task_count > 0
                    ORDER BY c.name ASC";
            
            error_log("Executing getActiveCategories query: " . $sql);
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            error_log("Retrieved active categories: " . print_r($results, true));
            return $results;
        } catch (\Exception $e) {
            error_log("Error in getActiveCategories: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return [];
        }
    }
} 