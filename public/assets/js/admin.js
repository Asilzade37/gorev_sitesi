$(document).ready(function() {
    // Theme Management
    const themeManager = {
        init() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const savedColor = localStorage.getItem('theme-color') || 'blue';
            
            document.documentElement.setAttribute('data-theme', savedTheme);
            this.applyTheme(savedTheme);
            this.applyColor(savedColor);
            this.bindEvents();
        },

        applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            $('#themeToggle i').toggleClass('bi-sun-fill bi-moon-fill', theme === 'dark');
        },

        applyColor(color) {
            document.documentElement.style.setProperty('--theme-color', `var(--theme-${color})`);
        },

        bindEvents() {
            $('#themeToggle').on('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                this.applyTheme(newTheme);
                localStorage.setItem('theme', newTheme);
            });

            $('[data-theme-color]').on('click', (e) => {
                const color = $(e.currentTarget).data('theme-color');
                this.applyColor(color);
                localStorage.setItem('theme-color', color);
            });
        }
    };

    // Sidebar Management
    const sidebarManager = {
        init() {
            this.loadSavedState();
            this.bindEvents();
        },

        loadSavedState() {
            const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            if (isCollapsed) {
                $('.sidebar').addClass('collapsed');
                $('#sidebarToggle i').toggleClass('bi-chevron-left bi-chevron-right');
            }
        },

        bindEvents() {
            $('#sidebarToggle').on('click', () => {
                $('.sidebar').toggleClass('collapsed');
                $('#sidebarToggle i').toggleClass('bi-chevron-left bi-chevron-right');
                localStorage.setItem('sidebar-collapsed', $('.sidebar').hasClass('collapsed'));
            });

            $('#mobileSidebarToggle').on('click', () => {
                $('.sidebar').toggleClass('mobile-open');
            });

            $('.has-submenu > .nav-link').on('click', function(e) {
                e.preventDefault();
                $(this).parent().toggleClass('open');
                $(this).find('.submenu-arrow').toggleClass('bi-chevron-down bi-chevron-up');
            });
        }
    };

    // Toast Notifications
    window.showToast = function(message, type = 'success') {
        const toast = `
            <div class="toast" role="alert">
                <div class="toast-header">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} text-${type} me-2"></i>
                    <strong class="me-auto">Bildirim</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">${message}</div>
            </div>
        `;
        
        $('.toast-container').append(toast);
        const toastElement = new bootstrap.Toast($('.toast').last(), {
            animation: true,
            autohide: true,
            delay: 3000
        });
        toastElement.show();

        // Remove toast after it's hidden
        $('.toast').last().on('hidden.bs.toast', function() {
            $(this).remove();
        });
    };

    // Ajax Form Handler
    $(document).on('submit', 'form[data-ajax="true"]', function(e) {
        e.preventDefault();
        const form = $(this);
        
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    if (response.redirect) {
                        setTimeout(() => window.location.href = response.redirect, 1000);
                    }
                } else {
                    showToast(response.message, 'danger');
                }
            },
            error: function() {
                showToast('Bir hata oluştu!', 'danger');
            }
        });
    });

    // Yorum ekleme fonksiyonu
    let commentCounter = 0;

    $('#addComment').click(function() {
        const commentHtml = `
            <div class="comment-item border rounded p-3 mb-2">
                <div class="d-flex justify-content-between mb-2">
                    <h6>Yorum #${++commentCounter}</h6>
                    <button type="button" class="btn btn-sm btn-danger remove-comment">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="mb-2">
                    <textarea class="form-control" name="comments[${commentCounter}][text]" rows="2" placeholder="Yorum"></textarea>
                </div>
                <div>
                    <input type="file" class="form-control" name="comments[${commentCounter}][image]" accept="image/*">
                </div>
            </div>
        `;
        
        $('#commentsContainer').append(commentHtml);
    });

    // Yorum silme
    $(document).on('click', '.remove-comment', function() {
        $(this).closest('.comment-item').remove();
    });

    // TinyMCE editör entegrasyonu
    if (typeof tinymce !== 'undefined' && document.getElementById('description')) {
        tinymce.init({
            selector: '#description',
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            height: 300
        });
    }

    // Select2 başlatma
    $('#city').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Şehir seçin',
        allowClear: true
    });

    // Form submit öncesi kontrol
    $('#taskForm').on('submit', function(e) {
        const cities = $('#city').val();
        if (cities && cities.length > 0) {
            // Seçili şehirler varsa formu gönder
            return true;
        } else {
            e.preventDefault();
            showToast('En az bir şehir seçmelisiniz!', 'danger');
            return false;
        }
    });

    // Görev düzenleme modalı açıldığında
    $('#taskModal').on('show.bs.modal', function(e) {
        const button = $(e.relatedTarget);
        const taskId = button.data('id');
        
        if (taskId) {
            // Görev verilerini getir
            $.get(`/gorev_sitesi/adminpanel/task/${taskId}`, function(task) {
                $('#taskId').val(task.id);
                $('#title').val(task.title);
                $('#category_id').val(task.category_id);
                $('#reward').val(task.reward);
                $('#quota').val(task.quota);
                $('#gender').val(task.gender);
                $('#guide_level').val(task.guide_level);
                $('#status').val(task.status);
                
                // Şehirleri seç
                if (task.cities) {
                    $('#city').val(task.cities.split(',')).trigger('change');
                }
                
                // ... diğer form alanları ...
            });
        }
    });

    // Initialize Managers
    themeManager.init();
    sidebarManager.init();
}); 