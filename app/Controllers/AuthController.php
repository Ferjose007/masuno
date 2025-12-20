<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    // Muestra el formulario de Login
    public function showLogin()
    {
        $this->view('auth/login');
    }

    // Procesa el Login
    public function login()
    {
        // 1. Obtener datos
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // 2. Buscar usuario
        $user = User::findByEmail($email);

        // 3. Verificar contraseña y usuario existente
        if ($user && password_verify($password, $user->password)) {
            
            // --- NUEVA VALIDACIÓN: Verificar si está activo ---
            // Si activo es 0 (Falso), le impedimos el paso.
            if (isset($user->activo) && $user->activo == 0) {
                $error = "Tu cuenta ha sido desactivada. Por favor, contacta al administrador.";
                $this->view('auth/login', compact('error'));
                return;
            }
            // --------------------------------------------------

            // 4. Crear Sesión
            $_SESSION['user'] = [
                'id'     => $user->id,
                'nombre' => $user->nombre,
                'email'  => $user->email,
                'rol'    => $user->rol
            ];

            // 5. Redireccionar según Rol
            if ($user->rol === 'admin') {
                header('Location: ' . BASE_URL . '/index.php?url=Admin/dashboard');
            } elseif ($user->rol === 'estilista') {
                header('Location: ' . BASE_URL . '/index.php?url=Stylist/index');
            } else {
                // Cliente
                header('Location: ' . BASE_URL . '/index.php?url=Reservation/my');
            }
            exit;

        } else {
            // Credenciales incorrectas
            $error = "Correo o contraseña incorrectos.";
            $this->view('auth/login', compact('error'));
        }
    }

    // Muestra el formulario de Registro (Solo para Clientes públicos)
    public function showRegister()
    {
        $this->view('auth/register');
    }

    // Procesa el Registro de Clientes
    public function register()
    {
        $data = $_POST;
        
        // Validaciones básicas
        if ($data['password'] !== $data['confirm_password']) {
            $error = "Las contraseñas no coinciden.";
            $this->view('auth/register', compact('error'));
            return;
        }

        // Verificar si email ya existe
        if (User::findByEmail($data['email'])) {
            $error = "El correo ya está registrado.";
            $this->view('auth/register', compact('error'));
            return;
        }

        // Preparar datos
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['rol'] = 'cliente'; // Registro público siempre es cliente
        
        // Crear
        if (User::create($data)) {
            // Redirigir al login con mensaje de éxito (opcional)
            header('Location: ' . BASE_URL . '/index.php?url=Auth/showLogin');
            exit;
        } else {
            $error = "Error al registrar el usuario.";
            $this->view('auth/register', compact('error'));
        }
    }

    // Cerrar Sesión
    public function logout()
    {
        session_destroy();
        header('Location: ' . BASE_URL . '/index.php?url=Auth/showLogin');
        exit;
    }
}