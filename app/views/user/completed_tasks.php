<div class="content">
    <div class="container-fluid">
        <!-- Header Card -->
        <div class="card header-card">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-check2-circle"></i>
                    Tamamlanan Görevler
                </h5>
            </div>
        </div>

        <!-- Tasks Grid -->
        <div class="card">
            <div class="card-body p-4">
                <div class="row g-4 justify-content-start">
                    <?php if (empty($tasks)): ?>
                        <div class="col-12 text-center text-muted">
                            <i class="bi bi-clipboard-check fs-1"></i>
                            <p class="mt-2">Henüz tamamlanmış görev bulunmuyor</p>
                            <p class="text-muted mb-0">Görevleri tamamladıkça burada listelenecektir.</p>
                            <a href="/gorev_sitesi/panel/tasks" class="btn btn-primary mt-3">
                                Görevleri Görüntüle
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($tasks as $task): ?>
                            <div class="col-auto">
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
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-success">Tamamlandı</span>
                                            <small class="text-muted">
                                                <?= date('d.m.Y H:i', strtotime($task->completed_at)) ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div> 