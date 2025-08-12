<?php

use App\Controllers\AuthController;
use App\Controllers\TicketController;

const ROUTES = [
    ''                  => [TicketController::class, 'index'],
    'login'             => [AuthController::class, 'login'],
    'logout'            => [AuthController::class, 'logout'],

    'tickets'           => [TicketController::class, 'index'],
    'tickets/create'    => [TicketController::class, 'create'],
    'tickets/store'     => [TicketController::class, 'store'],      // POST
    'tickets/view'      => [TicketController::class, 'show'],       // GET id
    'tickets/comment'   => [TicketController::class, 'comment'],    // POST
    'tickets/close'     => [TicketController::class, 'close'],      // POST
    'tickets/reopen'    => [TicketController::class, 'reopen'],     // POST
    'tickets/assign'    => [TicketController::class, 'assign'],     // POST (admin)
    'tickets/audit'     => [TicketController::class, 'audit'], // GET
    'audit'             => [TicketController::class, 'audit'],
];
