<?php /** @var array $room */ ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sala: <?= htmlspecialchars($room['name']) ?> - Jogosueca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-success m-0">Sala: <?= htmlspecialchars($room['name']) ?></h3>
            <a href="/leave_room?id=<?= $room['id'] ?>" class="btn btn-outline-secondary">Sair da Sala</a>
        </div>

        <?php if ($room['status'] === 'Waiting'): ?>
            <!-- LOBBY DA SALA -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">A aguardar jogadores (<?= count($room['players']) ?>/4)</h5>
                    <ul class="list-group list-group-flush mb-4">
                        <?php foreach ($room['players'] as $player): ?>
                            <li class="list-group-item">👤 <strong><?= htmlspecialchars($player) ?></strong></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (count($room['players']) === 4 && $room['owner_id'] === $_SESSION['user_id']): ?>
                        <div class="alert alert-info">A sala está cheia! Como proprietário, inicie a partida.</div>
                        <form action="/start_game?id=<?= $room['id'] ?>" method="POST">
                            <button type="submit" class="btn btn-success w-100 p-3 fw-bold">Iniciar Partida</button>
                        </form>
                    <?php elseif (count($room['players']) === 4): ?>
                        <div class="alert alert-info">A sala está cheia! A aguardar que o dono inicie a partida...</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif ($room['status'] === 'Playing' || $room['status'] === 'Finished'): ?>
            
            <!-- MESA DE JOGO (FRONTEND JS) -->
            <div class="card shadow border-0 bg-dark mb-4" id="game-board" 
                 data-jwt="<?= htmlspecialchars($_SESSION['jwt_token'] ?? '') ?>" 
                 data-room-id="<?= htmlspecialchars($room['id']) ?>">
                
                <div class="card-body p-4" style="background-color: #1a252f;">
                    
                    <!-- Top Bar: Pontuações e Trunfo -->
                    <div class="d-flex justify-content-between align-items-center mb-4 px-4 py-2 bg-dark rounded text-white shadow-sm">
                        <div class="fw-bold fs-5">Equipa A: <span class="text-warning" id="score-team-a">0</span></div>
                        <div class="text-center">
                            <span class="badge bg-primary fs-6 mb-1 border border-light">Trunfo: <span id="trump-card">-</span></span>
                            <div id="turn-indicator" class="text-white small fw-bold">A ligar ao Motor de Jogo...</div>
                        </div>
                        <div class="fw-bold fs-5">Equipa B: <span class="text-warning" id="score-team-b">0</span></div>
                    </div>

                    <!-- MESA VERDE DE SUECA -->
                    <div class="position-relative mx-auto shadow-lg border border-5 border-secondary" 
                         style="width: 100%; max-width: 800px; height: 450px; background: radial-gradient(circle, #27ae60, #145a32); border-radius: 220px;">
                        
                        <!-- Posição: Topo (Parceiro) -->
                        <div class="position-absolute top-0 start-50 translate-middle-x mt-3 text-center" style="z-index: 10;">
                            <img id="avatar-top" src="" class="rounded-circle border border-2 border-white shadow-sm d-none" width="60" height="60" style="object-fit: cover;">
                            <div id="name-top" class="text-white small fw-bold text-shadow mt-1">A carregar...</div>
                            <div id="cards-top" class="badge bg-dark mt-1 border border-secondary">🂠 ?</div>
                        </div>

                        <!-- Posição: Esquerda (Adversário) -->
                        <div class="position-absolute top-50 start-0 translate-middle-y ms-4 text-center" style="z-index: 10;">
                            <img id="avatar-left" src="" class="rounded-circle border border-2 border-white shadow-sm d-none" width="60" height="60" style="object-fit: cover;">
                            <div id="name-left" class="text-white small fw-bold text-shadow mt-1">A carregar...</div>
                            <div id="cards-left" class="badge bg-dark mt-1 border border-secondary">🂠 ?</div>
                        </div>

                        <!-- Posição: Direita (Adversário) -->
                        <div class="position-absolute top-50 end-0 translate-middle-y me-4 text-center" style="z-index: 10;">
                            <img id="avatar-right" src="" class="rounded-circle border border-2 border-white shadow-sm d-none" width="60" height="60" style="object-fit: cover;">
                            <div id="name-right" class="text-white small fw-bold text-shadow mt-1">A carregar...</div>
                            <div id="cards-right" class="badge bg-dark mt-1 border border-secondary">🂠 ?</div>
                        </div>

                        <!-- Posição: Centro (Cartas na mesa) -->
                        <div class="position-absolute top-50 start-50 translate-middle d-flex justify-content-center align-items-center" id="table-cards-container" style="width: 200px; height: 200px;">
                            <span class="text-white-50 small fst-italic">Mesa Vazia</span>
                        </div>
                    </div>

                    <!-- Minha Mão (Cartas para jogar) -->
                    <div class="mt-4 text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2 gap-2">
                            <img id="avatar-me" src="" class="rounded-circle border border-2 border-warning shadow d-none" width="45" height="45" style="object-fit: cover;">
                            <h5 class="text-white m-0 fw-bold" id="name-me">A desenhar a tua mão...</h5>
                        </div>
                        <div id="my-hand-container" class="d-flex justify-content-center flex-wrap px-3 py-3 bg-dark rounded shadow border border-secondary" style="min-height: 100px;"></div>
                    </div>
                </div>
            </div>

            <!-- Ficheiro JavaScript Responsável pela Interação -->
            <script src="../js/app.js"></script>
        <?php endif; ?>
    </div>
</body>
</html>