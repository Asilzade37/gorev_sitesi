<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Task;

class HomeController extends Controller
{
    private $taskModel;

    public function __construct()
    {
        $this->taskModel = new Task();
    }

    public function index()
    {
        try {
            $latestTasks = $this->taskModel->getAll();
            
            $this->view('home/index', [
                'title' => 'Ana Sayfa',
                'tasks' => $latestTasks
            ]);
        } catch (\Exception $e) {
            die("Hata: " . $e->getMessage());
        }
    }
}