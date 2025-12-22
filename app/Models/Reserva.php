<?php

namespace App\Models;

use Core\Database;
use PDO;

class Reserva
{
    public $id;
    public $usuario_id;
    public $servicio_id;
    public $horario_id; // Ahora puede ser null
    public $fecha_cita;
    public $hora_cita;
    public $estado;
    public $notas;
    public $creado_en;

    // Propiedades virtuales (para mostrar nombres en la lista)
    public $cliente_nombre;
    public $cliente_email;
    public $servicio_nombre;
    public $servicio_precio;
    public $servicio_duracion;

    public static function all()
    {
        $db = Database::getInstance();
        // JOIN simplificado: Ya tenemos fecha y hora en la tabla reserva
        $sql = "SELECT r.*, 
                       u.nombre as cliente_nombre, 
                       u.email as cliente_email,
                       s.nombre as servicio_nombre,
                       s.precio as servicio_precio,
                       s.duracion_minutes as servicio_duracion
                FROM reserva r
                JOIN usuario u ON r.usuario_id = u.id
                JOIN servicio s ON r.servicio_id = s.id
                ORDER BY r.fecha_cita DESC, r.hora_cita DESC";

        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function find($id)
    {
        $db = Database::getInstance();
        $sql = "SELECT r.*, u.nombre as cliente_nombre, s.nombre as servicio_nombre 
                FROM reserva r
                JOIN usuario u ON r.usuario_id = u.id
                JOIN servicio s ON r.servicio_id = s.id
                WHERE r.id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $stmt->fetch();
    }

    public static function create(array $data)
    {
        $db = Database::getInstance();
        // Nota: Insertamos horario_id como NULL si no viene definido
        $stmt = $db->prepare("
            INSERT INTO reserva (usuario_id, servicio_id, horario_id, fecha_cita, hora_cita, notas, estado, creado_en)
            VALUES (:cliente, :servicio, NULL, :fecha, :hora, :notas, 'pendiente', NOW())
        ");
        return $stmt->execute([
            'cliente'  => $data['usuario_id'],
            'servicio' => $data['servicio_id'],
            'fecha'    => $data['fecha_cita'],
            'hora'     => $data['hora_cita'],
            'notas'    => $data['notas'] ?? null
        ]);
    }

    public function update(array $data)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE reserva 
            SET fecha_cita = :fecha, hora_cita = :hora, notas = :notas, servicio_id = :servicio, usuario_id = :cliente
            WHERE id = :id
        ");
        return $stmt->execute([
            'fecha'    => $data['fecha_cita'],
            'hora'     => $data['hora_cita'],
            'notas'    => $data['notas'] ?? null,
            'servicio' => $data['servicio_id'],
            'cliente'  => $data['usuario_id'],
            'id'       => $this->id
        ]);
    }

    public function changeStatus($nuevoEstado)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE reserva SET estado = :estado WHERE id = :id");
        return $stmt->execute(['estado' => $nuevoEstado, 'id' => $this->id]);
    }

    public function delete()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM reserva WHERE id = :id");
        return $stmt->execute(['id' => $this->id]);
    }

    // Métodos para estadísticas (Dashboard) corregidos
    public static function countToday()
    {
        $db = Database::getInstance();
        $today = date('Y-m-d');
        // Ahora usamos fecha_cita directo de la tabla reserva
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM reserva WHERE fecha_cita = :today AND estado != 'cancelada'");
        $stmt->execute([':today' => $today]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['total'] ?? 0;
    }

    // Calcular ingresos SOLO de HOY y SOLO de citas COMPLETADAS
    public static function sumDailyRevenue()
    {
        $db = Database::getInstance();
        $today = date('Y-m-d');

        $sql = "SELECT SUM(s.precio) as revenue 
                FROM reserva r 
                JOIN servicio s ON r.servicio_id = s.id
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

        // CORRECCIÓN: Quitamos los alias "as fecha" y "as hora_inicio"
        // Ahora usamos los nombres reales de la tabla y la clase.
        $sql = "SELECT r.id, r.estado, u.nombre as cliente_nombre, s.nombre as servicio_nombre, r.fecha_cita, r.hora_cita
                FROM reserva r 
                JOIN usuario u ON r.usuario_id = u.id 
                JOIN servicio s ON r.servicio_id = s.id 
                WHERE r.fecha_cita >= :today AND r.estado != 'cancelada'
                ORDER BY r.fecha_cita ASC, r.hora_cita ASC 
                LIMIT " . (int)$limit;

        $stmt = $db->prepare($sql);
        $stmt->execute([':today' => $today]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    // Obtener reservas de un usuario específico (Para el panel del cliente)
    public static function getByUser($userId)
    {
        $db = Database::getInstance();
        $sql = "SELECT r.*, s.nombre as servicio_nombre, s.precio as servicio_precio, s.duracion_minutes 
                FROM reserva r
                JOIN servicio s ON r.servicio_id = s.id
                WHERE r.usuario_id = :id
                ORDER BY r.fecha_cita DESC, r.hora_cita DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    // En app/Models/Reserva.php

    // Función para agregar productos a la venta
    public function guardarProductos($productos)
    {
        $db = Database::getInstance();

        foreach ($productos as $prod) {
            // 1. Insertar en tabla intermedia
            $sql = "INSERT INTO reserva_producto (reserva_id, producto_id, cantidad, precio_unitario) 
                VALUES (:reserva_id, :producto_id, :cantidad, :precio)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'reserva_id' => $this->id,
                'producto_id' => $prod['id'],
                'cantidad' => $prod['cantidad'],
                'precio' => $prod['precio']
            ]);

            // 2. Descontar Stock del inventario
            $sqlStock = "UPDATE producto SET stock = stock - :cantidad WHERE id = :id";
            $stmtStock = $db->prepare($sqlStock);
            $stmtStock->execute([
                'cantidad' => $prod['cantidad'],
                'id' => $prod['id']
            ]);
        }
    }

    // Función para obtener el detalle de productos de una reserva (Para la boleta)
    public function getProductos()
    {
        $db = Database::getInstance();
        $sql = "SELECT rp.*, p.nombre 
            FROM reserva_producto rp
            JOIN producto p ON rp.producto_id = p.id
            WHERE rp.reserva_id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $this->id]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
