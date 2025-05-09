<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - Admin Panel' : 'Admin Panel' ?></title>
    
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
                <a href="/gorev_sitesi/adminpanel" class="brand">
                    <i class="bi bi-grid-fill"></i>
                    Admin Panel
                </a>
            </div>

            <nav class="nav-menu">
                <div class="nav-item">
                    <a href="/gorev_sitesi/adminpanel" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                        <i class="bi bi-speedometer2"></i>
                        Anasayfa
                    </a>
                </div>

                <div class="nav-item">
                    <a href="#" class="nav-link" data-bs-target="#gorevlerMenu">
                        <i class="bi bi-list-task"></i>
                        Görevler
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <div class="submenu" id="gorevlerMenu">
                        <a href="/gorev_sitesi/adminpanel/tasks" class="nav-link <?= $currentPage === 'tasks' ? 'active' : '' ?>">
                            <i class="bi bi-circle"></i>
                            Tüm Görevler
                        </a>
                        <a href="/gorev_sitesi/adminpanel/task/create" class="nav-link <?= $currentPage === 'task-create' ? 'active' : '' ?>">
                            <i class="bi bi-circle"></i>
                            Görev Ekle
                        </a>
                        <a href="/gorev_sitesi/adminpanel/tasks/pending" class="nav-link <?= $currentPage === 'pending-tasks' ? 'active' : '' ?>">
                            <i class="bi bi-circle"></i>
                            Bekleyen Görevler
                        </a>
                        <a href="/gorev_sitesi/adminpanel/tasks/completed" class="nav-link <?= $currentPage === 'completed-tasks' ? 'active' : '' ?>">
                            <i class="bi bi-circle"></i>
                            Tamamlanan Görevler
                        </a>
                    </div>
                </div>

                <div class="nav-item">
                    <a href="/gorev_sitesi/adminpanel/categories" class="nav-link <?= $currentPage === 'categories' ? 'active' : '' ?>">
                        <i class="bi bi-tags"></i>
                        Kategoriler
                    </a>
                </div>

                <div class="nav-item">
                    <a href="/gorev_sitesi/adminpanel/users" class="nav-link <?= $currentPage === 'users' ? 'active' : '' ?>">
                        <i class="bi bi-people"></i>
                        Kullanıcılar
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
                </div>

                <div class="navbar-end">
                    <div class="dropdown">
                        <button class="btn btn-link user-menu-toggle" data-bs-toggle="dropdown">
                            <div class="user-info">
                                <span class="user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                                <small class="user-role">Admin</small>
                            </div>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="/gorev_sitesi/adminpanel/profile">
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

        // Ana menü öğelerinin tıklama olaylarını yönet
        const menuItems = document.querySelectorAll('.nav-item > .nav-link');
        
        menuItems.forEach(item => {
            if (item.getAttribute('data-bs-target')) {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('data-bs-target');
                    const targetMenu = document.querySelector(targetId);
                    const arrow = this.querySelector('.bi-chevron-down');
                    
                    // Tıklanan menüyü aç/kapat
                    targetMenu.classList.toggle('show');
                    arrow.classList.toggle('rotate-180');
                });
            }
        });

        // Aktif menüyü işaretle ve parent menüyü aç
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
                
                // Parent submenu'yu aç
                const submenu = link.closest('.submenu');
                if (submenu) {
                    submenu.classList.add('show');
                    const parentLink = submenu.previousElementSibling;
                    const arrow = parentLink.querySelector('.bi-chevron-down');
                    if (arrow) {
                        arrow.classList.add('rotate-180');
                    }
                }
            }
        });

        // Sidebar toggle işlemleri
        function isDesktop() {
            return window.innerWidth >= 992;
        }

        function toggleSidebarHandler() {
            if (isDesktop()) {
                body.classList.toggle('sidebar-collapsed');
            } else {
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