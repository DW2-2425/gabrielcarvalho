<?php
/**
 * Navbar partial — reutilizada pelas vistas de autenticação e utilitárias.
 * Inclui marca, seletor de tema e seletor de idioma (mesma proposta do lobby).
 */
global $i18n;
?>
<nav class="navbar navbar-expand-lg bg-body shadow-sm mb-4 border-bottom">
    <div class="container">
        <a class="navbar-brand fw-bold text-spades fs-4" href="/">
            <i class="bi bi-suit-heart-fill text-hearts"></i> Jogosueca
        </a>

        <div class="d-flex align-items-center gap-3 ms-auto">
            <!-- Seletor de Tema -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle rounded-pill px-3" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-circle-half"></i>
                    <span class="d-none d-sm-inline ms-1"><?= $i18n['theme'] ?? 'Tema' ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><a class="dropdown-item theme-btn d-flex align-items-center" href="#" data-theme="light"><i class="bi bi-sun me-2 text-warning"></i> <?= $i18n['theme_light'] ?? 'Claro' ?></a></li>
                    <li><a class="dropdown-item theme-btn d-flex align-items-center" href="#" data-theme="dark"><i class="bi bi-moon-stars me-2 text-primary"></i> <?= $i18n['theme_dark'] ?? 'Escuro' ?></a></li>
                    <li><a class="dropdown-item theme-btn d-flex align-items-center" href="#" data-theme="auto"><i class="bi bi-display me-2 text-secondary"></i> <?= $i18n['theme_auto'] ?? 'Auto' ?></a></li>
                </ul>
            </div>

            <!-- Seletor de Idioma -->
            <div class="btn-group btn-group-sm bg-body-secondary rounded-pill p-1">
                <a href="?lang=pt" class="btn rounded-pill <?= ($_SESSION['lang'] ?? 'pt') === 'pt' ? 'btn-hearts-card shadow-sm' : 'btn-link text-body text-decoration-none' ?>">PT</a>
                <a href="?lang=en" class="btn rounded-pill <?= ($_SESSION['lang'] ?? 'pt') === 'en' ? 'btn-hearts-card shadow-sm' : 'btn-link text-body text-decoration-none' ?>">EN</a>
            </div>
            <!-- Botão de Login ou Logout -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/logout" class="btn btn-outline-danger btn-sm rounded-pill px-3"><i class="bi bi-box-arrow-right me-1"></i><?= $i18n['logout'] ?? 'Terminar Sessão' ?></a>
            <?php else: ?>
                <a href="/login" class="btn btn-outline-success btn-sm rounded-pill px-3"><i class="bi bi-box-arrow-in-right me-1"></i><?= $i18n['login_btn'] ?? 'Iniciar Sessão' ?></a>
            <?php endif; ?>
        </div>
    </div>
</nav>
