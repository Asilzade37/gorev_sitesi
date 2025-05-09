<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function loginForm()
    {
        $this->view('auth/login', [
            'title' => 'Giriş Yap',
            'layout' => 'layouts/default'
        ]);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/giris');
            return;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->setFlash('error', 'Lütfen tüm alanları doldurun');
            $this->redirect('/giris');
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            $this->setFlash('error', 'Email veya şifre hatalı');
            $this->redirect('/giris');
            return;
        }

        if (!password_verify($password, $user->password)) {
            $this->setFlash('error', 'Email veya şifre hatalı');
            $this->redirect('/giris');
            return;
        }

        // Oturum bilgilerini kaydet
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['is_admin'] = ($user->role === 'admin');

        // Kullanıcı tipine göre yönlendir
        if ($user->role === 'admin') {
            $this->redirect('/adminpanel');
        } else {
            $this->redirect('/panel');  // Panel sayfasına yönlendir
        }
    }

    public function registerForm()
    {
        $this->view('auth/register', [
            'title' => 'Kayıt Ol',
            'layout' => false
        ]);
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/kayit');
            return;
        }

        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'role' => 'user',
            'status' => 'active'
        ];

        // Şifre kontrolü
        if ($_POST['password'] !== $_POST['password_confirm']) {
            $this->setFlash('error', 'Şifreler eşleşmiyor');
            $this->redirect('/kayit');
            return;
        }

        // Email kontrolü
        if ($this->userModel->findByEmail($data['email'])) {
            $this->setFlash('error', 'Bu email adresi zaten kayıtlı');
            $this->redirect('/kayit');
            return;
        }

        if ($this->userModel->create($data)) {
            $this->setFlash('success', 'Kayıt başarılı! Giriş yapabilirsiniz.');
            $this->redirect('/giris');
        } else {
            $this->setFlash('error', 'Kayıt sırasında bir hata oluştu');
            $this->redirect('/kayit');
        }
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/');
    }
} 