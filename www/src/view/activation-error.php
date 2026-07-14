<?php global $i18n; ?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'pt' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $i18n['activation_error_title'] ?? 'Erro de ativação' ?> - Jogosueca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
</head>
<body class="bg-welcome bg-body-tertiary d-flex flex-column min-vh-100">

    <?php include __DIR__ . '/_partials_navbar.php'; ?>

    <main class="container flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-center">
                    <div class="card-body bg-body p-5">
                        <div class="mb-4">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 border border-danger border-opacity-25" style="width: 90px; height: 90px;">
                                <i class="bi bi-x-octagon-fill text-danger" style="font-size: 3rem;"></i>
                            </span>
                        </div>
                        <h3 class="fw-bold text-danger mb-2"><?= $i18n['activation_error_title'] ?? 'Token inválido ou conta já ativada.' ?></h3>
                        <p class="text-muted mb-4"><?= $i18n['activation_error_desc'] ?? 'O link de ativação pode ter expirado ou já foi utilizado.' ?></p>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <a href="?action=home" class="btn btn-outline-secondary fw-bold rounded-pill px-4">
                                <i class="bi bi-house-door-fill me-1"></i><?= $i18n['back_to_home'] ?? 'Voltar ao início' ?>
                            </a>
                            <a href="?action=login" class="btn btn-success fw-bold rounded-pill px-4">
                                <i class="bi bi-box-arrow-in-right me-1"></i><?= $i18n['login_btn'] ?? 'Entrar' ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/_partials_footer.php'; ?>
</body>
</html>
