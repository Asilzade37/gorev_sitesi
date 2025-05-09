<?php
$isEdit = $action === 'edit';
$formAction = $isEdit ? "/gorev_sitesi/adminpanel/category/edit/{$category->id}" : "/gorev_sitesi/adminpanel/category/create";
?>

<div class="section-header">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="title">
            <i class="bi bi-tag"></i>
            <?= $title ?>
        </h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= $formAction ?>" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Kategori Adı</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?= $isEdit ? htmlspecialchars($category->name) : '' ?>" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Açıklama</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= $isEdit ? htmlspecialchars($category->description) : '' ?></textarea>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Kategori Resmi</label>
                <?php if ($isEdit && isset($category->image) && $category->image): ?>
                    <div class="mb-2">
                        <img src="/gorev_sitesi/public/uploads/categories/<?= htmlspecialchars($category->image) ?>" 
                             alt="<?= htmlspecialchars($category->name) ?>" 
                             class="category-preview">
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                <?php if ($isEdit): ?>
                    <small class="text-muted">Yeni bir resim yüklemezseniz mevcut resim korunacaktır.</small>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Durum</label>
                <select class="form-select" id="status" name="status">
                    <option value="active" <?= (!$isEdit || ($isEdit && $category->status === 'active')) ? 'selected' : '' ?>>Aktif</option>
                    <option value="passive" <?= ($isEdit && $category->status === 'passive') ? 'selected' : '' ?>>Pasif</option>
                </select>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="/gorev_sitesi/adminpanel/categories" class="btn btn-secondary">İptal</a>
                <button type="submit" class="btn btn-primary">Kaydet</button>
            </div>
        </form>
    </div>
</div> 