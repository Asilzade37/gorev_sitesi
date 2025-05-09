<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <!-- Son Eklenen Görevler Kartı -->
                <div class="card header-card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-clock-history"></i>
                            Son Eklenen Görevler
                        </h5>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-4">
                        <div class="row g-4 justify-content-start">
                            <?php foreach (array_slice($availableTasks, 0, 4) as $task): ?>
                            <div class="col-auto">
                                <a href="/gorev_sitesi/panel/task/<?= $task->id ?>" class="text-decoration-none">
                                    <div class="task-card">
                                        <div class="task-card-image">
                                            <?php if ($task->category_image): ?>
                                                <img src="/gorev_sitesi/public/uploads/categories/<?= $task->category_image ?>" 
                                                     alt="<?= htmlspecialchars($task->category) ?>">
                                            <?php else: ?>
                                                <div class="category-preview-placeholder">
                                                    <i class="bi bi-image"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="task-card-body">
                                            <h5 class="task-card-title"><?= htmlspecialchars($task->title) ?></h5>
                                            <div class="task-card-price"><?= number_format($task->reward, 2) ?> TL</div>
                                            
                                            <hr class="task-card-divider">
                                            
                                            <div class="task-progress-wrapper">
                                                <div class="task-progress-stats">
                                                    <?= $task->completed_count ?>/<?= $task->quota ?>
                                                </div>
                                                <div class="task-progress">
                                                    <div class="progress-bar" 
                                                         style="width: <?= ($task->completed_count / $task->quota) * 100 ?>%">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 