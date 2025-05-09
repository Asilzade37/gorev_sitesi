<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - Görev Sitesi' : 'Görev Sitesi' ?></title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="/gorev_sitesi/public/css/main.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom fixed-top">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="/gorev_sitesi">
                <img src="/gorev_sitesi/assets/img/logo.png" alt="Logo" height="40">
            </a>
            
            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navbar Content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Left Menu -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/gorev_sitesi">Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gorev_sitesi/nasil-calisir">Nasıl Çalışır?</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gorev_sitesi/blog">Blog</a>
                    </li>
                </ul>
                
                <!-- Right Menu -->
                <div class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['is_admin']): ?>
                            <a href="/gorev_sitesi/adminpanel" class="btn btn-primary me-2">Admin Panel</a>
                        <?php else: ?>
                            <a href="/gorev_sitesi/panel" class="btn btn-primary me-2">Panel</a>
                        <?php endif; ?>
                        <a href="/gorev_sitesi/cikis" class="btn btn-outline-danger">Çıkış</a>
                    <?php else: ?>
                        <a href="/gorev_sitesi/giris" class="btn btn-outline-primary me-2">Giriş Yap</a>
                        <a href="/gorev_sitesi/kayit" class="btn btn-primary">Kayıt Ol</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="container mt-4">
                <div class="alert alert-<?= $_SESSION['flash_type'] ?> alert-dismissible fade show">
                    <?= $_SESSION['flash_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
            </div>
        <?php endif; ?>

        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="footer bg-white border-top">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center py-4">
                <div class="copyright">
                    &copy; <?= date('Y') ?> Görev Sitesi. Tüm hakları saklıdır.
                </div>
                <div class="social-links">
                    <a href="#" class="text-muted me-3"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-muted me-3"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="text-muted me-3"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-muted"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 