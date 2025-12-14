<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function showRegister()
    {
        $this->view('auth/register');
    }

    public function register()
    {
        // Validaciones sencillas (puedes robustecerlas)
        $nombre = trim($_POST['nombre'] ?? '');
        $email  = trim($_POST['email'] ?? '');
        $pass   = $_POST['password'] ?? '';
        $pass2  = $_POST['password2'] ?? '';

        $errors = [];
        if (!$nombre || !$email || !$pass || !$pass2) {
            $errors[] = "Todos los campos son obligatorios.";
        }
        if ($pass !== $pass2) {
            $errors[] = "Las contraseñas no coinciden.";
        }
        if (User::findByEmail($email)) {
            $errors[] = "El email ya está registrado.";
        }

        if ($errors) {
            return $this->view('auth/register', ['errors' => $errors, 'old' => $_POST]);
        }

        User::create(['nombre' => $nombre, 'email' => $email, 'password' => $pass]);
        header('Location: /masuno/public/index.php?url=Auth/showLogin');
        exit;
    }

    public function showLogin()
    {
        $this->view('auth/login');
    }

    public function login()
    {
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';
        $user  = User::findByEmail($email);

        if (!$user || !$user->verifyPassword($pass)) {
            $error = "Credenciales inválidas.";
            return $this->view('auth/login', ['error' => $error, 'old' => $_POST]);
        }

        // Crear sesión
        session_regenerate_id();
        $_SESSION['user'] = [
            'id'     => $user->id,
            'nombre' => $user->nombre,
            'email'  => $user->email,
            'rol'    => $user->rol
        ];

        // Redirigir según rol
        if ($user->rol === 'admin') {
            header('Location: /masuno/public/index.php?url=Admin/dashboard');
        } elseif ($user->rol === 'estilista') {
            header('Location: /masuno/public/index.php?url=Stylist/dashboard');
        } else {
            // cliente → enviamos al nuevo dashboard
            header('Location: /masuno/public/index.php?url=Reservation/dashboard');
        }
        exit;
    }

    public function logout()
    {
        // Si por algún motivo aún no se inició la sesión
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Destruir toda la sesión
        $_SESSION = [];
        session_unset();
        session_destroy();

        // Redirigir al login DE FORMA ABSOLUTA
        header('Location: /masuno/public/index.php?url=Auth/showLogin');
        exit;
    }
}
