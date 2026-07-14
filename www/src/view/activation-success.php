<?php global $i18n; ?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'pt' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $i18n['activation_success_title'] ?? 'Conta ativada' ?> - Jogosueca</title>
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
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 border border-success border-opacity-25" style="width: 90px; height: 90px;">
                                <i class="bi bi-check2-circle text-success" style="font-size: 3rem;"></i>
                            </span>
                        </div>
                        <h3 class="fw-bold text-success mb-2"><?= $i18n['activation_success_title'] ?? 'Conta ativada com sucesso!' ?></h3>
                        <p class="text-muted mb-4"><?= $i18n['activation_success_desc'] ?? 'Já pode entrar na sua conta e começar a jogar.' ?></p>
                        <a href="?action=login" class="btn btn-success btn-lg fw-bold rounded-pill shadow-sm px-4">
                            <i class="bi bi-box-arrow-in-right me-1"></i><?= $i18n['go_to_login'] ?? 'Ir para o Login' ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/_partials_footer.php'; ?>
</body>
</html>
