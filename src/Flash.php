<?php

namespace App;

final class Flash
{
    public static function set(string $type, string $msg): void
    {
        $_SESSION['flash'][$type] = $msg;
    }
    public static function get(?string $type = null): ?string
    {
        if ($type) {
            $m = $_SESSION['flash'][$type] ?? null;
            unset($_SESSION['flash'][$type]);
            return $m;
        }
        $all = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $all ? implode(' ', $all) : null;
    }
}
