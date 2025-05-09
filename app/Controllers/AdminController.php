<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Task;
use App\Models\Category;
use App\Models\User;

class AdminController extends Controller
{
    private $taskModel;
    private $categoryModel;
    private $userModel;

    public function __construct()
    {
        // Admin olmayan kullanıcıları engellemek için middleware
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            $this->redirect('/');
            exit;
        }

        $this->taskModel = new Task();
        $this->categoryModel = new Category();
        $this->userModel = new User();
    }

    public function index()
    {
        $tasks = $this->taskModel->getAll();
        $users = $this->userModel->getAll();

        $this->adminView('admin/dashboard', [
            'title' => 'Admin Panel',
            'tasks' => $tasks,
            'users' => $users
        ]);
    }

    public function tasks()
    {
        $tasks = $this->taskModel->getAll();
        $categories = $this->categoryModel->getAll();
        
        $this->adminView('admin/tasks', [
            'title' => 'Görev Yönetimi',
            'tasks' => $tasks,
            'categories' => $categories
        ]);
    }

    public function users()
    {
        $users = $this->userModel->getAll();
        
        $this->adminView('admin/users', [
            'title' => 'Kullanıcı Yönetimi',
            'users' => $users
        ]);
    }

    public function taskCreate()
    {
        $categories = $this->categoryModel->getAll();
        $this->adminView('admin/task_form', [
            'title' => 'Yeni Görev Ekle2',
            'categories' => $categories
        ]);
    }

    public function taskEdit($id)
    {
        $task = $this->taskModel->getById($id);
        if (!$task) {
            $this->setFlash('danger', 'Görev bulunamadı.');
            header('Location: /gorev_sitesi/adminpanel/tasks');
            exit;
        }

        $categories = $this->categoryModel->getAll();
        $this->adminView('admin/task_form', [
            'task' => $task,
            'categories' => $categories
        ]);
    }

    public function taskSave()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /gorev_sitesi/adminpanel/tasks');
            exit;
        }

        // Form verilerini al ve kaydet
        $data = [
            'title' => $_POST['title'] ?? '',
            'category_id' => $_POST['category_id'] ?? '',
            'description' => $_POST['description'] ?? '',
            'reward' => $_POST['reward'] ?? 0,
            'quota' => $_POST['quota'] ?? 1,
            'gender' => $_POST['gender'] ?? 'all',
            'cities' => $_POST['cities'] ?? [], // cities olarak alıyoruz
            'daily_limit' => $_POST['daily_limit'] ?? 1,
            'user_limit' => $_POST['user_limit'] ?? 1,
            'guide_level' => $_POST['guide_level'] ?? 1,
            'status' => $_POST['status'] ?? 'active'
        ];

        if (isset($_POST['id'])) {
            $data['id'] = $_POST['id'];
            $this->taskModel->update($data);
        } else {
            $this->taskModel->create($data);
        }

        header('Location: /gorev_sitesi/adminpanel/tasks');
        exit;
    }

    public function deleteTask($id)
    {
        if ($this->taskModel->delete($id)) {
            $this->setFlash('success', 'Görev başarıyla silindi.');
        } else {
            $this->setFlash('danger', 'Görev silinirken bir hata oluştu.');
        }

        header('Location: /gorev_sitesi/adminpanel/tasks');
        exit;
    }

    public function categories()
    {
        try {
            error_log("Fetching all categories");
            $categories = $this->categoryModel->getAll();
            error_log("Categories fetched: " . print_r($categories, true));
            
            $this->adminView('admin/categories', [
                'title' => 'Kategoriler',
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            error_log("Error in categories action: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Kategoriler yüklenirken bir hata oluştu.');
            $this->redirect('/adminpanel');
        }
    }

    public function categoryCreate()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                error_log("Processing category create POST request");
                error_log("POST data: " . print_r($_POST, true));
                error_log("FILES data: " . print_r($_FILES, true));

                $data = [
                    'name' => $_POST['name'] ?? '',
                    'description' => $_POST['description'] ?? '',
                    'status' => $_POST['status'] ?? 'active'
                ];

                // Resim yükleme kontrolü
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    error_log("Image upload detected");
                    if (!$this->validateImage($_FILES['image'])) {
                        error_log("Image validation failed");
                        $this->setFlash('error', 'Geçersiz resim formatı. Lütfen JPG, PNG veya GIF yükleyin.');
                        $this->redirect('/adminpanel/categories');
                        return;
                    }
                    error_log("Image validation passed");
                }

                if ($this->categoryModel->create($data)) {
                    error_log("Category created successfully");
                    $this->setFlash('success', 'Kategori başarıyla oluşturuldu.');
                    $this->redirect('/adminpanel/categories');
                } else {
                    error_log("Category creation failed");
                    $this->setFlash('error', 'Kategori oluşturulurken bir hata oluştu.');
                    $this->redirect('/adminpanel/categories');
                }
                return;
            }

            error_log("Displaying category create form");
            $this->adminView('admin/category_form', [
                'title' => 'Yeni Kategori',
                'action' => 'create'
            ]);
        } catch (\Exception $e) {
            error_log("Error in categoryCreate action: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Bir hata oluştu.');
            $this->redirect('/adminpanel/categories');
        }
    }

    public function categoryEdit($id)
    {
        try {
            error_log("Attempting to edit category with ID: " . $id);
            $category = $this->categoryModel->find($id);
            error_log("Found category: " . print_r($category, true));

            if (!$category) {
                error_log("Category not found");
                $this->setFlash('error', 'Kategori bulunamadı.');
                $this->redirect('/adminpanel/categories');
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                error_log("Processing category edit POST request");
                error_log("POST data: " . print_r($_POST, true));
                error_log("FILES data: " . print_r($_FILES, true));

                $data = [
                    'id' => $id,
                    'name' => $_POST['name'] ?? '',
                    'description' => $_POST['description'] ?? '',
                    'status' => $_POST['status'] ?? 'active'
                ];

                // Resim yükleme kontrolü
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    error_log("New image upload detected");
                    if (!$this->validateImage($_FILES['image'])) {
                        error_log("Image validation failed");
                        $this->setFlash('error', 'Geçersiz resim formatı. Lütfen JPG, PNG veya GIF yükleyin.');
                        $this->redirect('/adminpanel/categories');
                        return;
                    }
                    error_log("Image validation passed");
                }

                if ($this->categoryModel->update($data)) {
                    error_log("Category updated successfully");
                    $this->setFlash('success', 'Kategori başarıyla güncellendi.');
                    $this->redirect('/adminpanel/categories');
                } else {
                    error_log("Category update failed");
                    $this->setFlash('error', 'Kategori güncellenirken bir hata oluştu.');
                    $this->redirect('/adminpanel/categories');
                }
                return;
            }

            error_log("Displaying category edit form");
            $this->adminView('admin/category_form', [
                'title' => 'Kategori Düzenle',
                'action' => 'edit',
                'category' => $category
            ]);
        } catch (\Exception $e) {
            error_log("Error in categoryEdit action: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Bir hata oluştu.');
            $this->redirect('/adminpanel/categories');
        }
    }

    private function validateImage($file)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        error_log("Validating image: " . print_r($file, true));
        error_log("File type: " . $file['type']);
        error_log("File size: " . $file['size']);

        // Dosya türü kontrolü
        if (!in_array($file['type'], $allowedTypes)) {
            error_log("Invalid file type: " . $file['type']);
            return false;
        }

        // Dosya boyutu kontrolü
        if ($file['size'] > $maxSize) {
            error_log("File too large: " . $file['size']);
            return false;
        }

        error_log("Image validation successful");
        return true;
    }

    public function categoryDelete($id)
    {
        $this->categoryModel->delete($id);
        header('Location: /gorev_sitesi/adminpanel/categories');
        exit;
    }

    public function completedTasks()
    {
        $tasks = $this->taskModel->getTasksWithSubmissions('completed');
        
        // Debug için
        error_log("Completed tasks count: " . count($tasks));
        
        $this->adminView('admin/tasks_submissions', [
            'title' => 'Tamamlanan Görevler',
            'tasks' => $tasks,
            'type' => 'completed'
        ]);
    }

    public function pendingTasks()
    {
        $tasks = $this->taskModel->getTasksWithSubmissions('pending');
        
        // Debug için
        error_log("Pending tasks count: " . count($tasks));
        
        $this->adminView('admin/tasks_submissions', [
            'title' => 'Bekleyen Görevler',
            'tasks' => $tasks,
            'type' => 'pending'
        ]);
    }

    public function reviewSubmission($submissionId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/adminpanel/tasks/pending');
            return;
        }

        $status = $_POST['status'] ?? '';
        $rejectionReason = $_POST['rejection_reason'] ?? '';

        if ($this->taskModel->updateSubmission($submissionId, [
            'status' => $status,
            'rejection_reason' => $rejectionReason
        ])) {
            $this->setFlash('success', 'Görev durumu güncellendi');
        } else {
            $this->setFlash('error', 'Görev durumu güncellenirken bir hata oluştu');
        }

        $this->redirect('/adminpanel/tasks/pending');
    }
} 