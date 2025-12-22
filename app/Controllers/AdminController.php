<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Reserva;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $this->authorizeAdmin();

        // 1. Obtener Estadísticas
        // CORRECCIÓN: Usamos las claves EXACTAS que tu vista está pidiendo en los errores
        $stats = [
            'total_clientes' => User::countByRole('cliente'),       // Antes 'clientes'
            'citas_hoy'      => Reserva::countToday(),              // Este suele estar bien
            'ingresos_hoy'   => Reserva::sumDailyRevenue() ?? 0,
            'total_stylists' => User::countByRole('estilista')      // Antes 'estilistas'
        ];

        // 2. Obtener Próximas Citas
        $upcoming = Reserva::getUpcoming(7); 

        // 3. Enviar a la vista
        $this->view('admin/dashboard', compact('stats', 'upcoming'));
    }
}