document.addEventListener('DOMContentLoaded', () => {
    const htmlElement = document.documentElement;
    const themeButtons = document.querySelectorAll('.theme-btn');

    // 1. Carregar tema salvo ou usar o sistema por defeito
    const savedTheme = localStorage.getItem('theme') || 'auto';
    applyTheme(savedTheme);

    // 2. Adicionar os cliques aos botões do menu dropdown
    themeButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const theme = btn.getAttribute('data-theme');
            localStorage.setItem('theme', theme);
            applyTheme(theme);
        });
    });

    // 3. Função que aplica as cores do Bootstrap
    function applyTheme(theme) {
        if (theme === 'auto') {
            // Verifica a preferência do sistema operativo (Windows/macOS/Android)
            const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            htmlElement.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
        } else {
            htmlElement.setAttribute('data-bs-theme', theme);
        }
    }
});