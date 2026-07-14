document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const form = document.getElementById('registerForm');
    const inputs = form.querySelectorAll('.validate-container input');

    inputs.forEach(input => {

        input.addEventListener('blur', () => {
            const container = input.closest('.validate-container');

            if (input.checkValidity()) {
                container.classList.remove('was-validated');
            } else {
                container.classList.add('was-validated');
            }
        });

    });

    form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();

            form.classList.add('was-validated');
        }
    });
});