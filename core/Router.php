<?php

namespace Core;

class Router
{
    private $routes = [];

    // Registrar ruta GET
    public function get(string $uri, string $controllerAction)
    {
        $this->routes['GET'][$uri] = $controllerAction;
    }

    // Registrar ruta POST
    public function post(string $uri, string $controllerAction)
    {
        $this->routes['POST'][$uri] = $controllerAction;
    }

    // Ejecutar el router
    public function run()
    {
        // Obtiene la ruta desde ?url=... o '' si no existe
        $uri    = $_GET['url'] ?? '';
        $uri    = trim($uri, '/');
        $method = $_SERVER['REQUEST_METHOD'];

        // Busca la acción en el array de rutas
        $action = $this->routes[$method][$uri] ?? null;
        if (!$action) {
            http_response_code(404);
            echo "Página no encontrada";
            return;
        }

        list($controller, $method) = explode('@', $action);
        $controller = "App\\Controllers\\{$controller}";
        call_user_func([new $controller, $method]);
    }
}
