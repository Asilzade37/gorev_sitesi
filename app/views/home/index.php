<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Görevleri Tamamla, Kazanmaya Başla</h1>
            <p class="hero-description">
                Basit görevleri tamamlayarak para kazanabileceğiniz platformumuza hoş geldiniz.
                Hemen üye olun ve kazanmaya başlayın!
            </p>
            <div class="hero-buttons">
                <a href="/gorev_sitesi/kayit" class="btn btn-light btn-lg me-3">Hemen Başla</a>
                <a href="/gorev_sitesi/nasil-calisir" class="btn btn-outline-light btn-lg">Nasıl Çalışır?</a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <h2 class="section-title">Neden Bizi Seçmelisiniz?</h2>
        
        <div class="features-grid">
            <!-- Kolay Kullanım -->
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="bi bi-mouse"></i>
                </div>
                <h3 class="feature-title">Kolay Kullanım</h3>
                <p class="feature-description">
                    Basit ve kullanıcı dostu arayüzümüz ile görevleri kolayca tamamlayın.
                </p>
            </div>

            <!-- Hızlı Ödeme -->
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <h3 class="feature-title">Hızlı Ödeme</h3>
                <p class="feature-description">
                    Kazandığınız parayı hızlı ve güvenli bir şekilde çekin.
                </p>
            </div>

            <!-- 7/24 Destek -->
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="bi bi-headset"></i>
                </div>
                <h3 class="feature-title">7/24 Destek</h3>
                <p class="feature-description">
                    Sorularınız için destek ekibimiz her zaman yanınızda.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2 class="cta-title">Hemen Üye Olun, Kazanmaya Başlayın!</h2>
        <p class="cta-description">
            Binlerce kullanıcımız gibi siz de görevleri tamamlayarak para kazanmaya başlayın.
            Üyelik tamamen ücretsiz!
        </p>
        <div class="cta-buttons">
            <a href="/gorev_sitesi/kayit" class="btn btn-primary btn-lg me-3">Üye Ol</a>
            <a href="/gorev_sitesi/giris" class="btn btn-outline-primary btn-lg">Giriş Yap</a>
        </div>
    </div>
</section>

<div class="container mt-4">
    <h1>Hoş Geldiniz</h1>
    
    <div class="row mt-4">
        <div class="col-md-8">
            <h2>Aktif Görevler</h2>
            
            <?php if (!empty($tasks)): ?>
                <div class="row">
                    <?php foreach ($tasks as $task): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($task->title) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($task->description) ?></p>
                                    <p class="text-muted">Ödül: <?= number_format($task->reward, 2) ?> TL</p>
                                    <a href="/gorev_sitesi/gorev/<?= $task->id ?>" class="btn btn-primary">Detaylar</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Henüz aktif görev bulunmuyor.</p>
            <?php endif; ?>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Hızlı Bilgi</h5>
                    <p class="card-text">Görevleri tamamlayarak para kazanmaya hemen başlayabilirsiniz!</p>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="/gorev_sitesi/kayit" class="btn btn-success">Hemen Üye Ol</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div> 