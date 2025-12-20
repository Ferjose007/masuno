<?php
namespace App\Models;

use Core\Database;
use PDO;

class Reserva
{
    // Propiedades de la tabla reserva
    public int    $id;
    public int    $usuario_id;
    public int    $servicio_id;
    public int    $horario_id;
    public string $estado;
    public string $creado_en;

    // Campos de JOIN / alias
    public string $cliente_nombre;
    public string $servicio_nombre;
    public string $fecha;
    public string $hora_inicio;
    public string $hora_fin;

    /**
     * Constructor: asigna sólo propiedades existentes (evita dinámicas)
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Inserta una nueva reserva
     */
    public static function create(array $data): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO reserva (usuario_id, servicio_id, horario_id)
            VALUES (:usuario_id, :servicio_id, :horario_id)
        ");
        return $stmt->execute([
            'usuario_id'  => $data['usuario_id'],
            'servicio_id' => $data['servicio_id'],
            'horario_id'  => $data['horario_id']
        ]);
    }

    /**
     * Obtiene todas las reservas de un cliente, con detalles
     */
    public static function findByUser(int $userId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT 
              r.id,
              r.estado,
              r.creado_en,
              s.nombre AS servicio_nombre,
              h.fecha,
              h.hora_inicio,
              h.hora_fin
            FROM reserva r
            JOIN servicio s  ON r.servicio_id = s.id
            JOIN horario  h  ON r.horario_id  = h.id
            WHERE r.usuario_id = :uid
            ORDER BY h.fecha, h.hora_inicio
        ");
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    /**
     * Obtiene todas las reservas con cliente, servicio y horario
     */
    public static function findAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT 
              r.id,
              r.usuario_id,
              r.servicio_id,
              r.horario_id,
              r.estado,
              r.creado_en,
              u.nombre AS cliente_nombre,
              s.nombre AS servicio_nombre,
              h.fecha,
              h.hora_inicio,
              h.hora_fin
            FROM reserva r
            JOIN usuario u   ON r.usuario_id  = u.id
            JOIN servicio s  ON r.servicio_id = s.id
            JOIN horario h   ON r.horario_id  = h.id
            ORDER BY h.fecha DESC, h.hora_inicio DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    // app/Models/Reserva.php

   // 1. Cuenta las citas de hoy (CORREGIDO: Usa JOIN con horario)
    public static function countToday() {
        $db = Database::getInstance();
        $today = date('Y-m-d');
        
        // ESTA ES LA CONSULTA CORRECTA:
        $sql = "SELECT COUNT(*) as total 
                FROM reserva r 
                JOIN horario h ON r.horario_id = h.id 
                WHERE h.fecha = :today AND r.estado != 'cancelada'";
                
        $stmt = $db->prepare($sql);
        $stmt->execute([':today' => $today]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // 2. Suma ingresos del mes (CORREGIDO: Usa JOIN con horario y servicio)
    public static function sumMonthlyRevenue() {
        $db = Database::getInstance();
        $month = date('m');
        $year = date('Y');
        
        $sql = "SELECT SUM(s.precio) as revenue 
                FROM reserva r 
                JOIN horario h ON r.horario_id = h.id 
                JOIN servicio s ON r.servicio_id = s.id
                WHERE MONTH(h.fecha) = :m AND YEAR(h.fecha) = :y AND r.estado != 'cancelada'";
                
        $stmt = $db->prepare($sql);
        $stmt->execute([':m' => $month, ':y' => $year]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['revenue'] ?? 0;
    }

    // 3. Próximas citas (CORREGIDO: Usa JOIN para traer nombres reales)
    public static function getUpcoming($limit = 5) {
        $db = Database::getInstance();
        $today = date('Y-m-d');
        
        $sql = "SELECT 
                    r.id, 
                    r.estado, 
                    u.nombre as cliente_nombre, 
                    s.nombre as servicio_nombre, 
                    h.fecha, 
                    h.hora_inicio
                FROM reserva r 
                JOIN usuario u ON r.usuario_id = u.id 
                JOIN servicio s ON r.servicio_id = s.id 
                JOIN horario h ON r.horario_id = h.id
                WHERE h.fecha >= :today AND r.estado != 'cancelada'
                ORDER BY h.fecha ASC, h.hora_inicio ASC 
                LIMIT " . (int)$limit;
        
        $stmt = $db->prepare($sql);
        $stmt->execute([':today' => $today]);
        
        // Importante: fetchAll devuelve instancias de la clase actual
        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::class);
    }
}
