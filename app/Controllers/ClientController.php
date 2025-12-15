<?php
namespace App\Controllers;

use Core\Controller;
use Core\Database;
use App\Models\User;

class ClientController extends Controller
{
    /**
     * Verifica que el usuario logueado sea admin.
     */
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

    /**
     * Listado de clientes.
     */
    public function index()
    {
        $this->authorizeAdmin();
        // Obtiene todos los usuarios con rol 'cliente'
        $clientes = User::getAllClients();
        $this->view('admin/clients/index', compact('clientes'));
    }

    /**
     * Muestra detalle de un cliente.
     */
    public function show()
    {
        $this->authorizeAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $cliente = User::findById($id);
        $this->view('admin/clients/show', compact('cliente'));
    }

    /**
     * Formulario para crear un nuevo cliente.
     */
    public function create()
    {
        $this->authorizeAdmin();
        $errors = [];
        $old    = ['nombre'=>'', 'email'=>''];
        $this->view('admin/clients/form', compact('errors','old'));
    }

    /**
     * Procesa el POST de creación de cliente.
     */
    public function store()
    {
        $this->authorizeAdmin();
        $nombre    = trim($_POST['nombre']   ?? '');
        $email     = trim($_POST['email']    ?? '');
        $password  = $_POST['password']      ?? '';
        $password2 = $_POST['password2']     ?? '';
        $errors    = [];

        // Validaciones
        if ($nombre === '') {
            $errors[] = 'El nombre es obligatorio.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El email no es válido.';
        }
        if (strlen($password) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
        }
        if ($password !== $password2) {
            $errors[] = 'Las contraseñas no coinciden.';
        }

        if (!empty($errors)) {
            $old = ['nombre'=>$nombre,'email'=>$email];
            $this->view('admin/clients/form', compact('errors','old'));
            return;
        }

        // Inserta en BD
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO usuario (nombre,email,password,rol)
            VALUES (:n,:e,:p,'cliente')
        ");
        $stmt->execute([
            'n' => $nombre,
            'e' => $email,
            'p' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        header('Location: index.php?url=Client/index');
        exit;
    }

    /**
     * Formulario para editar un cliente existente.
     */
    public function edit()
    {
        $this->authorizeAdmin();
        $id      = (int)($_GET['id'] ?? 0);
        $cliente = User::findById($id);

        $errors = [];
        $old    = [
            'id'     => $cliente->id,
            'nombre' => $cliente->nombre,
            'email'  => $cliente->email
        ];

        $this->view('admin/clients/form', compact('errors','old'));
    }

    /**
     * Procesa el POST de actualización de cliente.
     */
    public function update()
    {
        $this->authorizeAdmin();
        $id     = (int)($_POST['id']     ?? 0);
        $nombre = trim($_POST['nombre']  ?? '');
        $email  = trim($_POST['email']   ?? '');
        $errors = [];

        // Validaciones mínimas
        if ($nombre === '') {
            $errors[] = 'El nombre es obligatorio.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El email no es válido.';
        }

        if (!empty($errors)) {
            $old = ['id'=>$id,'nombre'=>$nombre,'email'=>$email];
            $this->view('admin/clients/form', compact('errors','old'));
            return;
        }

        // Actualiza en BD
        $db = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE usuario
               SET nombre = :n,
                   email  = :e
             WHERE id = :id
        ");
        $stmt->execute([
            'n'  => $nombre,
            'e'  => $email,
            'id' => $id
        ]);

        header('Location: index.php?url=Client/index');
        exit;
    }

    /**
     * Elimina un cliente.
     */
    public function delete()
    {
        $this->authorizeAdmin();
        $id = (int)($_GET['id'] ?? 0);

        $db = Database::getInstance();
        $db->prepare("DELETE FROM usuario WHERE id = :id")
           ->execute(['id' => $id]);

        header('Location: index.php?url=Client/index');
        exit;
    }
}
