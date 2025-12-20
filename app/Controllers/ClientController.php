<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\User;

class ClientController extends Controller
{
    // 1. Listar Clientes (Vista Única con Modales)
    public function index()
    {
        $this->authorizeAdmin(); // Seguridad (heredada de Core\Controller)
        
        $clientes = User::getAllClients(); 
        
        // Carga la vista que contiene la tabla y todos los modales
        $this->view('admin/clients/index', compact('clientes'));
    }

    // 2. Guardar Nuevo Cliente (Desde Modal Crear)
    public function store()
    {
        $this->authorizeAdmin();
        
        // Validar campos obligatorios
        if (!empty($_POST['nombre']) && !empty($_POST['email']) && !empty($_POST['password'])) {
            $data = $_POST;
            
            // Seguridad: Encriptar contraseña
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            
            // Forzar rol de cliente
            $data['rol'] = 'cliente'; 

            // Verificar duplicados (opcional pero recomendado)
            if (User::findByEmail($data['email'])) {
                // Aquí podrías manejar un error, por simplicidad redirigimos
                header('Location: ' . BASE_URL . '/index.php?url=Client/index&error=email_exists');
                exit;
            }

            User::create($data);
        }
        
        header('Location: ' . BASE_URL . '/index.php?url=Client/index');
        exit;
    }

    // 3. Actualizar Cliente (Desde Modal Editar)
    public function update()
    {
        $this->authorizeAdmin();
        
        $id = $_POST['id'] ?? null;
        $user = User::find((int)$id);
        
        if ($user) {
            $data = $_POST;
            
            // Lógica de Contraseña:
            // Si el campo password NO está vacío, lo encriptamos y actualizamos.
            // Si está vacío, lo quitamos del array para no sobrescribir la actual con vacío.
            if (!empty($_POST['password'])) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            } else {
                unset($data['password']); 
            }
            
            // El método update del modelo se encarga de filtrar campos y actualizar 'updated_at'
            $user->update($data);
        }
        
        header('Location: ' . BASE_URL . '/index.php?url=Client/index');
        exit;
    }

    // 4. Eliminar Cliente (Borrado Permanente)
    public function delete()
    {
        $this->authorizeAdmin();
        
        $id = $_GET['id'] ?? null;
        $user = User::find((int)$id);
        
        if ($user) {
            $user->delete();
        }
        
        header('Location: ' . BASE_URL . '/index.php?url=Client/index');
        exit;
    }

    // 5. Anular / Activar Cliente (Soft Delete / Toggle)
    public function toggle()
    {
        $this->authorizeAdmin();
        
        $id = $_GET['id'] ?? null;
        $user = User::find((int)$id);
        
        if ($user) {
            // Llama al método del modelo que cambia 1 a 0 y viceversa
            $user->toggleStatus(); 
        }
        
        header('Location: ' . BASE_URL . '/index.php?url=Client/index');
        exit;
    }
}