<!-- Header Card -->
<div class="card header-card">
    <div class="card-body">
        <h5 class="card-title">
            <i class="bi bi-plus-circle"></i>
            <?= isset($task) ? 'Görevi Düzenle' : 'Yeni Görev Ekle' ?>
        </h5>
        <div class="header-actions">
            <a href="/gorev_sitesi/adminpanel/tasks" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Geri Dön
            </a>
        </div>
    </div>
</div>

<!-- Form Card -->
<div class="card">
    <div class="card-body">
        <form id="taskForm" action="/gorev_sitesi/adminpanel/task/save" method="POST" enctype="multipart/form-data">
            <?php if(isset($task)): ?>
                <input type="hidden" name="id" value="<?= $task->id ?>">
            <?php endif; ?>
            
            <!-- Form Grupları -->
            <div class="row g-3">
                <!-- Temel Bilgiler -->
                <div class="col-md-4">
                    <label for="title" class="form-label small">Görev Başlığı *</label>
                    <input type="text" class="form-control form-control-sm" id="title" name="title" required 
                           value="<?= isset($task) ? htmlspecialchars($task->title) : '' ?>">
                </div>
                
                <div class="col-md-4">
                    <label for="category_id" class="form-label small">Kategorisi *</label>
                    <select class="form-select form-select-sm" id="category_id" name="category_id" required>
                        <option value="">Kategori Seçin</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category->id ?>" <?= (isset($task) && $task->category_id == $category->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="reward" class="form-label small">Ücret *</label>
                    <input type="number" class="form-control form-control-sm" id="reward" name="reward" step="0.01" required
                           value="<?= isset($task) ? $task->reward : '' ?>">
                </div>

                <!-- Görev Limitleri -->
                <div class="col-md-4">
                    <label for="quota" class="form-label small" title="Kaç kişi bu görevi alabilir">Görev Kontenjanı</label>
                    <input type="number" class="form-control form-control-sm" id="quota" name="quota" min="1" 
                           value="<?= isset($task) ? $task->quota : '1' ?>">
                </div>

                <div class="col-md-4">
                    <label for="gender" class="form-label small">Cinsiyet</label>
                    <select class="form-select form-select-sm" id="gender" name="gender">
                        <option value="all">Tümü</option>
                        <option value="male">Erkek</option>
                        <option value="female">Kadın</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="city" class="form-label small">Görev Şehirleri</label>
                    <select class="form-select form-select-sm" id="city" name="cities[]" multiple>
                        <?php
                        $cities = isset($task) ? explode(',', $task->city) : [];
                        $turkishCities = [
                            "Adana", "Adıyaman", "Afyonkarahisar", "Ağrı", "Amasya", "Ankara", "Antalya", "Artvin", 
                            "Aydın", "Balıkesir", "Bilecik", "Bingöl", "Bitlis", "Bolu", "Burdur", "Bursa", "Çanakkale", 
                            "Çankırı", "Çorum", "Denizli", "Diyarbakır", "Edirne", "Elazığ", "Erzincan", "Erzurum", 
                            "Eskişehir", "Gaziantep", "Giresun", "Gümüşhane", "Hakkari", "Hatay", "Isparta", "Mersin", 
                            "İstanbul", "İzmir", "Kars", "Kastamonu", "Kayseri", "Kırklareli", "Kırşehir", "Kocaeli", 
                            "Konya", "Kütahya", "Malatya", "Manisa", "Kahramanmaraş", "Mardin", "Muğla", "Muş", "Nevşehir", 
                            "Niğde", "Ordu", "Rize", "Sakarya", "Samsun", "Siirt", "Sinop", "Sivas", "Tekirdağ", "Tokat", 
                            "Trabzon", "Tunceli", "Şanlıurfa", "Uşak", "Van", "Yozgat", "Zonguldak", "Aksaray", "Bayburt", 
                            "Karaman", "Kırıkkale", "Batman", "Şırnak", "Bartın", "Ardahan", "Iğdır", "Yalova", "Karabük", 
                            "Kilis", "Osmaniye", "Düzce"
                        ];
                        foreach($turkishCities as $city): ?>
                            <option value="<?= $city ?>" <?= in_array($city, $cities ?? []) ? 'selected' : '' ?>>
                                <?= $city ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Diğer Limitler -->
                <div class="col-md-4">
                    <label for="daily_limit" class="form-label small">Günlük Görev Sınırı</label>
                    <input type="number" class="form-control form-control-sm" id="daily_limit" name="daily_limit" min="1" value="1">
                </div>

                <div class="col-md-4">
                    <label for="user_limit" class="form-label small">Kullanıcı Sınırı</label>
                    <input type="number" class="form-control form-control-sm" id="user_limit" name="user_limit" min="1" value="1">
                </div>

                <div class="col-md-4">
                    <label for="guide_level" class="form-label small">Yerel Rehber Seviyesi</label>
                    <select class="form-select form-select-sm" id="guide_level" name="guide_level">
                        <?php for($i = 1; $i <= 9; $i++): ?>
                            <option value="<?= $i ?>"><?= $i == 9 ? 'Sadece 9+ Seviye' : "Seviye {$i} ve üstü" ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Yorumlar -->
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label small mb-0">Görev Yorumları</label>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="addComment">
                            <i class="bi bi-plus-lg"></i> Yorum Ekle
                        </button>
                    </div>
                    <div id="commentsContainer">
                        <!-- Yorumlar buraya eklenecek -->
                    </div>
                </div>

                <!-- Açıklama -->
                <div class="col-12">
                    <label for="description" class="form-label small">Görev Açıklaması *</label>
                    <textarea class="form-control form-control-sm" id="description" name="description" rows="4" required><?= isset($task) ? htmlspecialchars($task->description) : '' ?></textarea>
                </div>

                <!-- Durum -->
                <div class="col-md-4">
                    <label for="status" class="form-label small">Durum</label>
                    <select class="form-select form-select-sm" id="status" name="status">
                        <option value="active">Aktif</option>
                        <option value="inactive">Pasif</option>
                    </select>
                </div>
            </div>

            <!-- Form Butonları -->
            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Kaydet
                </button>
                <a href="/gorev_sitesi/adminpanel/tasks" class="btn btn-light ms-2">İptal</a>
            </div>
        </form>
    </div>
</div>

<!-- Yorum Ekleme için JavaScript -->
<script>
document.getElementById('addComment').addEventListener('click', function() {
    const container = document.getElementById('commentsContainer');
    const commentCount = container.children.length;
    
    const commentDiv = document.createElement('div');
    commentDiv.className = 'comment-item border rounded p-2 mb-2';
    commentDiv.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <small class="text-muted">Yorum #${commentCount + 1}</small>
            <button type="button" class="btn btn-danger btn-sm remove-comment">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="row g-2">
            <div class="col-md-8">
                <textarea class="form-control form-control-sm" name="comments[${commentCount}][text]" rows="2" placeholder="Yorum metni..."></textarea>
            </div>
            <div class="col-md-4">
                <input type="file" class="form-control form-control-sm" name="comments[${commentCount}][image]" accept="image/*">
            </div>
        </div>
    `;
    
    container.appendChild(commentDiv);
    
    commentDiv.querySelector('.remove-comment').addEventListener('click', function() {
        commentDiv.remove();
    });
});
</script> 