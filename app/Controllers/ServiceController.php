<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Servicio;

class ServiceController extends Controller
{
    // MÉTODO PRIVADO: centraliza la validación de acceso
    private function authorizeAdmin()
    {
        if (
            empty($_SESSION['user'])
            || $_SESSION['user']['rol'] !== 'admin'
        ) {
            header('Location: index.php?url=Auth/showLogin');
            exit;
        }
    }


    // Listar servicios
    public function index()
    {
        $this->authorizeAdmin();
        $servicios = Servicio::all();
        $this->view('admin/services/index', compact('servicios'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        $this->authorizeAdmin();
        $this->view('admin/services/form', ['action' => 'store']);
    }

    // Guardar nuevo servicio
    public function store()
    {
        $this->authorizeAdmin();
        Servicio::create($_POST);
        header('Location: index.php?url=Service/index');
        exit;
    }

    // Mostrar formulario de edición
    public function edit()
    {
        $this->authorizeAdmin();
        $id = $_GET['id'] ?? null;
        $servicio = Servicio::find((int)$id);
        if (!$servicio) {
            echo "Servicio no encontrado";
            return;
        }
        $this->view('admin/services/form', ['action' => 'update', 'servicio' => $servicio]);
    }

    // Actualizar servicio
    public function update()
    {
        $this->authorizeAdmin();
        $id = $_POST['id'] ?? null;
        $servicio = Servicio::find((int)$id);
        if ($servicio) {
            $servicio->update($_POST);
        }
        header('Location: index.php?url=Service/index');
        exit;
    }

    // Eliminar servicio
    public function delete()
    {
        $this->authorizeAdmin();
        $id = $_GET['id'] ?? null;
        $servicio = Servicio::find((int)$id);
        if ($servicio) {
            $servicio->delete();
        }
        header('Location: index.php?url=Service/index');
        exit;
    }
}
