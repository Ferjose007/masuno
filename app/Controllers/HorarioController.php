<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Horario;
use App\Models\EstadoHorario;
use App\Models\User;

class HorarioController extends Controller
{
    // Valida que sea admin
    private function authorizeAdmin()
    {
        if (
            empty($_SESSION['user']) ||
            $_SESSION['user']['rol'] !== 'admin'
        ) {
            header('Location: index.php?url=Auth/showLogin');
            exit;
        }
    }

    // Listar todos los horarios
    public function index()
    {
        $this->authorizeAdmin();
        $horarios = Horario::all();
        $this->view('admin/horarios/index', compact('horarios'));
    }

    // Formulario de creación
    public function create()
    {
        $this->authorizeAdmin();
        $estados    = EstadoHorario::all();
        $estilistas = User::getAllStylists();   // solo rol=estilista
        $this->view('admin/horarios/form', [
            'action'     => 'store',
            'estados'    => $estados,
            'estilistas' => $estilistas,
        ]);
    }

    // Guardar nuevo horario
    public function store()
    {
        $this->authorizeAdmin();
        Horario::create($_POST);
        header('Location: index.php?url=Horario/index');
        exit;
    }

    // Formulario de edición
    public function edit()
    {
        $this->authorizeAdmin();
        $id         = (int)($_GET['id'] ?? 0);
        $horario    = Horario::find($id);
        $estados    = EstadoHorario::all();
        $estilistas = User::getAllStylists();
        $this->view('admin/horarios/form', [
            'action'     => 'update',
            'horario'    => $horario,
            'estados'    => $estados,
            'estilistas' => $estilistas,
        ]);
    }

    // Actualizar horario existente
    public function update()
    {
        $this->authorizeAdmin();
        $id       = $_POST['id'] ?? null;
        $horario  = Horario::find((int)$id);
        if ($horario) {
            $horario->update($_POST);
        }
        header('Location: index.php?url=Horario/index');
        exit;
    }

    // Eliminar horario
    public function delete()
    {
        $this->authorizeAdmin();
        $id       = $_GET['id'] ?? null;
        $horario  = Horario::find((int)$id);
        if ($horario) {
            $horario->delete();
        }
        header('Location: index.php?url=Horario/index');
        exit;
    }
}
