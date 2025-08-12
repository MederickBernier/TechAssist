<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base = __DIR__ . '/../app/';
    if (str_starts_with($class, $prefix)) {
        $rel = str_replace('\\', '/', substr($class, strlen($prefix)));
        $file = $base . $rel . '.php';
        if (is_file($file)) require $file;
    }
});

require __DIR__ . '/Env.php';
require __DIR__ . '/DB.php';
require __DIR__ . '/Csrf.php';
require __DIR__ . '/Auth.php';
require __DIR__ . '/routes.php';
require __DIR__ . '/Flash.php';
require __DIR__ . '/Audit.php';


// Load .env
App\Env::load(__DIR__ . '/../.env');

// Set TZ
date_default_timezone_set(getenv('TZ') ?: 'UTC');
