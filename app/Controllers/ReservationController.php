<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Servicio;
use App\Models\Horario;
use App\Models\Reserva;

class ReservationController extends Controller
{
    private function authorizeClienteOAdmin()
    {
        if (
            empty($_SESSION['user']) ||
            ! in_array($_SESSION['user']['rol'], ['cliente', 'admin'])
        ) {
            header('Location: index.php?url=Auth/showLogin');
            exit;
        }
    }

    // Nuevo: dashboard para clientes
    public function dashboard()
    {
        $this->authorizeClienteOAdmin();
        $uid       = $_SESSION['user']['id'];
        $nombre    = $_SESSION['user']['nombre'];
        $reservas  = Reserva::findByUser($uid);
        $count     = count($reservas);
        // Tomamos hasta 5 próximas (o las que haya)
        $upcoming  = array_slice($reservas, 0, 5);

        $this->view('reservations/dashboard', compact(
            'nombre',
            'count',
            'upcoming'
        ));
    }

    // Mostrar el formulario de reserva
    public function create()
    {
        $this->authorizeClienteOAdmin();
        $servicios = Servicio::all();
        // sólo horarios libres
        $todos     = Horario::all();
        $horarios  = array_filter($todos, fn($h) => $h->estado === 'L');
        $this->view('reservations/index', compact('servicios', 'horarios'));
    }

    // (Opcional) alias para que index haga lo mismo que create
    public function index()
    {
        $this->create();
    }

    // Guardar la reserva y marcar el horario
    public function store()
    {
        $this->authorizeClienteOAdmin();
        $uid = $_SESSION['user']['id'];
        $sid = (int)($_POST['servicio_id'] ?? 0);
        $hid = (int)($_POST['horario_id']  ?? 0);

        if ($sid && $hid) {
            Reserva::create([
                'usuario_id'  => $uid,
                'servicio_id' => $sid,
                'horario_id'  => $hid
            ]);
            // marcar horario como reservado
            $h = Horario::find($hid);
            if ($h) {
                $h->update([
                    'fecha'       => $h->fecha,
                    'hora_inicio' => $h->hora_inicio,
                    'hora_fin'    => $h->hora_fin,
                    'estado'      => 'R',
                    'estilista_id'  => $h->estilista_id,
                ]);
            }
        }
        header('Location: index.php?url=Reservation/my');
        exit;
    }

    // Listar las reservas del cliente
    public function my()
    {
        $this->authorizeClienteOAdmin();
        $uid      = $_SESSION['user']['id'];
        $reservas = Reserva::findByUser($uid);
        $this->view('reservations/my', compact('reservas'));
    }
}
