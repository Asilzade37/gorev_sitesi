<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Kayıt Ol' ?> - Görev Sitesi</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/gorev_sitesi/public/css/main.css" rel="stylesheet">
</head>
<body>
    <div class="auth-layout">
        <!-- Auth Content -->
        <div class="auth-content">
            <div class="auth-card">
                <div class="card">
                    <div class="card-body">
                        <h1 class="auth-title">Kayıt Ol</h1>
                        
                        <?php if (isset($_SESSION['flash_message'])): ?>
                            <div class="alert alert-<?= $_SESSION['flash_type'] ?> alert-dismissible fade show">
                                <?= $_SESSION['flash_message'] ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
                        <?php endif; ?>

                        <form action="/gorev_sitesi/kayit/register" method="POST" class="auth-form">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Ad Soyad</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Ad ve soyadınız" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="email" class="form-label">E-posta Adresi</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="E-posta adresiniz" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="phone" class="form-label">Telefon Numarası</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="5XX XXX XX XX" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="password" class="form-label">Şifre</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Şifreniz" required>
                            </div>

                            <div class="form-group mb-4">
                                <label for="password_confirm" class="form-label">Şifre Tekrar</label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Şifrenizi tekrar girin" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Kayıt Ol</button>
                        </form>

                        <div class="auth-links">
                            <a href="/gorev_sitesi/giris">Zaten hesabınız var mı? Giriş yapın</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 