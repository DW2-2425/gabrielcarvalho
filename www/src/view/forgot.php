<?php global $i18n; ?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'pt' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $i18n['recover_account'] ?? 'Recuperar conta' ?> - Jogosueca</title>
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
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-warning text-dark py-3">
                        <h5 class="mb-0"><i class="bi bi-key-fill me-2"></i><?= $i18n['recover_account'] ?></h5>
                    </div>
                    <div class="card-body bg-body p-4 p-md-5">
                        <p class="text-muted small text-center mb-4">
                            <i class="bi bi-info-circle me-1"></i>
                            <?= $i18n['forgot_password_instructions'] ?>
                        </p>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger d-flex align-items-center shadow-sm">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success d-flex align-items-center shadow-sm">
                                <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/forgot">
                            <div class="mb-3">
                                <label for="email" class="form-label small fw-bold text-uppercase text-muted">Email</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-body"><i class="bi bi-envelope-fill text-warning"></i></span>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-success btn-lg fw-bold rounded-pill shadow-sm">
                                    <i class="bi bi-send-fill me-1"></i><?= $i18n['send_recovery_link'] ?>
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <a href="/login" class="text-decoration-none text-muted"><i class="bi bi-arrow-left me-1"></i><?= $i18n['back_to_login'] ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/_partials_footer.php'; ?>
</body>
</html>
