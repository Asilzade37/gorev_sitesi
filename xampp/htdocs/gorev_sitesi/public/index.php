<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Hata raporlama
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Oturum başlat
session_start();

// Router işlemleri
$request = $_SERVER['REQUEST_URI'];
$request = str_replace('/gorev_sitesi/public/', '/', $request);
$request = str_replace('/gorev_sitesi/', '/', $request);
$request = str_replace('/gorev_sitesi', '/', $request);

$router = new \App\Core\Router();

// Ana rotalar
$router->get('/', 'HomeController@index');
$router->get('/gorevler', 'TaskController@index');
$router->get('/gorev/{id}', 'TaskController@show');
$router->post('/gorev/tamamla', 'TaskController@complete');

// Auth rotaları
$router->get('/giris', 'AuthController@loginForm');
$router->post('/giris', 'AuthController@login');
$router->get('/kayit', 'AuthController@registerForm');
$router->post('/kayit', 'AuthController@register');
$router->get('/cikis', 'AuthController@logout');

// Admin rotaları
$router->get('/admin/gorevler', 'Admin\TaskController@index');
$router->post('/admin/gorev/ekle', 'Admin\TaskController@create');
$router->post('/admin/gorev/onayla/{id}', 'Admin\TaskController@approve');
$router->post('/admin/gorev/reddet/{id}', 'Admin\TaskController@reject');

$router->dispatch();