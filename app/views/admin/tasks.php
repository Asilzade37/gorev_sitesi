<!-- Header Card -->
<div class="card header-card">
    <div class="card-body">
        <h5 class="card-title">
            <i class="bi bi-list-task"></i>
            Görev Listesi
        </h5>
        <div class="header-actions">
            <a href="/gorev_sitesi/adminpanel/task/create" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Yeni Görev
            </a>
        </div>
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
                        <th>Kategori</th>
                        <th>Ödül</th>
                        <th>Durum</th>
                        <th>Oluşturulma Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?= $task->id ?></td>
                        <td><?= htmlspecialchars($task->title) ?></td>
                        <td><?= htmlspecialchars($task->category) ?></td>
                        <td><?= number_format($task->reward, 2) ?> TL</td>
                        <td>
                            <span class="badge <?= $task->status === 'active' ? 'badge-success' : 'badge-secondary' ?>">
                                <?= $task->status === 'active' ? 'Aktif' : 'Pasif' ?>
                            </span>
                        </td>
                        <td><?= $task->created_at ?></td>
                        <td>
                            <div class="btn-group">
                                <a href="/gorev_sitesi/adminpanel/task/edit/<?= $task->id ?>" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm delete-task" data-id="<?= $task->id ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Silme Onay Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Görevi Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Bu görevi silmek istediğinizden emin misiniz?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <a href="#" class="btn btn-danger" id="confirmDelete">Sil</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Silme modalı için
    const deleteModal = document.getElementById('deleteModal');
    const confirmDelete = document.getElementById('confirmDelete');
    
    // Silme butonlarına tıklandığında
    document.querySelectorAll('.delete-task').forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.getAttribute('data-id');
            const deleteUrl = `/gorev_sitesi/adminpanel/task/delete/${taskId}`;
            confirmDelete.href = deleteUrl;
            
            const modal = new bootstrap.Modal(deleteModal);
            modal.show();
        });
    });
});
</script> 