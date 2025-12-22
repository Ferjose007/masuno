<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\User;
use App\Models\Servicio; // <--- Importante

class AdminStylistController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();
        $estilistas = User::getAllStylists();
        
        // 1. Cargamos servicios para el formulario
        $servicios = Servicio::all(); 

        // 2. Para cada estilista, cargamos sus servicios actuales (para pintar el checklist al editar)
        // Esto es un pequeño truco para enviarlo ya listo a la vista
        foreach ($estilistas as $estilista) {
            $estilista->mis_servicios = User::getServiceIds($estilista->id); // Array [1, 2]
            $estilista->lista_servicios = User::getServicesObj($estilista->id); // Objetos para ver detalles
        }

        $this->view('admin/stylists/index', compact('estilistas', 'servicios'));
    }

    public function store()
    {
        $this->authorizeAdmin();
        
        if (!empty($_POST['nombre']) && !empty($_POST['email'])) {
            $data = $_POST;
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $data['rol'] = 'estilista'; 

            if (User::findByEmail($data['email'])) {
                header('Location: ' . BASE_URL . '/index.php?url=AdminStylist/index&error=email_exists');
                exit;
            }

            // A. Creamos el usuario
            User::create($data);
            
            // B. Obtenemos el ID del usuario recién creado (buscándolo por email es lo más rápido aquí)
            $newUser = User::findByEmail($data['email']);
            
            // C. Guardamos sus servicios
            if (isset($_POST['servicios']) && is_array($_POST['servicios'])) {
                User::syncServices($newUser->id, $_POST['servicios']);
            }
        }
        
        header('Location: ' . BASE_URL . '/index.php?url=AdminStylist/index');
        exit;
    }

    public function update()
    {
        $this->authorizeAdmin();
        
        $id = $_POST['id'] ?? null;
        $user = User::find((int)$id);
        
        if ($user) {
            $data = $_POST;
            if (!empty($_POST['password'])) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            } else {
                unset($data['password']); 
            }
            
            $user->update($data);

            // ACTUALIZAR SERVICIOS
            // Si no marcan ninguno, enviamos array vacío
            $serviciosSeleccionados = $_POST['servicios'] ?? [];
            User::syncServices($user->id, $serviciosSeleccionados);
        }
        
        header('Location: ' . BASE_URL . '/index.php?url=AdminStylist/index');
        exit;
    }

    // ... delete y toggle se mantienen igual ...
    // 4. Eliminar Estilista (AQUÍ ESTABA EL VACÍO)
    public function delete()
    {
        $this->authorizeAdmin();
        
        $id = $_GET['id'] ?? null;
        $user = User::find((int)$id);
        
        if ($user) {
            $user->delete();
        }
        
        header('Location: ' . BASE_URL . '/index.php?url=AdminStylist/index');
        exit;
    }

    // 5. Anular / Activar Toggle (AQUÍ ESTABA EL VACÍO)
    public function toggle()
    {
        $this->authorizeAdmin();
        
        $id = $_GET['id'] ?? null;
        $user = User::find((int)$id);
        
        if ($user) {
            $user->toggleStatus(); 
        }
        
        header('Location: ' . BASE_URL . '/index.php?url=AdminStylist/index');
        exit;
    }
}