<?php

namespace App\Controllers;

use App\Auth;
use App\Csrf;

final class AuthController
{
    public function login(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // var_dump($_POST);
            // exit;
            Csrf::check();
            $ok = Auth::attempt(trim($_POST['username'] ?? ''), $_POST['password'] ?? '');
            if ($ok) {
                header('Location: /');
                exit;
            }
            $_SESSION['error'] = 'Invalid credentials';
            header('Location: /?r=login');
            exit;
        }
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        ob_start();
        $title = 'Login';
        include __DIR__ . '/../Views/login.php';
        return ob_get_clean();
    }

    public function logout(): void
    {
        Auth::logout();
    }
}
