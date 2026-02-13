<?php

namespace App\Models;

use Core\Database;
use PDO;

class Reserva
{
    public $id;
    public $usuario_id;
    public $estilista_id;
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

        // ATENCIÓN AL SQL: Usamos GROUP_CONCAT
        $sql = "SELECT 
                r.*, 
                
                -- Alias para JS
                r.usuario_id AS cliente_id,  
                
                -- Datos Clientes y Estilistas
                c.nombre AS cliente_nombre, 
                e.nombre AS estilista_nombre,
                
                -- MAGIA AQUÍ: Concatenamos todos los servicios de esta reserva
                -- Resultado ejemplo: 'Corte, Barba, Masaje'
                GROUP_CONCAT(s.nombre SEPARATOR ', ') AS servicios_nombres,
                
                -- También necesitamos los IDs para el Modal de Editar
                -- Resultado ejemplo: '1,5,8'
                GROUP_CONCAT(s.id) AS servicios_ids,
                
                -- Sumamos el precio total de todos los servicios
                SUM(s.precio) AS precio_total_estimado
                
            FROM reserva r
            LEFT JOIN usuario c ON r.usuario_id = c.id
            LEFT JOIN usuario e ON r.estilista_id = e.id
            
            -- JOIN a la tabla intermedia
            LEFT JOIN reserva_servicio rs ON r.id = rs.reserva_id
            -- JOIN a servicios
            LEFT JOIN servicio s ON rs.servicio_id = s.id
            
            -- AGRUPAR: Importante para que no salgan filas repetidas
            GROUP BY r.id
            
            ORDER BY r.fecha_cita DESC, r.hora_cita DESC";

        $stmt = $db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
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
            'cliente' => $data['usuario_id'],
            'servicio' => $data['servicio_id'],
            'fecha' => $data['fecha_cita'],
            'hora' => $data['hora_cita'],
            'notas' => $data['notas'] ?? null
        ]);
    }

    public function update($data)
    {
        $db = Database::getInstance();

        // 1. Mapeo inteligente: Si viene 'cliente_id', lo pasamos a 'usuario_id'
        $usuario_id = $data['usuario_id'] ?? $data['cliente_id'];

        // 2. Manejo de servicio: Si viene un array, tomamos el primero como principal
        // Esto evita el error "Column cannot be null"
        $servicio_id = $data['servicio_id'] ?? null;
        if (isset($data['servicios']) && is_array($data['servicios']) && count($data['servicios']) > 0) {
            $servicio_id = $data['servicios'][0];
        }

        $sql = "UPDATE reserva SET 
                    usuario_id = :usuario_id,
                    estilista_id = :estilista_id, 
                    servicio_id = :servicio_id,
                    fecha_cita = :fecha_cita,
                    hora_cita = :hora_cita,
                    notas = :notas
                WHERE id = :id";

        $stmt = $db->prepare($sql);

        // Ejecutamos pasando los valores limpios
        return $stmt->execute([
            'usuario_id' => $usuario_id,
            'estilista_id' => $data['estilista_id'], // Asegúrate que el form envíe esto
            'servicio_id' => $servicio_id,
            'fecha_cita' => $data['fecha_cita'],
            'hora_cita' => $data['hora_cita'],
            'notas' => $data['notas'] ?? '',
            'id' => $data['id']
        ]);
    }

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
        $stmt->execute([
            'estado' => $status,
            'id' => $this->id
        ]);

        // Actualizamos la propiedad del objeto actual también
        $this->estado = $status;
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
                LIMIT " . (int) $limit;

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

    // Función para agregar productos a la venta
    public function guardarProductos($productos)
    {
        $db = Database::getInstance();

        foreach ($productos as $prod) {
            // A. Insertar en tabla intermedia (reserva_producto)
            // Asegúrate de que esta tabla exista en tu BD
            $sql = "INSERT INTO reserva_producto (reserva_id, producto_id, cantidad, precio_unitario) 
                    VALUES (:reserva_id, :producto_id, :cantidad, :precio)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'reserva_id' => $this->id,
                'producto_id' => $prod['id'],
                'cantidad' => 1, // Por defecto 1 en este flujo simple
                'precio' => $prod['precio']
            ]);

            // B. Descontar Stock del inventario
            $sqlStock = "UPDATE producto SET stock = stock - 1 WHERE id = :id";
            $stmtStock = $db->prepare($sqlStock);
            $stmtStock->execute(['id' => $prod['id']]);
        }
    }

    // Función para obtener el detalle de productos de una reserva (Para la boleta)
    // CAMBIO: Ahora es 'public static' y pide el '$id'
    public static function getProductosPorReserva($id)
    {
        $db = Database::getInstance();
        // Hacemos JOIN para traer el nombre del producto
        $sql = "SELECT rp.*, p.nombre 
                FROM reserva_producto rp
                JOIN producto p ON rp.producto_id = p.id
                WHERE rp.reserva_id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    // CAMBIO: Ahora es 'public static' y pide el '$id' por parámetro
    public static function getServiciosPorReserva($id)
    {
        $db = Database::getInstance();
        $sql = "SELECT s.id, s.nombre, s.precio 
                FROM reserva_servicio rs
                JOIN servicio s ON rs.servicio_id = s.id
                WHERE rs.reserva_id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    // Obtener una reserva por ID con los nombres de Cliente y Estilista
    public static function getByIdWithDetails($id)
    {
        $db = Database::getInstance();
        $sql = "SELECT r.*, 
                       c.nombre AS cliente_nombre, 
                       c.email AS cliente_email,
                       e.nombre AS estilista_nombre
                FROM reserva r
                LEFT JOIN usuario c ON r.usuario_id = c.id
                LEFT JOIN usuario e ON r.estilista_id = e.id
                WHERE r.id = :id";

        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }
}

