<?php

use Router\Router;

// Importação das tuas classes Controlador
use Controller\AuthController;
use Controller\ProfileController;
use Controller\RoomController;

// O ficheiro retorna uma Closure para ser executada no index.php
return function (Router $router) { // Recebe o objeto Router do index.php
    
    // ==========================================
    // Lobby / Homepage (Sprint 3)
    // ==========================================
    $router->get('/', [RoomController::class, 'showLobby']);
    $router->get('/index.php', [RoomController::class, 'showLobby']);
    $router->get('/rules', [RoomController::class, 'showRules']);

    // ==========================================
    // Autenticação (Sprint 1)
    // ==========================================
    $router->get('/register', [AuthController::class, 'showRegister']);
    $router->post('/register', [AuthController::class, 'register']);
    $router->get('/activate', [AuthController::class, 'activate']);
    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->get('/logout', [AuthController::class, 'logout']);

    // ==========================================
    // Perfil (Sprint 2)
    // ==========================================
    $router->get('/profile', [ProfileController::class, 'show']);
    $router->post('/profile', [ProfileController::class, 'handlePost']);
   

    // ==========================================
    // Gestão de Salas e Jogo (Sprint 3 e 4)
    // ==========================================
    $router->post('/create_room', [RoomController::class, 'create']);
    $router->post('/create_training', [RoomController::class, 'createTraining']);
    $router->get('/join_room', [RoomController::class, 'join']);
    $router->get('/room', [RoomController::class, 'showRoom']);
    $router->get('/leave_room', [RoomController::class, 'leaveRoom']);
    $router->post('/start_game', [RoomController::class, 'startGame']);

    // ==========================================
    // Recuperação de Password (Sprint 5)
    // ==========================================

    $router->get('/forgot',[AuthController::class, 'forgotPassword']);
    $router->post('/forgot',[AuthController::class, 'processForgotPassword']);
    $router->get('/reset',[AuthController::class, 'resetPassword']);
    $router->post('/reset',[AuthController::class, 'processResetPassword']);

    // ==========================================
    // Perfil Público (Sprint 6)
    // ==========================================
    $router->get('/{username}', [ProfileController::class, 'showPublic']);

};