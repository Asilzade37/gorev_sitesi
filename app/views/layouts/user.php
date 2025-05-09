<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - Görev Paneli' : 'Görev Paneli' ?></title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="/gorev_sitesi/public/css/pages/panel.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="/gorev_sitesi/panel" class="brand">
                    <i class="bi bi-grid-fill"></i>
                    Görev Paneli
                </a>
            </div>

            <nav class="nav-menu">
                <div class="nav-item">
                    <a href="/gorev_sitesi/panel" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                        <i class="bi bi-house"></i>
                        Anasayfa
                    </a>
                </div>

                <div class="nav-item">
                    <a href="/gorev_sitesi/panel/tasks" class="nav-link <?= $currentPage === 'tasks' ? 'active' : '' ?>">
                        <i class="bi bi-list-check"></i>
                        Görevler
                    </a>
                </div>

                <div class="nav-item">
                    <a href="/gorev_sitesi/panel/tasks/completed" class="nav-link <?= $currentPage === 'completed-tasks' ? 'active' : '' ?>">
                        <i class="bi bi-check2-circle"></i>
                        Tamamlanan Görevler
                    </a>
                </div>

                <div class="nav-item">
                    <a href="/gorev_sitesi/panel/tasks/pending" class="nav-link <?= $currentPage === 'pending-tasks' ? 'active' : '' ?>">
                        <i class="bi bi-hourglass-split"></i>
                        Bekleyen Görevler
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="main-wrapper">
            <!-- Navbar -->
            <nav class="navbar">
                <div class="navbar-start">
                    <button class="sidebar-toggle" id="toggleSidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="user-balance">
                        <i class="bi bi-wallet2"></i>
                        <span class="balance-amount"><?= number_format($userBalance ?? 0, 2) ?> TL</span>
                    </div>
                </div>

                <div class="navbar-end">
                    <div class="dropdown">
                        <button class="btn btn-link user-menu-toggle" data-bs-toggle="dropdown">
                            <div class="user-info">
                                <span class="user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                                <small class="user-role">Kullanıcı</small>
                            </div>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="/gorev_sitesi/panel/profile">
                                    <i class="bi bi-person"></i> Profil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="/gorev_sitesi/cikis">
                                    <i class="bi bi-box-arrow-right"></i> Çıkış
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="main-content">
                <?= $content ?>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const body = document.body;
        const sidebar = document.getElementById('sidebar');
        const toggleSidebar = document.getElementById('toggleSidebar');
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';

        // Check screen width
        function isDesktop() {
            return window.innerWidth >= 992;
        }

        function toggleSidebarHandler() {
            if (isDesktop()) {
                // Desktop behavior - toggle collapsed state
                body.classList.toggle('sidebar-collapsed');
            } else {
                // Mobile behavior - show/hide with overlay
                sidebar.classList.toggle('show');
                body.classList.toggle('sidebar-open');
                
                if (sidebar.classList.contains('show')) {
                    body.appendChild(overlay);
                } else {
                    if (document.querySelector('.sidebar-overlay')) {
                        document.querySelector('.sidebar-overlay').remove();
                    }
                }
            }
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            if (isDesktop()) {
                sidebar.classList.remove('show');
                body.classList.remove('sidebar-open');
                if (document.querySelector('.sidebar-overlay')) {
                    document.querySelector('.sidebar-overlay').remove();
                }
            } else {
                body.classList.remove('sidebar-collapsed');
            }
        });

        toggleSidebar?.addEventListener('click', toggleSidebarHandler);
        overlay.addEventListener('click', toggleSidebarHandler);
    });
    </script>
</body>
</html> 