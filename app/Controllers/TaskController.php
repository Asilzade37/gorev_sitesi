<?php

class TaskController {
    public function tasks() {
        $taskModel = new Task();
        $tasks = $taskModel->getAllTasks();

        $this->view('user/tasks', [
            'tasks' => $tasks
        ]);
    }
} 