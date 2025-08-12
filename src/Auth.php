<?php

namespace App;

use App\DB;

final class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function attempt(string $username, string $password): bool
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $u = $stmt->fetch();
        if ($u && password_verify($password, $u['password_hash'])) {
            $_SESSION['user'] = ['id' => (int)$u['id'], 'username' => $u['username'], 'role' => $u['role']];
            return true;
        }
        return false;
    }

    public static function logout(): void
    {
        session_destroy();
        header('Location: /?r=login');
        exit;
    }
}
