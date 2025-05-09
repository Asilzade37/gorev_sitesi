<div class="container py-4">
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?= $_SESSION['flash_type'] ?> alert-dismissible fade show">
            <?= $_SESSION['flash_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title h3 mb-4"><?= htmlspecialchars($task->title) ?></h1>
                    
                    <div class="task-info mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Kategori:</strong> <?= htmlspecialchars($task->category) ?></p>
                                <p><strong>Ödül:</strong> <?= number_format($task->reward, 2) ?> TL</p>
                                <p><strong>Kota:</strong> <?= $task->quota ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Günlük Limit:</strong> <?= $task->daily_limit ?></p>
                                <p><strong>Kullanıcı Limiti:</strong> <?= $task->user_limit ?></p>
                                <p><strong>Cinsiyet:</strong> <?= $task->gender === 'all' ? 'Hepsi' : ($task->gender === 'male' ? 'Erkek' : 'Kadın') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="task-description mb-4">
                        <h5>Görev Açıklaması</h5>
                        <p><?= nl2br(htmlspecialchars($task->description)) ?></p>
                    </div>

                    <?php if ($task->city): ?>
                    <div class="task-cities mb-4">
                        <h5>Geçerli Şehirler</h5>
                        <p><?= htmlspecialchars(str_replace(',', ', ', $task->city)) ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if ($submission): ?>
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Görev Durumu</h5>
                                <?php
                                $statusBadge = [
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger'
                                ];
                                $statusText = [
                                    'pending' => 'İnceleniyor',
                                    'approved' => 'Onaylandı',
                                    'rejected' => 'Reddedildi'
                                ];
                                ?>
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge bg-<?= $statusBadge[$submission->status] ?> me-2">
                                        <?= $statusText[$submission->status] ?>
                                    </span>
                                    <small class="text-muted">
                                        Gönderim: <?= date('d.m.Y H:i', strtotime($submission->created_at)) ?>
                                    </small>
                                </div>

                                <?php if ($submission->status === 'rejected' && $submission->rejection_reason): ?>
                                    <div class="alert alert-danger">
                                        <strong>Red Nedeni:</strong> <?= htmlspecialchars($submission->rejection_reason) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="task-proof mb-3">
                                    <strong>Gönderilen Kanıt:</strong>
                                    <p class="mt-2"><?= nl2br(htmlspecialchars($submission->proof)) ?></p>
                                </div>

                                <?php if ($submission->image_path): ?>
                                    <div class="task-image">
                                        <img src="/gorev_sitesi/<?= $submission->image_path ?>" class="img-fluid rounded" alt="Kanıt Görseli">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!$submission || $submission->status === 'rejected'): ?>
                        <form action="/gorev_sitesi/panel/task/submit/<?= $task->id ?>" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="proof" class="form-label">Görev Kanıtı</label>
                                <textarea class="form-control" id="proof" name="proof" rows="4" required></textarea>
                                <div class="form-text">Görevi nasıl tamamladığınızı detaylı bir şekilde açıklayın.</div>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Kanıt Görseli</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <div class="form-text">Varsa, görevi tamamladığınıza dair ekran görüntüsü ekleyin.</div>
                            </div>

                            <button type="submit" class="btn btn-primary">Görevi Tamamladım</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Görev Kuralları</h5>
                    <ul class="list-unstyled mb-0">
                        <li>✓ Görevi dikkatle okuyun</li>
                        <li>✓ Tüm adımları eksiksiz tamamlayın</li>
                        <li>✓ Kanıtlarınızı doğru şekilde yükleyin</li>
                        <li>✓ Dürüst ve açık olun</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div> 