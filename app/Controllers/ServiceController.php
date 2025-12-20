<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Servicio;

class ServiceController extends Controller
{
    // Listar servicios
    public function index()
    {
        $this->authorizeAdmin();
        $servicios = Servicio::all();
        $this->view('admin/services/index', compact('servicios'));
    }

    // Guardar nuevo servicio
    public function store()
    {
        $this->authorizeAdmin();
        if (!empty($_POST['nombre']) && !empty($_POST['precio'])) {
            Servicio::create($_POST);
        }
        header('Location: ' . BASE_URL . '/index.php?url=Service/index');
        exit;
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
        header('Location: ' . BASE_URL . '/index.php?url=Service/index');
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
        header('Location: ' . BASE_URL . '/index.php?url=Service/index');
        exit;
    }

    // NUEVO: Anular / Activar Servicio
    public function toggle()
    {
        $this->authorizeAdmin();
        $id = $_GET['id'] ?? null;
        $servicio = Servicio::find((int)$id);
        
        if ($servicio) {
            $servicio->toggleStatus();
        }
        header('Location: ' . BASE_URL . '/index.php?url=Service/index');
        exit;
    }
}