<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\User;
use App\Models\Reserva;


class AdminController extends Controller
{
    public function dashboard() {
        $this->authorizeAdmin();

        // Recopilar estadísticas
        $stats = [
            'citas_hoy'      => Reserva::countToday(),
            'ingresos_mes'   => Reserva::sumMonthlyRevenue(),
            'total_clientes' => User::countByRole('cliente'),
            'total_stylists' => User::countByRole('estilista')
        ];

        // Obtener lista de próximas citas
        $upcoming_appointments = Reserva::getUpcoming(5);

        // Enviar todo a la vista
        $this->view('admin/dashboard', compact('stats', 'upcoming_appointments'));
    }
}
