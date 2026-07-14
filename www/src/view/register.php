<?php global $i18n; ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo - Jogosueca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
</head>
<body class="bg-welcome">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="card-title text-center text-success mb-4"><?= $i18n['register'] ?></h3>
                        
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>

                        <form method="POST" action="/register" id="registerForm" novalidate>
                            <div class="mb-3">
                                <label for="username" class="form-label"><?= $i18n['username'] ?></label>
                                <input type="text" class="form-control" id="username" name="username" required minlength="3" maxlength="10">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label"><?= $i18n['email'] ?></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label"><?= $i18n['password'] ?></label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="8" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{12,}$">
                                <div class="invalid-feedback"><?= $i18n['password_feedback'] ?></div>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label"><?= $i18n['confirm_password'] ?></label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <div class="invalid-feedback" id="confirm-feedback"><?= $i18n['password_mismatch'] ?></div>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-success"><?= $i18n['register_account'] ?></button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <a href="/" class="text-decoration-none text-muted"><?= $i18n['back_to_home'] ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/js/validate.js"></script>
</body>
</html>