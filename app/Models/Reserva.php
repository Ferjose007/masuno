<?php

namespace App\Models;

use Core\Database;
use PDO;

class Reserva
{
    public $id;
    public $usuario_id;
    // public $estilista_id; <- Ya no usaremos este campo principal
    // public $servicio_id;  <- Tampoco este
    public $horario_id;
    public $fecha_cita;
    public $hora_cita;
    public $estado;
    public $notas;
    public $creado_en;

    // Propiedades virtuales
    public $cliente_nombre;
    public $cliente_email;
    public $servicio_nombre;
    public $servicio_precio;
    public $servicio_duracion;
    public $estilistas_nombres; // NUEVO: Para guardar varios estilistas

    public static function all()
    {
        $db = Database::getInstance();

        // NUEVO: La consulta ahora busca a los estilistas en la tabla reserva_servicio
        $sql = "SELECT 
                r.*, 
                r.usuario_id AS cliente_id,  
                c.nombre AS cliente_nombre, 
                
                -- MAGIA AQUÍ: Concatenamos los servicios y también los estilistas involucrados
                GROUP_CONCAT(DISTINCT s.nombre SEPARATOR ', ') AS servicios_nombres,
                GROUP_CONCAT(DISTINCT s.id) AS servicios_ids,
                
                -- Extraemos los estilistas de la tabla pivote (Si es null, pone 'Sin asignar')
                GROUP_CONCAT(DISTINCT COALESCE(e.nombre, 'Sin asignar') SEPARATOR ', ') AS estilistas_nombres,
                
                SUM(s.precio) AS precio_total_estimado
                
            FROM reserva r
            LEFT JOIN usuario c ON r.usuario_id = c.id
            
            -- JOIN a la tabla intermedia
            LEFT JOIN reserva_servicio rs ON r.id = rs.reserva_id
            -- JOIN a servicios
            LEFT JOIN servicio s ON rs.servicio_id = s.id
            -- NUEVO: JOIN a los estilistas desde la tabla pivote
            LEFT JOIN usuario e ON rs.estilista_id = e.id
            
            GROUP BY r.id
            ORDER BY r.fecha_cita DESC, r.hora_cita DESC";

        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public static function find($id)
    {
        $db = Database::getInstance();
        // NOTA: Esta función base se mantiene sencilla, la magia pesada la hace getByIdWithDetails
        $sql = "SELECT r.*, u.nombre as cliente_nombre 
                FROM reserva r
                JOIN usuario u ON r.usuario_id = u.id
                WHERE r.id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $stmt->fetch();
    }

    public static function create(array $data)
    {
        $db = Database::getInstance();
        // NUEVO: Quitamos servicio_id y estilista_id de la inserción principal.
        // Ahora devuelve el ID insertado para que el Controlador guarde los detalles
        $stmt = $db->prepare("
            INSERT INTO reserva (usuario_id, horario_id, fecha_cita, hora_cita, notas, estado, creado_en)
            VALUES (:cliente, NULL, :fecha, :hora, :notas, 'pendiente', NOW())
        ");

        $exito = $stmt->execute([
            'cliente' => $data['usuario_id'],
            'fecha' => $data['fecha_cita'],
            'hora' => $data['hora_cita'],
            'notas' => $data['notas'] ?? null
        ]);

        if ($exito) {
            return $db->lastInsertId(); // Devolvemos el ID de la reserva creada
        }
        return false;
    }

    public function update($data)
    {
        $db = Database::getInstance();
        $usuario_id = $data['usuario_id'] ?? $data['cliente_id'];

        // NUEVO: Solo actualizamos los datos generales de la reserva
        $sql = "UPDATE reserva SET 
                    usuario_id = :usuario_id,
                    fecha_cita = :fecha_cita,
                    hora_cita = :hora_cita,
                    notas = :notas
                WHERE id = :id";

        $stmt = $db->prepare($sql);

        return $stmt->execute([
            'usuario_id' => $usuario_id,
            'fecha_cita' => $data['fecha_cita'],
            'hora_cita' => $data['hora_cita'],
            'notas' => $data['notas'] ?? '',
            'id' => $data['id']
        ]);
    }

    // ... (Mantengo updateStatus, changeStatus, delete, countToday, sumDailyRevenue iguales) ...
    public function changeStatus($nuevoEstado)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE reserva SET estado = :estado WHERE id = :id");
        return $stmt->execute(['estado' => $nuevoEstado, 'id' => $this->id]);
    }

    public function updateStatus($status)
    {
        $db = Database::getInstance();
        $sql = "UPDATE reserva SET estado = :estado WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['estado' => $status, 'id' => $this->id]);
        $this->estado = $status;
    }

    public function delete()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM reserva WHERE id = :id");
        return $stmt->execute(['id' => $this->id]);
    }

