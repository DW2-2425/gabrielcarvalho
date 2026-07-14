<?php global $i18n; ?>
<footer class="bg-body-tertiary border-top py-4 mt-auto">
    <div class="container text-center text-muted small">
        <p class="mb-1">&copy; <?= date('Y') ?> <?= $i18n['copyright'] ?? 'Jogosueca' ?></p>
        <p class="mb-0 opacity-75"><?= $i18n['signature'] ?? 'Desenvolvido no âmbito de DWII' ?></p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme.js"></script>
