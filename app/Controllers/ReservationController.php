<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Reserva;
use App\Models\User;
use App\Models\Servicio;
use App\Models\Producto;

class ReservationController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        // 1. Cargar Reservas
        $reservas = Reserva::all();
        $clientes = User::getAllClients();
        $estilistas = User::getAllStylists();
        $servicios = Servicio::getActive();
        $productos = Producto::getActive();

        // Enviamos todo a la vista
        $this->view('admin/reservations/index', compact('reservas', 'clientes', 'estilistas', 'servicios', 'productos'));
    }

    public function store()
    {
        $this->authorizeAdmin();

        // 1. Recibimos el ARRAY de servicios desde tu JS
        $data = $_POST;
        $listaServicios = $data['servicios'] ?? []; // Esto recibe: [1, 5, 8...]

        // Validar que haya al menos uno
        if (empty($listaServicios) || empty($data['cliente_id']) || empty($data['estilista_id'])) {
            // Redirigir con error si faltan datos
            header('Location: ' . BASE_URL . '/index.php?url=Reservation/index&error=missing_data');
            exit;
        }

        try {
            $db = \Core\Database::getInstance();
            $db->beginTransaction(); // Inicia modo seguro

            // 2. Insertamos la RESERVA (Cabecera)
            // Guardamos el PRIMER servicio en la columna 'servicio_id' para que el sistema antiguo no falle
            $servicioPrincipal = $listaServicios[0];

            $sql = "INSERT INTO reserva (usuario_id, estilista_id, servicio_id, fecha_cita, hora_cita, notas, estado) 
                VALUES (:uid, :eid, :sid, :fecha, :hora, :notas, 'pendiente')";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                'uid' => $data['cliente_id'],
                'eid' => $data['estilista_id'],
                'sid' => $servicioPrincipal, // Fallback para legacy
                'fecha' => $data['fecha_cita'],
                'hora' => $data['hora_cita'],
                'notas' => $data['notas'] ?? ''
            ]);

            $reserva_id = $db->lastInsertId(); // Obtenemos el ID creado

            // 3. Insertamos LOS DETALLES (El bucle mágico)
            $sqlDetalle = "INSERT INTO reserva_servicio (reserva_id, servicio_id, precio_momento) VALUES (:rid, :sid, :precio)";
            $stmtDetalle = $db->prepare($sqlDetalle);

            foreach ($listaServicios as $idServicio) {
                // Buscamos el precio real de cada servicio para guardarlo
                $svc = Servicio::find($idServicio);
                if ($svc) {
                    $stmtDetalle->execute([
                        'rid' => $reserva_id,
                        'sid' => $idServicio,
                        'precio' => $svc->precio
                    ]);
                }
            }

            $db->commit(); // Confirmar cambios

        } catch (\Exception $e) {
            $db->rollBack(); // Si falla algo, deshacer todo
            die("Error: " . $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/index.php?url=Reservation/index');
        exit;
    }

    public function update()
    {
        $this->authorizeAdmin();
        $data = $_POST;

        if (empty($data['id'])) {
            header('Location: ' . BASE_URL . '/index.php?url=Reservation/index');
            exit;
        }

        $reserva = Reserva::find($data['id']);

        if ($reserva) {
            // 1. Actualizar datos principales (usando el método update del modelo que arreglamos antes)
            // Esto actualiza cliente, estilista, fecha, hora y el servicio_id legacy
            $reserva->update($data);

            // 2. ACTUALIZAR MÚLTIPLES SERVICIOS
            // A. Primero borramos los anteriores para evitar duplicados
            $db = \Core\Database::getInstance();
            $del = $db->prepare("DELETE FROM reserva_servicio WHERE reserva_id = :id");
            $del->execute(['id' => $data['id']]);

            // B. Insertamos los nuevos seleccionados
            $servicios = $data['servicios'] ?? [];
            if (!empty($servicios)) {
                foreach ($servicios as $svcId) {
                    $svc = \App\Models\Servicio::find($svcId);
                    if ($svc) {
                        $ins = $db->prepare("INSERT INTO reserva_servicio (reserva_id, servicio_id, precio_momento) VALUES (:rid, :sid, :precio)");
                        $ins->execute([
                            'rid' => $data['id'],
                            'sid' => $svcId,
                            'precio' => $svc->precio
                        ]);
                    }
                }
            }
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

        // Lista blanca de estados permitidos por seguridad
        $allowed = ['pendiente', 'confirmada', 'en_proceso', 'completada', 'cancelada'];

        if ($id && in_array($status, $allowed)) {
            $reserva = \App\Models\Reserva::find($id);
            if ($reserva) {
                $reserva->updateStatus($status);
            }
        }
        header('Location: ' . BASE_URL . '/index.php?url=Reservation/index');
        exit;
    }

    public function delete()
    {
        $this->authorizeAdmin();
        $id = $_GET['id'] ?? null;
        $reserva = Reserva::find((int) $id);
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
        $this->authorizeAdmin();

        $data = $_POST;

        // Validación básica
        if (empty($data['reserva_id']) || empty($data['productos_data'])) {
            header('Location: ' . BASE_URL . '/index.php?url=Reservation/index&error=missing_data');
            exit;
        }

        // Decodificar el JSON del carrito
        $productosCarrito = json_decode($data['productos_data'], true);

        // 1. AGRUPAR CANTIDADES
        // Transformamos [ID 5, ID 5, ID 5] en { "5": 3 }
        $conteo = [];
        if (is_array($productosCarrito)) {
            foreach ($productosCarrito as $item) {
                if (!isset($conteo[$item['id']]))
                    $conteo[$item['id']] = 0;
                $conteo[$item['id']]++;
            }
        }

        $db = \Core\Database::getInstance();
        $db->beginTransaction(); // INICIO TRANSACCIÓN

        try {
            // Preparamos la consulta de inserción una sola vez para usarla en el bucle
            // Asumo que tienes una tabla 'reserva_producto' para guardar lo que se vendió
            $sqlInsertVenta = "INSERT INTO reserva_producto (reserva_id, producto_id, cantidad, precio_unitario) 
                           VALUES (:rid, :pid, :cant, :precio)";
            $stmtInsertVenta = $db->prepare($sqlInsertVenta);

            // 2. PROCESAR CADA PRODUCTO (Verificar Stock -> Restar -> Guardar Venta)
            foreach ($conteo as $prodId => $cantidadNecesaria) {

                // A. Buscamos Stock y PRECIO (Importante sacar el precio de la BD por seguridad)
                $stmt = $db->prepare("SELECT stock, nombre, precio FROM producto WHERE id = :id FOR UPDATE");
                $stmt->execute(['id' => $prodId]);
                $prodReal = $stmt->fetch(\PDO::FETCH_OBJ);

                // B. Validación de Stock estricta
                if (!$prodReal || $prodReal->stock < $cantidadNecesaria) {
                    throw new \Exception("Stock insuficiente para: " . $prodReal->nombre . ". Disponibles: " . $prodReal->stock);
                }

                // C. Descontar Stock
                $stmtUpdate = $db->prepare("UPDATE producto SET stock = stock - :cant WHERE id = :id");
                $stmtUpdate->execute(['cant' => $cantidadNecesaria, 'id' => $prodId]);

                // D. GUARDAR EL DETALLE DE LA VENTA (Lo que faltaba)
                $stmtInsertVenta->execute([
                    'rid' => $data['reserva_id'],
                    'pid' => $prodId,
                    'cant' => $cantidadNecesaria,
                    'precio' => $prodReal->precio // Usamos el precio de la BD, no el del JSON (seguridad)
                ]);
            }

            // 3. CAMBIAR ESTADO DE LA RESERVA A "COMPLETADA"
            $stmtEstado = $db->prepare("UPDATE reserva SET estado = 'completada' WHERE id = :id");
            $stmtEstado->execute(['id' => $data['reserva_id']]);

            $db->commit(); // CONFIRMAR CAMBIOS

            // Redirigir con éxito
            header('Location: ' . BASE_URL . '/index.php?url=Reservation/index&success=checkout_ok');
            exit;

        } catch (\Exception $e) {
            $db->rollBack(); // DESHACER TODO SI HAY ERROR

            // Puedes redirigir con el error en la URL para mostrarlo en una alerta
            error_log("Error en Checkout: " . $e->getMessage()); // Guardar en log del servidor
            header('Location: ' . BASE_URL . '/index.php?url=Reservation/index&error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function ticket()
    {
        $this->authorizeAdmin();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '/index.php?url=Reservation/index');
            exit;
        }

        $reserva = Reserva::getByIdWithDetails($id);

        if (!$reserva) {
            die("Reserva no encontrada.");
        }

        // 1. Obtener Servicios (Usando el método estático que creamos antes)
        $servicios = Reserva::getServiciosPorReserva($id);

        // 2. Obtener Productos Vendidos (Usando el método del modelo)
        // Nota: Asegúrate de tener el método getProductos() en tu modelo Reserva
        $productos = Reserva::getProductosPorReserva($id);

        // 3. Cargar la vista
        // NOTA: No usamos $this->view() con layout porque queremos una hoja limpia
        // Hacemos el include directo o usamos una vista sin header/footer
        require_once __DIR__ . '/../views/admin/reservations/ticket.php';
    }
}
