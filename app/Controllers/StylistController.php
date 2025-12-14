<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Reserva;

class StylistController extends Controller
{
    private function authorizeStylist()
    {
        if (
            empty($_SESSION['user']) ||
            $_SESSION['user']['rol'] !== 'estilista'
        ) {
            header('Location: index.php?url=Auth/showLogin');
            exit;
        }
    }

    // 1. Dashboard con saludo y próximas citas
    public function dashboard()
    {
        $this->authorizeStylist();
        $nombre   = $_SESSION['user']['nombre'];
        $all      = Reserva::findAll();             // Método que ya creamos
        $today    = date('Y-m-d');
        $upcoming = array_filter(
            $all,
            fn($r) => $r->fecha >= $today
        );
        $count    = count($upcoming);
        $next5    = array_slice($upcoming, 0, 5);

        $this->view('stylist/dashboard', compact(
            'nombre','count','next5'
        ));
    }

    // 2. Listado completo de citas
    public function appointments()
    {
        $this->authorizeStylist();
        $reservas = Reserva::findAll();
        $this->view('stylist/index', compact('reservas'));
    }
}
