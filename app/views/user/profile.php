<div class="content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0">Profilim</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="<?= $user->avatar ?? '/gorev_sitesi/assets/img/default-avatar.png' ?>" 
                             alt="<?= htmlspecialchars($user->name) ?>" 
                             class="rounded-circle mb-3"
                             width="128"
                             height="128">
                        <h5 class="card-title"><?= htmlspecialchars($user->name) ?></h5>
                        <p class="text-muted mb-1"><?= htmlspecialchars($user->email) ?></p>
                        <p class="text-muted mb-3"><?= htmlspecialchars($user->phone) ?></p>
                        
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            Profili Düzenle
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Profil Bilgileri</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">Ad Soyad</label>
                            <p class="mb-0"><?= htmlspecialchars($user->name) ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">E-posta</label>
                            <p class="mb-0"><?= htmlspecialchars($user->email) ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Telefon</label>
                            <p class="mb-0"><?= htmlspecialchars($user->phone) ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Üyelik Tarihi</label>
                            <p class="mb-0"><?= date('d.m.Y', strtotime($user->created_at)) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Profil Düzenleme Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/gorev_sitesi/panel/profile/update" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Profili Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ad Soyad</label>
                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($user->name) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Telefon</label>
                        <input type="tel" class="form-control" name="phone" value="<?= htmlspecialchars($user->phone) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Profil Fotoğrafı</label>
                        <input type="file" class="form-control" name="avatar" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div> 