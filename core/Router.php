<?php
namespace Core;

class Router
{
    public function run()
    {
        // 1. Obtener la URL limpia
        $url = isset($_GET['url']) ? $_GET['url'] : 'Admin/dashboard'; // Ruta por defecto
        $url = rtrim($url, '/');
        $url = explode('/', $url);

        // 2. Definir Controlador y Método
        // Si la URL es "Client/toggle", $url[0] es Client
        $controllerName = !empty($url[0]) ? ucfirst($url[0]) . 'Controller' : 'AdminController';
        $methodName = !empty($url[1]) ? $url[1] : 'index';

        // 3. Ruta completa del archivo
        $controllerClass = "App\\Controllers\\" . $controllerName;

        // 4. Verificar y Ejecutar
        // Comprobamos si la clase existe (gracias al Autoload)
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();

            // Comprobamos si el método existe dentro de la clase
            if (method_exists($controller, $methodName)) {
                
                // Pasamos parámetros extra si los hay
                $params = array_slice($url, 2);
                call_user_func_array([$controller, $methodName], $params);
                
            } else {
                echo "Error 404: El método '$methodName' no existe en '$controllerName'.";
            }
        } else {
            echo "Error 404: El controlador '$controllerName' no existe.";
        }
    }
}