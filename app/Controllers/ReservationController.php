<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Reserva;
use App\Models\User;
use App\Models\Servicio;

class ReservationController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        // 1. Cargar Reservas
        $reservas = Reserva::all();
        $clientes = User::getAllClients();

        $servicios = Servicio::getActive();

        // Enviamos todo a la vista
        $this->view('admin/reservations/index', compact('reservas', 'clientes', 'servicios'));
    }

    public function store()
    {
        $this->authorizeAdmin();
        // Validar que vengan los IDs
        if (!empty($_POST['usuario_id']) && !empty($_POST['servicio_id']) && !empty($_POST['fecha_cita'])) {
            Reserva::create($_POST);
        }
        header('Location: ' . BASE_URL . '/index.php?url=Reservation/index');
        exit;
    }

    public function update()
    {
        $this->authorizeAdmin();
        $id = $_POST['id'] ?? null;
        $reserva = Reserva::find((int)$id);

        if ($reserva) {
            $reserva->update($_POST);
        }
        header('Location: ' . BASE_URL . '/index.php?url=Reservation/index');
        exit;
    }

    // Acción para CANCELAR (Anular) o CONFIRMAR
    public function changeStatus()
    {
        $this->authorizeAdmin();
        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? 'pendiente';

        $reserva = Reserva::find((int)$id);
        if ($reserva) {
            $reserva->changeStatus($status);
        }
        header('Location: ' . BASE_URL . '/index.php?url=Reservation/index');
        exit;
    }

    public function delete()
    {
        $this->authorizeAdmin();
        $id = $_GET['id'] ?? null;
        $reserva = Reserva::find((int)$id);
        if ($reserva) {
            $reserva->delete();
        }
        header('Location: ' . BASE_URL . '/index.php?url=Reservation/index');
        exit;
    }

    // Panel del Cliente: Mis Reservas
    public function my()
    {
        // 1. Verificar si hay sesión
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/index.php?url=Auth/showLogin');
            exit;
        }

        // 2. Obtener ID del usuario logueado
        $userId = $_SESSION['user']['id'];

        // 3. Buscar sus reservas
        $reservas = Reserva::getByUser($userId);

        // 4. Cargar la vista del cliente (crearemos esta carpeta ahora)
        $this->view('client/reservations/my', compact('reservas'));
    }

    // En app/Controllers/ReservationController.php

    public function finalizarVenta()
    {
        $this->authorizeAdmin(); // Asegúrate que esté logueado

        $reserva_id = $_POST['reserva_id'];
        $productos_json = $_POST['productos_data']; // Recibiremos un JSON desde el front

        // 1. Decodificar productos (ID, Cantidad, Precio)
        $productos = json_decode($productos_json, true);

        $reserva = Reserva::find($reserva_id);

        if ($reserva) {
            // 2. Guardar productos y descontar stock
            if (!empty($productos)) {
                $reserva->guardarProductos($productos);
            }

            // 3. Cambiar estado a COMPLETADA
            $reserva->updateStatus('completada');

            // 4. Redirigir a la BOLETA
            header("Location: " . BASE_URL . "/index.php?url=Reservation/ticket&id=" . $reserva_id);
            exit;
        }
    }

    public function ticket()
    {
        $this->authorizeAdmin();
        $id = $_GET['id'] ?? null;

        $reserva = Reserva::find($id);
        // Asumimos que tu modelo Reserva tiene método para traer cliente y servicio
        // Si no, deberías hacer joins o consultas extra aquí.

        // Traemos los productos vendidos
        $productos = $reserva->getProductos();

        // Vista exclusiva para imprimir
        require_once __DIR__ . '/../views/admin/reservations/ticket.php';
    }
}