    public static function countToday()
    {
        $db = Database::getInstance();
        $today = date('Y-m-d');
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM reserva WHERE fecha_cita = :today AND estado != 'cancelada'");
        $stmt->execute([':today' => $today]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['total'] ?? 0;
    }

    public static function sumDailyRevenue()
    {
        $db = Database::getInstance();
        $today = date('Y-m-d');
        $sql = "SELECT SUM(s.precio) as revenue 
                FROM reserva r 
                JOIN reserva_servicio rs ON r.id = rs.reserva_id
                JOIN servicio s ON rs.servicio_id = s.id
                WHERE r.fecha_cita = :today AND r.estado = 'completada'";
        $stmt = $db->prepare($sql);
        $stmt->execute([':today' => $today]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['revenue'] ?? 0;
    }

    public static function getUpcoming($limit = 5)
    {
        $db = Database::getInstance();
        $today = date('Y-m-d');
        // Usamos GROUP_CONCAT también aquí por si hay múltiples servicios
        $sql = "SELECT r.id, r.estado, u.nombre as cliente_nombre, r.fecha_cita, r.hora_cita,
                       GROUP_CONCAT(s.nombre SEPARATOR ', ') as servicio_nombre
                FROM reserva r 
                JOIN usuario u ON r.usuario_id = u.id 
                LEFT JOIN reserva_servicio rs ON r.id = rs.reserva_id
                LEFT JOIN servicio s ON rs.servicio_id = s.id 
                WHERE r.fecha_cita >= :today AND r.estado != 'cancelada'
                GROUP BY r.id
                ORDER BY r.fecha_cita ASC, r.hora_cita ASC 
                LIMIT " . (int) $limit;
        $stmt = $db->prepare($sql);
        $stmt->execute([':today' => $today]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function getByUser($userId)
    {
        $db = Database::getInstance();
        $sql = "SELECT r.*, 
                       GROUP_CONCAT(s.nombre SEPARATOR ', ') as servicio_nombre, 
                       SUM(s.precio) as servicio_precio
                FROM reserva r
                LEFT JOIN reserva_servicio rs ON r.id = rs.reserva_id
                LEFT JOIN servicio s ON rs.servicio_id = s.id
                WHERE r.usuario_id = :id
                GROUP BY r.id
                ORDER BY r.fecha_cita DESC, r.hora_cita DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public function guardarProductos($productos)
    {
        $db = Database::getInstance();
        foreach ($productos as $prod) {
            $sql = "INSERT INTO reserva_producto (reserva_id, producto_id, cantidad, precio_unitario) 
                    VALUES (:reserva_id, :producto_id, :cantidad, :precio)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'reserva_id' => $this->id,
                'producto_id' => $prod['id'],
                'cantidad' => 1,
                'precio' => $prod['precio']
            ]);
            $sqlStock = "UPDATE producto SET stock = stock - 1 WHERE id = :id";
            $stmtStock = $db->prepare($sqlStock);
            $stmtStock->execute(['id' => $prod['id']]);
        }
    }

    public static function getProductosPorReserva($id)
    {
        $db = Database::getInstance();
        $sql = "SELECT rp.*, p.nombre 
                FROM reserva_producto rp
                JOIN producto p ON rp.producto_id = p.id
                WHERE rp.reserva_id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    // ========================================================
    // NUEVO: FUNCIONES CLAVES PARA EL NUEVO FLUJO DE SERVICIOS
    // ========================================================

    // Ahora recupera el servicio Y QUIÉN lo realizó
    public static function getServiciosPorReserva($id)
    {
        $db = Database::getInstance();
        $sql = "SELECT s.id, s.nombre, s.precio, 
                       rs.estilista_id, e.nombre AS estilista_nombre 
                FROM reserva_servicio rs
                JOIN servicio s ON rs.servicio_id = s.id
                LEFT JOIN usuario e ON rs.estilista_id = e.id
                WHERE rs.reserva_id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    // NUEVO: Función para que el controlador asigne servicios a una reserva
    public static function syncServicios($reserva_id, $servicios)
    {
        $db = Database::getInstance();

        // Primero, limpiamos los servicios actuales para esta reserva (útil al editar)
        $db->prepare("DELETE FROM reserva_servicio WHERE reserva_id = ?")->execute([$reserva_id]);

        $sql = "INSERT INTO reserva_servicio (reserva_id, servicio_id, estilista_id) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);

        // $servicios debe ser un array como: [['servicio_id' => 1, 'estilista_id' => 2], ...]
        foreach ($servicios as $item) {
            $est_id = !empty($item['estilista_id']) ? $item['estilista_id'] : null;
            $stmt->execute([$reserva_id, $item['servicio_id'], $est_id]);
        }
    }

    public static function getByIdWithDetails($id)
    {
        $db = Database::getInstance();
        $sql = "SELECT r.*, 
                       c.nombre AS cliente_nombre, 
                       c.email AS cliente_email
                FROM reserva r
                LEFT JOIN usuario c ON r.usuario_id = c.id
                WHERE r.id = :id";

        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }
}