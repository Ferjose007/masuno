<?php
namespace Core;

class Controller {
    public function view($view, $data = []) {
        extract($data);
        require __DIR__ . '/../app/views/' . $view . '.php';
    }

    // Método para proteger rutas: Si no es admin, lo saca.
    protected function authorizeAdmin() {
        if (empty($_SESSION['user']) || $_SESSION['user']['rol'] !== 'admin') {
            // Usamos BASE_URL si está definida, sino una ruta relativa
            $baseUrl = defined('BASE_URL') ? BASE_URL : '/masuno/public'; 
            header("Location: $baseUrl/index.php?url=Auth/showLogin");
            exit;
        }
    }   
}
