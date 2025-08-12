<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\TicketController;

// Simple router via ?r=path or REQUEST_URI
$r = $_GET['r'] ?? trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Route table is defined in src/routes.php
[$controller, $action] = ROUTES[$r] ?? ROUTES[''];

echo (new $controller)->{$action}();
