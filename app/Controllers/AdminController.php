<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Servicio;
use App\Models\Horario;
use App\Models\User;
use App\Models\Reserva;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Validar sesión y rol
        if (
            empty($_SESSION['user']) ||
            $_SESSION['user']['rol'] !== 'admin'
        ) {
            header('Location: /masuno/public/index.php?url=Auth/showLogin');
            exit;
        }

        // Traer datos para el dashboard
        $servicios = Servicio::all();
        $horarios  = Horario::all();
        $clientes  = User::getAllClients();
        $reservas = Reserva::findAll();

        // Pasar a la vista
        $this->view('admin/dashboard', [
            'servicios' => $servicios,
            'horarios'  => $horarios,
            'clientes'  => $clientes,
            'reservas'  => $reservas
        ]);
    }
}
