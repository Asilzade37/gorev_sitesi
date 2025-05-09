<div class="section-header">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="title">
            <i class="bi bi-tags"></i>
            Kategoriler
        </h2>
        <div>
            <a href="/gorev_sitesi/adminpanel/category/create" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Yeni Kategori
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
                        <th width="80">Resim</th>
                        <th>Kategori Adı</th>
                        <th>Açıklama</th>
                        <th width="120">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                    <tr>
                        <td>
                            <?php if (!empty($category->image)): ?>
                                <img src="/gorev_sitesi/public/uploads/categories/<?= htmlspecialchars($category->image) ?>" 
                                     alt="<?= htmlspecialchars($category->name) ?>" 
                                     class="category-preview">
                            <?php else: ?>
                                <div class="category-preview-placeholder">
                                    <i class="bi bi-image"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($category->name) ?></td>
                        <td><?= htmlspecialchars($category->description) ?></td>
                        <td>
                            <a href="/gorev_sitesi/adminpanel/category/edit/<?= $category->id ?>" 
                               class="btn btn-primary btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button class="btn btn-danger btn-sm" 
                                    onclick="deleteCategory(<?= $category->id ?>)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function deleteCategory(id) {
    if (confirm('Bu kategoriyi silmek istediğinize emin misiniz?')) {
        window.location.href = '/gorev_sitesi/adminpanel/category/delete/' + id;
    }
}
</script> 