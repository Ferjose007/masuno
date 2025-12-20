<?php

// 1. Mostrar errores (Útil para desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Iniciar sesión (Necesario para el login)
session_start();

// 3. Definir la constante BASE_URL
// Ajusta esto si tu carpeta se llama diferente a 'masuno'
define('BASE_URL', 'http://localhost/masuno/public');

// 4. Autocarga de clases (Para que funcionen los 'use')
spl_autoload_register(function ($class) {
    // Convierte los namespaces (App\Models\User) en rutas (../app/Models/User.php)
    $prefix = '';
    $base_dir = __DIR__ . '/../';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// 5. Cargar e Iniciar el Router Automático
use Core\Router;

$router = new Router();
$router->run();