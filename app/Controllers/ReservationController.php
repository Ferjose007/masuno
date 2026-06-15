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

        $reservas = Reserva::all();
        $clientes = User::getAllClients();
        $estilistas = User::getAllStylists();
        $servicios = Servicio::getActive();
        $productos = Producto::getActive();

        $this->view('admin/reservations/index', compact('reservas', 'clientes', 'estilistas', 'servicios', 'productos'));
    }

    // =========================================================
    // CREAR RESERVA (Calculando Precio Inicial)
    // =========================================================
    public function store()
    {
        $this->authorizeAdmin();

        $data = $_POST;
        $listaServicios = $data['servicios'] ?? [];

        if (empty($listaServicios) || empty($data['cliente_id']) || empty($data['estilista_id'])) {
            header('Location: ' . BASE_URL . '/index.php?url=Reservation/index&error=missing_data');
            exit;
        }

        try {
            $db = \Core\Database::getInstance();
            $db->beginTransaction();

            // 1. CALCULAR PRECIO FINAL (Servicios)
            // Calculamos cuánto cuestan los servicios seleccionados HOY
            $precioInicial = $this->calcularTotalServicios($listaServicios);

            // 2. INSERTAR RESERVA
            $servicioPrincipal = $listaServicios[0];

            // AGREGAMOS 'precio_final' AL INSERT
            $sql = "INSERT INTO reserva (usuario_id, estilista_id, servicio_id, fecha_cita, hora_cita, notas, estado, precio_final) 
                    VALUES (:uid, :eid, :sid, :fecha, :hora, :notas, 'pendiente', :precio)";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                'uid' => $data['cliente_id'],
                'eid' => $data['estilista_id'],
                'sid' => $servicioPrincipal,
                'fecha' => $data['fecha_cita'],
                'hora' => $data['hora_cita'],
                'notas' => $data['notas'] ?? '',
                'precio' => $precioInicial // <--- AQUÍ GUARDAMOS EL TOTAL
            ]);

            $reserva_id = $db->lastInsertId();

            // 3. INSERTAR DETALLES
            $sqlDetalle = "INSERT INTO reserva_servicio (reserva_id, servicio_id, precio_momento) VALUES (:rid, :sid, :precio)";
            $stmtDetalle = $db->prepare($sqlDetalle);

            foreach ($listaServicios as $idServicio) {
                $svc = Servicio::find($idServicio);
                if ($svc) {
                    $stmtDetalle->execute([
                        'rid' => $reserva_id,
                        'sid' => $idServicio,
                        'precio' => $svc->precio
                    ]);
                }
            }

            $db->commit();

        } catch (\Exception $e) {
            $db->rollBack();
            die("Error: " . $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/index.php?url=Reservation/index');
        exit;
    }

    // =========================================================
    // EDITAR RESERVA (Recalculando Precio)
    // =========================================================
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
            // 1. Recalcular el nuevo precio de servicios
            $servicios = $data['servicios'] ?? [];
            $nuevoPrecioServicios = $this->calcularTotalServicios($servicios);

            // IMPORTANTE: Si la reserva ya tenía productos vendidos, deberíamos sumarlos.
            // Por simplicidad en edición básica, asumimos que 'update' solo toca servicios.
            // Si quieres ser muy estricto, deberías sumar $reserva->totalProductos() + $nuevoPrecioServicios.
            // Por ahora, actualizamos el precio base de la cita.
            $data['precio_final'] = $nuevoPrecioServicios;

            // 2. Actualizar Reserva (Datos básicos + Precio)
            $reserva->update($data);

            // 3. Actualizar Relación Servicios
            $db = \Core\Database::getInstance();
            $del = $db->prepare("DELETE FROM reserva_servicio WHERE reserva_id = :id");
            $del->execute(['id' => $data['id']]);

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

    // =========================================================
    // CHECKOUT / VENTA PRODUCTOS + FACTURACIÓN ELECTRÓNICA
    // =========================================================
    public function finalizarVenta()
    {
        $this->authorizeAdmin();
        $data = $_POST;

        if (empty($data['reserva_id'])) {
            header('Location: ' . BASE_URL . '/index.php?url=Reservation/index&error=missing_data');
            exit;
        }

        $productosCarrito = !empty($data['productos_data']) ? json_decode($data['productos_data'], true) : [];

        // Agrupar cantidades
        $conteo = [];
        if (is_array($productosCarrito)) {
            foreach ($productosCarrito as $item) {
                if (!isset($conteo[$item['id']]))
                    $conteo[$item['id']] = 0;
                $conteo[$item['id']]++;
            }
        }

        $db = \Core\Database::getInstance();
        $db->beginTransaction();

        try {
            $sqlInsertVenta = "INSERT INTO reserva_producto (reserva_id, producto_id, cantidad, precio_unitario) VALUES (:rid, :pid, :cant, :precio)";
            $stmtInsertVenta = $db->prepare($sqlInsertVenta);

            $totalVentaProductos = 0;
            $productosParaSunat = []; // <-- ARREGLO PARA GREENTER

            foreach ($conteo as $prodId => $cantidadNecesaria) {
                // Bloqueamos fila para evitar condiciones de carrera en stock
                $stmt = $db->prepare("SELECT stock, nombre, precio FROM producto WHERE id = :id FOR UPDATE");
                $stmt->execute(['id' => $prodId]);
                $prodReal = $stmt->fetch(\PDO::FETCH_OBJ);

                if (!$prodReal || $prodReal->stock < $cantidadNecesaria) {
                    throw new \Exception("Stock insuficiente para: " . $prodReal->nombre);
                }

                // Descontar Stock
                $stmtUpdate = $db->prepare("UPDATE producto SET stock = stock - :cant WHERE id = :id");
                $stmtUpdate->execute(['cant' => $cantidadNecesaria, 'id' => $prodId]);

                // Guardar Detalle
                $stmtInsertVenta->execute([
                    'rid' => $data['reserva_id'],
                    'pid' => $prodId,
                    'cant' => $cantidadNecesaria,
                    'precio' => $prodReal->precio
                ]);

                $totalVentaProductos += ($prodReal->precio * $cantidadNecesaria);

                // Llenamos los datos para enviar a SUNAT
                $productosParaSunat[] = [
                    'id' => $prodId,
                    'nombre' => $prodReal->nombre,
                    'precio' => $prodReal->precio,
                    'cantidad' => $cantidadNecesaria
                ];
            }

            // --- ACTUALIZAR PRECIO FINAL DE LA RESERVA ---
            $sqlFinal = "UPDATE reserva 
                         SET estado = 'completada', 
                             precio_final = precio_final + :totalProd,
                             finalizado_en = NOW() 
                         WHERE id = :id";

            $stmtEstado = $db->prepare($sqlFinal);
            $stmtEstado->execute([
                'totalProd' => $totalVentaProductos,
                'id' => $data['reserva_id']
            ]);

            $db->commit(); // VENTA GUARDADA CON ÉXITO

            // =======================================================
            // 🚀 INICIO DE FACTURACIÓN ELECTRÓNICA (GREENTER)
            // =======================================================
            try {
                $res_id = $data['reserva_id'];

                // 1. Obtener Datos del Cliente (Usando JOIN a la reserva)
                $stmtCli = $db->prepare("SELECT u.nombre, u.dni FROM reserva r JOIN usuario u ON r.usuario_id = u.id WHERE r.id = ?");
                $stmtCli->execute([$res_id]);
                $clienteBD = $stmtCli->fetch(\PDO::FETCH_ASSOC);

                $clienteSunat = [
                    'dni' => !empty($clienteBD['dni']) ? $clienteBD['dni'] : '00000000',
                    'nombre' => !empty($clienteBD['nombre']) ? $clienteBD['nombre'] : 'CLIENTE VARIOS'
                ];

                // 2. Obtener los Servicios que se hizo en la reserva
                $stmtServ = $db->prepare("SELECT s.id, s.nombre, rs.precio_momento as precio 
                                          FROM reserva_servicio rs 
                                          JOIN servicio s ON rs.servicio_id = s.id 
                                          WHERE rs.reserva_id = ?");
                $stmtServ->execute([$res_id]);
                $serviciosParaSunat = $stmtServ->fetchAll(\PDO::FETCH_ASSOC);

                // 3. Obtener el Siguiente Correlativo
                $stmtCorr = $db->query("SELECT COALESCE(MAX(correlativo), 0) + 1 FROM comprobantes WHERE serie = 'B001'");
                $siguienteCorrelativo = $stmtCorr->fetchColumn();

                // 4. Emitir Boleta
                $sunatService = new \App\Services\SunatService();
                $resultadoSunat = $sunatService->emitirBoleta(
                    $clienteSunat,
                    $serviciosParaSunat,
                    $productosParaSunat,
                    $siguienteCorrelativo
                );

                // 5. Guardar Comprobante en la Base de Datos
                if ($resultadoSunat['exito']) {
                    $sqlC = "INSERT INTO comprobantes 
                        (reserva_id, tipo_comprobante, serie, correlativo, cliente_tipo_doc, cliente_num_doc, cliente_nombre, 
                        total_gravada, total_igv, total_total, estado_sunat, mensaje_sunat, hash_cpe, xml_path, cdr_path) 
                        VALUES (?, '03', 'B001', ?, '1', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $db->prepare($sqlC)->execute([
                        $res_id,
                        $siguienteCorrelativo,
                        $clienteSunat['dni'],
                        $clienteSunat['nombre'],
                        $resultadoSunat['totales']['gravada'],
                        $resultadoSunat['totales']['igv'],
                        $resultadoSunat['totales']['total'],
                        $resultadoSunat['estado_sunat'],
                        $resultadoSunat['mensaje'],
                        $resultadoSunat['hash_cpe'],
                        $resultadoSunat['ruta_xml'],
                        $resultadoSunat['ruta_cdr']
                    ]);
                } else {
                    // Temporal: Forzar que nos muestre el error de SUNAT en pantalla
                    die("<h2 style='color:red;'>❌ ERROR DE SUNAT: " . $resultadoSunat['mensaje'] . "</h2>");
                }

            } catch (\Exception $eFactura) {
                // Temporal: Forzar que nos muestre el error de código en pantalla
                die("<h2 style='color:red;'>❌ ERROR DE CÓDIGO/SERVIDOR: " . $eFactura->getMessage() . "</h2><br>Archivo: " . $eFactura->getFile() . " (Línea: " . $eFactura->getLine() . ")");
            }
            // =======================================================
            // FIN DE FACTURACIÓN ELECTRÓNICA
            // =======================================================

            header('Location: ' . BASE_URL . '/index.php?url=Reservation/index&success=checkout_ok');
            exit;

        } catch (\Exception $e) {
            $db->rollBack();
            error_log("Error Checkout: " . $e->getMessage());
            header('Location: ' . BASE_URL . '/index.php?url=Reservation/index&error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    // =========================================================
    // CAMBIAR ESTADO (Con lógica de Tiempos completa)
    // =========================================================
    public function changeStatus()
    {
        $this->authorizeAdmin();

        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? 'pendiente';

        $allowed = ['pendiente', 'confirmada', 'en_proceso', 'completada', 'cancelada'];

        if ($id && in_array($status, $allowed)) {

            $db = \Core\Database::getInstance();

            // Lógica según el estado al que vamos
            switch ($status) {
                case 'confirmada':
                    // Confirmamos la cita
                    $sql = "UPDATE reserva SET estado = 'confirmada', confirmado_en = NOW() WHERE id = :id";
                    break;

                case 'en_proceso':
                    // El cliente se sentó en la silla
                    $sql = "UPDATE reserva SET estado = 'en_proceso', iniciado_en = NOW() WHERE id = :id";
                    break;

                case 'cancelada':
                    // El cliente canceló (o nosotros cancelamos)
                    $sql = "UPDATE reserva SET estado = 'cancelada', cancelado_en = NOW() WHERE id = :id";
                    break;

                case 'pendiente':
                case 'reactivada': // Si usas un botón específico para reactivar
                    // Reactivación: Volver a pendiente desde cancelada
                    // Nota: 'reactivado_en' marca cuándo se rescató la cita.
                    // Opcional: Podrías querer limpiar 'cancelado_en' con NULL si quieres borrar el rastro, 
                    // pero mejor déjalo para saber que fue cancelada antes.
                    $sql = "UPDATE reserva SET estado = 'pendiente', reactivado_en = NOW() WHERE id = :id";
                    break;

                default:
                    // Cualquier otro cambio simple
                    $sql = "UPDATE reserva SET estado = :st WHERE id = :id";
                    // Para este default, necesitamos pasar el :st en el execute
                    $stmt = $db->prepare($sql);
                    $stmt->execute(['st' => $status, 'id' => $id]);
                    // Salimos aquí para no ejecutar el execute de abajo que no lleva :st
                    header('Location: ' . BASE_URL . '/index.php?url=Reservation/index');
                    exit;
            }

            // Ejecutar la consulta preparada arriba (para los casos que no son default)
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $id]);
        }

        header('Location: ' . BASE_URL . '/index.php?url=Reservation/index');
        exit;
    }

    // Metodos standard sin cambios mayores...
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

    public function my()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/index.php?url=Auth/showLogin');
            exit;
        }
        $userId = $_SESSION['user']['id'];
        $reservas = Reserva::getByUser($userId);
        $this->view('client/reservations/my', compact('reservas'));
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
        if (!$reserva)
            die("Reserva no encontrada.");

        $servicios = Reserva::getServiciosPorReserva($id);
        $productos = Reserva::getProductosPorReserva($id);

        require_once __DIR__ . '/../views/admin/reservations/ticket.php';
    }

    // =========================================================
    // HELPER: Calcular Total Servicios
    // =========================================================
    private function calcularTotalServicios($serviciosIds = [])
    {
        $total = 0;
        if (!empty($serviciosIds)) {
            $db = \Core\Database::getInstance();
            // Sanitizamos IDs a enteros para evitar inyeccion en IN()
            $ids = implode(',', array_map('intval', $serviciosIds));

            if (!empty($ids)) {
                $stmt = $db->query("SELECT SUM(precio) as total FROM servicio WHERE id IN ($ids)");
                $res = $stmt->fetch(\PDO::FETCH_OBJ);
                $total = $res->total ?? 0;
            }
        }
        return $total;
    }
}