<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\User;
use App\Models\Servicio;
use App\Models\EstilistaServicio;

class AdminStylistController extends Controller
{
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

    // Listado de estilistas
    public function index()
    {
        $this->authorizeAdmin();
        $stylists = User::getAllStylists();
        $this->view('admin/stylists/index', compact('stylists'));
    }

    // Formulario de creación
    public function create()
    {
        $this->authorizeAdmin();
        $services = Servicio::all();
        $this->view('admin/stylists/form', [
            'action'   => 'store',
            'services' => $services
        ]);
    }

    // Guardar nuevo estilista
    public function store()
    {
        $this->authorizeAdmin();
        // Crear usuario
        $stmt = \Core\Database::getInstance()->prepare("
            INSERT INTO usuario (nombre,email,password,rol)
            VALUES (:nombre,:email,:password,'estilista')
        ");
        $stmt->execute([
            'nombre'   => $_POST['nombre'],
            'email'    => $_POST['email'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
        ]);
        $id = \Core\Database::getInstance()->lastInsertId();

        // Asignar servicios si se enviaron
        if (!empty($_POST['services'])) {
            EstilistaServicio::assignServices($id, $_POST['services']);
        }

        header('Location: index.php?url=AdminStylist/index');
        exit;
    }

    // Formulario de edición
    public function edit()
    {
        $this->authorizeAdmin();
        $id        = (int)($_GET['id'] ?? 0);
        $stylist   = User::findById($id);
        $services  = Servicio::all();
        $assigned  = EstilistaServicio::getServicesForStylist($id);
        $assignedIds = array_column($assigned, 'id');

        // Indicamos a la vista que debe usar la acción "update"
        $action = 'update';

        $this->view('admin/stylists/form', compact(
            'stylist','action','services','assignedIds'
        ));
    }

    // Actualizar estilista
    public function update()
    {
        $this->authorizeAdmin();
        $id = (int)($_POST['id'] ?? 0);
        // Actualizar nombre/email/password opcional
        $fields = ['nombre'=>$_POST['nombre'],'email'=>$_POST['email']];
        if (!empty($_POST['password'])) {
            $fields['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        $set = implode(',', array_map(fn($k) => "$k=:$k", array_keys($fields)));
        $fields['id'] = $id;

        \Core\Database::getInstance()
            ->prepare("UPDATE usuario SET $set WHERE id = :id")
            ->execute($fields);

        // Reasignar servicios
        EstilistaServicio::assignServices($id, $_POST['services'] ?? []);

        header('Location: index.php?url=AdminStylist/index');
        exit;
    }

    // Eliminar estilista
    public function delete()
    {
        $this->authorizeAdmin();
        $id = (int)($_GET['id'] ?? 0);
        \Core\Database::getInstance()
            ->prepare("DELETE FROM usuario WHERE id = :id")
            ->execute(['id'=>$id]);
        header('Location: index.php?url=AdminStylist/index');
        exit;
    }
}
