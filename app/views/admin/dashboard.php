<div class="row">
    <!-- İstatistikler -->
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Toplam Görev</h5>
                <p class="card-text display-6"><?= count($tasks) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Toplam Kullanıcı</h5>
                <p class="card-text display-6"><?= count($users) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title">Aktif Görevler</h5>
                <p class="card-text display-6"><?= count(array_filter($tasks, fn($task) => $task->status === 'active')) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Son Görevler -->
<div class="row mt-4">
    <div class="col-12">
        <!-- Header Card -->
        <div class="card header-card">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-clock-history"></i>
                    Son Eklenen Görevler
                </h5>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Başlık</th>
                                <th>Ödül</th>
                                <th>Durum</th>
                                <th>Oluşturulma Tarihi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($tasks, 0, 5) as $task): ?>
                            <tr>
                                <td><?= $task->id ?></td>
                                <td><?= htmlspecialchars($task->title) ?></td>
                                <td><?= number_format($task->reward, 2) ?> TL</td>
                                <td>
                                    <span class="badge bg-<?= $task->status === 'active' ? 'success' : 'secondary' ?>">
                                        <?= $task->status === 'active' ? 'Aktif' : 'Pasif' ?>
                                    </span>
                                </td>
                                <td><?= $task->created_at ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

