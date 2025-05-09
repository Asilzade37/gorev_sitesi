<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/config.php';  // Config dosyasını dahil et

// URL'i parçala
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Controller ve method belirleme
$controllerName = 'HomeController';
$methodName = 'index';

if (!empty($url[0])) {
    // URL mapping - özel durumlar için
    $routeMap = [
        'adminpanel' => ['controller' => 'AdminController', 'method' => 'index'],
        'adminpanel/tasks' => ['controller' => 'AdminController', 'method' => 'tasks'],
        'adminpanel/task/create' => ['controller' => 'AdminController', 'method' => 'taskCreate'],
        'adminpanel/task/edit/{id}' => ['controller' => 'AdminController', 'method' => 'taskEdit'],
        'adminpanel/task/save' => ['controller' => 'AdminController', 'method' => 'taskSave'],
        'adminpanel/task/delete/{id}' => ['controller' => 'AdminController', 'method' => 'deleteTask'],
        'adminpanel/categories' => ['controller' => 'AdminController', 'method' => 'categories'],
        'adminpanel/category/create' => ['controller' => 'AdminController', 'method' => 'categoryCreate'],
        'adminpanel/category/edit/{id}' => ['controller' => 'AdminController', 'method' => 'categoryEdit'],
        'adminpanel/category/save' => ['controller' => 'AdminController', 'method' => 'categorySave'],
        'adminpanel/category/delete/{id}' => ['controller' => 'AdminController', 'method' => 'categoryDelete'],
        'giris' => ['controller' => 'AuthController', 'method' => 'loginForm'],
        'giris/auth' => ['controller' => 'AuthController', 'method' => 'login'],
        'kayit' => ['controller' => 'AuthController', 'method' => 'registerForm'],
        'kayit/save' => ['controller' => 'AuthController', 'method' => 'register'],
        'cikis' => ['controller' => 'AuthController', 'method' => 'logout'],
        'panel' => ['controller' => 'UserController', 'method' => 'index'],
        'panel/tasks' => ['controller' => 'UserController', 'method' => 'tasks'],
        'panel/task/{id}' => ['controller' => 'UserController', 'method' => 'taskDetail'],
        'gorev/{id}' => ['controller' => 'UserController', 'method' => 'taskDetail'],
        'panel/task/submit/{id}' => ['controller' => 'UserController', 'method' => 'submitTask'],
        'adminpanel/tasks/completed' => ['controller' => 'AdminController', 'method' => 'completedTasks'],
        'adminpanel/tasks/pending' => ['controller' => 'AdminController', 'method' => 'pendingTasks'],
        'adminpanel/submission/review/{id}' => ['controller' => 'AdminController', 'method' => 'reviewSubmission'],
        'panel/tasks/completed' => ['controller' => 'UserController', 'method' => 'completedTasks'],
        'panel/tasks/pending' => ['controller' => 'UserController', 'method' => 'pendingTasks'],
        'panel/profile' => ['controller' => 'UserController', 'method' => 'profile'],
        'panel/earnings' => ['controller' => 'UserController', 'method' => 'earnings'],
        'panel/settings' => ['controller' => 'UserController', 'method' => 'settings'],
    ];

    // Route kontrolü
    $requestUrl = rtrim(implode('/', $url), '/');

    // Parametre içeren route'ları kontrol et
    foreach ($routeMap as $pattern => $route) {
        $pattern = str_replace(['{id}'], ['(\d+)'], $pattern);
        $pattern = str_replace('/', '\/', $pattern);
        
        if (preg_match('/^' . $pattern . '$/', $requestUrl, $matches)) {
            $controllerName = $route['controller'];
            $methodName = $route['method'];
            
            // İlk eşleşme parametresi tam URL olduğu için onu atlıyoruz
            array_shift($matches);
            $params = $matches;
            
            try {
                $controllerClass = "App\\Controllers\\{$controllerName}";
                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    if (method_exists($controller, $methodName)) {
                        call_user_func_array([$controller, $methodName], $params);
                        exit;
                    }
                }
            } catch (Exception $e) {
                // Hata yönetimi
                header("HTTP/1.0 404 Not Found");
                echo "404 - Page Not Found: " . $e->getMessage();
                exit;
            }
        }
    }

    // Varsayılan routing mantığı
    $controllerName = ucfirst($url[0]) . 'Controller';
    if (isset($url[1])) {
        $methodName = $url[1];
    }
}

// Controller sınıf adını oluştur
$controllerClass = "App\\Controllers\\{$controllerName}";

try {
    if (class_exists($controllerClass)) {
        $controller = new $controllerClass();
        
        if (method_exists($controller, $methodName)) {
            // Parametreleri ayır
            $params = array_slice($url, 2);
            call_user_func_array([$controller, $methodName], $params);
        } else {
            throw new Exception("Method not found: {$methodName}");
        }
    } else {
        throw new Exception("Controller not found: {$controllerName}");
    }
} catch (Exception $e) {
    // Hata sayfasına yönlendir
    header("HTTP/1.0 404 Not Found");
    echo "404 - Page Not Found: " . $e->getMessage();
}