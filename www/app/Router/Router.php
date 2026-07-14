<?php
namespace Router;

class Router {
    private array $routes = [];

    // Regista uma rota GET
    public function get(string $path, array $action): void {
        $this->routes['GET'][$path] = $action;
    }

    // Regista uma rota POST
    public function post(string $path, array $action): void {
        $this->routes['POST'][$path] = $action;
    }

    // Executa a rota correta
    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Obtém o URL limpo (ex: /login)
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // [Mecanismo de Retrocompatibilidade para a Sprint 1, 2 e 3]
        if ($path === '/' && isset($_GET['action'])) {
            $actionParam = strtolower($_GET['action']);
            $path = $actionParam === 'home' ? '/' : '/' . $actionParam;
        }

        $action = null;
        $params = [];

        // [NOVO] Suporte a Rotas Dinâmicas (ex: /{username})
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            // Transforma {nome} numa expressão regular para apanhar o valor do URL
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[^/]+)', $route);
            $pattern = "#^" . $pattern . "$#";
            
            if (preg_match($pattern, $path, $matches)) {
                $action = $handler;
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = urldecode($match);
                    }
                }
                break;
            }
        }

        if (!$action) {
            http_response_code(404);
            echo "<div style='text-align:center; padding: 50px; font-family: sans-serif;'>";
            echo "<h2 style='color:#dc3545;'>404 - Página não encontrada</h2>";
            echo "<a href='/'>Voltar ao Lobby</a>";
            echo "</div>";
            return;
        }

        $controllerName = $action[0];
        $methodName = $action[1];

        // Instancia o controlador e chama o método via Reflexão Básica
        if (class_exists($controllerName)) {
            $controller = new $controllerName();
            if (method_exists($controller, $methodName)) {
                // [NOVO] Passa os parâmetros encontrados no URL para a função!
                $controller->$methodName(...array_values($params));
            } else {
                die("Erro de MVC: O método '{$methodName}' não existe na classe '{$controllerName}'.");
            }
        } else {
            die("Erro de MVC: O controlador '{$controllerName}' não foi encontrado pelo Autoloader.");
        }
    }
}