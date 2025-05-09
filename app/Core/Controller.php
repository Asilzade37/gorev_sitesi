<?php
namespace App\Core;

class Controller
{
    protected $layout = 'default';

    public function __construct()
    {
        // Base constructor
    }

    protected function view($view, $data = [])
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        // View dosyasının içeriğini buffer'a al
        ob_start();
        extract($data);
        require_once __DIR__ . "/../views/{$view}.php";
        $content = ob_get_clean();

        // Layout varsa kullan
        if ($this->layout) {
            require_once __DIR__ . "/../views/layouts/{$this->layout}.php";
        } else {
            echo $content;
        }
    }

    protected function redirect($url)
    {
        // URL'nin başında /gorev_sitesi/ var mı kontrol et
        if (strpos($url, '/gorev_sitesi/') === 0) {
            header("Location: " . $url);
        } else {
            // Eğer yoksa ekle
            header("Location: /gorev_sitesi" . $url);
        }
        exit;
    }

    protected function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    protected function isAdmin()
    {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
    }

    protected function setFlash($type, $message)
    {
        $_SESSION['flash_type'] = $type;
        $_SESSION['flash_message'] = $message;
    }

    protected function adminView($view, $data = [])
    {
        // Data değişkenlerini extract et
        extract($data);

        // View dosyasının tam yolunu oluştur
        $viewPath = __DIR__ . "/../views/{$view}.php";
        
        if (!file_exists($viewPath)) {
            die("View dosyası bulunamadı: {$viewPath}");
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();
        
        require __DIR__ . "/../views/layouts/admin.php";
    }
}