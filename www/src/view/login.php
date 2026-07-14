<?php global $i18n; ?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'pt' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $i18n['login_title'] ?? 'Login - Jogosueca' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
</head>
<body class="bg-welcome bg-body-tertiary d-flex flex-column min-vh-100">

    <?php include __DIR__ . '/_partials_navbar.php'; ?>

    <main class="container flex-grow-1">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-box-arrow-in-right me-2"></i><?= $i18n['login_btn'] ?? 'Entrar' ?></h5>
                    </div>
                    <div class="card-body bg-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-suit-spade-fill text-spades fs-1"></i>
                            <p class="text-muted mt-2 mb-0"><?= $i18n['welcome_guest'] ?? 'Bem-vindo de volta!' ?></p>
                        </div>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger d-flex align-items-center shadow-sm">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/login">
                            <div class="mb-3">
                                <label for="username" class="form-label small fw-bold text-uppercase text-muted"><?= $i18n['username'] ?></label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-body"><i class="bi bi-person-fill text-success"></i></span>
                                    <input type="text" class="form-control form-control-lg" id="username" name="username" placeholder="<?= $i18n['username_placeholder'] ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label small fw-bold text-uppercase text-muted"><?= $i18n['password'] ?></label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-body"><i class="bi bi-lock-fill text-success"></i></span>
                                    <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="••••••••" required>
                                </div>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-hearts-card btn-lg text-white fw-bold shadow-sm rounded-pill">
                                    <i class="bi bi-play-fill me-1"></i> <?= $i18n['enter_game-btn'] ?>
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <a href="/register" class="text-decoration-none text-success fw-bold">
                                <i class="bi bi-person-plus me-1"></i><?= $i18n['create_account'] ?>
                            </a>
                            <span class="mx-2 text-muted">·</span>
                            <a href="/forgot" class="text-decoration-none text-muted"><?= $i18n['forgot_password'] ?></a>
                        </div>
                        <div class="text-center mt-3">
                            <a href="/" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left me-1"></i><?= $i18n['back'] ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/_partials_footer.php'; ?>
</body>
</html>
