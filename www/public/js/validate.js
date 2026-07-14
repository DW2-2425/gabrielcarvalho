document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('registerForm');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');

            // Função para verificar se as passwords são iguais
            function validatePasswords() {
                if (confirmPassword.value) {
                    if (password.value !== confirmPassword.value) {
                        // Atira um erro nativo no HTML5
                        confirmPassword.setCustomValidity('As passwords não coincidem.');
                    } else {
                        // Limpa o erro
                        confirmPassword.setCustomValidity('');
                    }
                }
            }

            // Ouve as teclas a serem premidas em tempo real
            password.addEventListener('input', validatePasswords);
            confirmPassword.addEventListener('input', validatePasswords);

            // Interceta a submissão do formulário
            form.addEventListener('submit', function (event) {
                validatePasswords(); // Última verificação de segurança
                
                // Se algum campo falhar nas regras HTML (required, minlength ou customValidity)
                if (!form.checkValidity()) {
                    event.preventDefault(); // Impede o envio do formulário para o PHP
                    event.stopPropagation();
                }
                
                // Adiciona a classe do Bootstrap que pinta as caixas de verde/vermelho
                form.classList.add('was-validated');
            }, false);
        });