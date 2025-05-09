<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Task;
use App\Models\User;

class UserController extends Controller
{
    private $taskModel;
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        
        // Giriş yapmamış kullanıcıları engelle
        if (!$this->isLoggedIn()) {
            $this->redirect('/giris');
            exit;
        }

        // Admin kullanıcıları engelle
        if ($this->isAdmin()) {
            $this->redirect('/adminpanel');
            exit;
        }

        $this->taskModel = new Task();
        $this->userModel = new User();
    }

    public function index()
    {
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->find($userId);
        
        // İstatistikleri al
        $completedTasks = $this->taskModel->getCompletedTasksCount($userId);
        $pendingTasks = $this->taskModel->getPendingTasksCount($userId);
        $totalEarnings = $this->taskModel->getTotalEarnings($userId);
        $userLevel = $this->userModel->getUserLevel($userId);
        
        // Mevcut görevleri al
        $availableTasks = $this->taskModel->getAvailableTasks();
        
        // Son görevleri al
        $recentTasks = $this->taskModel->getRecentTasks($userId, 5);
        
        // Son aktiviteleri al
        $activities = $this->taskModel->getRecentActivities($userId, 5);

        $this->view('user/dashboard', [
            'title' => 'Dashboard',
            'user' => $user,
            'availableTasks' => $availableTasks,
            'completedTasks' => $completedTasks,
            'pendingTasks' => $pendingTasks,
            'totalEarnings' => $totalEarnings,
            'userLevel' => $userLevel,
            'recentTasks' => $recentTasks,
            'activities' => $activities
        ]);
    }

    public function tasks()
    {
        $tasks = $this->taskModel->getAll();
        
        $this->view('user/tasks', [
            'title' => 'Görevler',
            'tasks' => $tasks
        ]);
    }

    public function taskDetail($id)
    {
        $task = $this->taskModel->getById($id);
        if (!$task) {
            $this->setFlash('error', 'Görev bulunamadı');
            $this->redirect('/panel/tasks');
            return;
        }

        // Kullanıcının bu göreve ait gönderisi var mı kontrol et
        $submission = $this->taskModel->getSubmission($id, $_SESSION['user_id']);

        $this->view('user/task_detail', [
            'title' => $task->title,
            'task' => $task,
            'submission' => $submission
        ]);
    }

    public function submitTask($taskId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/panel/task/' . $taskId);
            return;
        }

        // Görevin var olduğunu ve aktif olduğunu kontrol et
        $task = $this->taskModel->getById($taskId);
        if (!$task || $task->status !== 'active') {
            $this->setFlash('error', 'Görev bulunamadı veya aktif değil');
            $this->redirect('/panel/tasks');
            return;
        }

        // Daha önce gönderilmiş mi kontrol et
        $existingSubmission = $this->taskModel->getSubmission($taskId, $_SESSION['user_id']);
        if ($existingSubmission && $existingSubmission->status === 'pending') {
            $this->setFlash('error', 'Bu görev için zaten bekleyen bir gönderiniz var');
            $this->redirect('/panel/task/' . $taskId);
            return;
        }

        $data = [
            'task_id' => $taskId,
            'user_id' => $_SESSION['user_id'],
            'proof' => $_POST['proof'],
            'status' => 'pending'
        ];

        // Görsel yükleme işlemi
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $target_dir = "uploads/task_proofs/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $file_name = uniqid() . '.' . $file_extension;
            $target_path = $target_dir . $file_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $data['image_path'] = $target_path;
            } else {
                $this->setFlash('error', 'Görsel yüklenirken bir hata oluştu');
                $this->redirect('/panel/task/' . $taskId);
                return;
            }
        }

        if ($this->taskModel->submitTask($data)) {
            $this->setFlash('success', 'Görev başarıyla gönderildi. İncelendikten sonra onaylanacaktır.');
        } else {
            // Hata loglarını kontrol et
            $errorMessage = error_get_last();
            $errorDetails = $errorMessage ? ': ' . $errorMessage['message'] : '';
            
            $this->setFlash('error', 'Görev gönderilirken bir hata oluştu' . $errorDetails);
            error_log("Task submission failed for task_id: {$taskId}, user_id: {$_SESSION['user_id']}");
        }

        $this->redirect('/panel/task/' . $taskId);
    }

    public function completedTasks()
    {
        $userId = $_SESSION['user_id'];
        $tasks = $this->taskModel->getUserCompletedTasks($userId);
        
        $this->view('user/completed_tasks', [
            'title' => 'Tamamlanan Görevler',
            'tasks' => $tasks
        ]);
    }

    public function pendingTasks()
    {
        $userId = $_SESSION['user_id'];
        $tasks = $this->taskModel->getUserPendingTasks($userId);
        
        $this->view('user/pending_tasks', [
            'title' => 'Bekleyen Görevler',
            'tasks' => $tasks
        ]);
    }

    public function profile()
    {
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->find($userId);
        
        $this->view('user/profile', [
            'title' => 'Profilim',
            'user' => $user
        ]);
    }

    protected function view($view, $data = [])
    {
        // Kullanıcı bilgilerini al
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->find($userId);
        
        // Aktif sayfayı belirle
        $currentPage = '';
        $uri = $_SERVER['REQUEST_URI'];
        
        if (strpos($uri, '/panel/tasks/completed') !== false) {
            $currentPage = 'completed-tasks';
        } elseif (strpos($uri, '/panel/tasks/pending') !== false) {
            $currentPage = 'pending-tasks';
        } elseif (strpos($uri, '/panel/tasks') !== false) {
            $currentPage = 'tasks';
        } elseif (strpos($uri, '/panel/profile') !== false) {
            $currentPage = 'profile';
        } elseif (strpos($uri, '/panel/earnings') !== false) {
            $currentPage = 'earnings';
        } elseif (strpos($uri, '/panel/settings') !== false) {
            $currentPage = 'settings';
        } elseif ($uri === '/gorev_sitesi/panel' || $uri === '/gorev_sitesi/panel/') {
            $currentPage = 'dashboard';
        }

        // View data'ya kullanıcı bilgilerini ve aktif sayfayı ekle
        $data['user'] = $user;
        $data['currentPage'] = $currentPage;

        // Layout'u kullanıcı layout'u olarak ayarla
        $this->layout = 'user';
        
        parent::view($view, $data);
    }
} 