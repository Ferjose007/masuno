<?php
namespace App\Models;

use Core\Database;
use PDO;

class EstilistaServicio
{
    public static function assignServices(int $stylistId, array $serviceIds): bool
    {
        $db = Database::getInstance();
        // Borrar asignaciones previas
        $db->prepare("DELETE FROM estilista_servicio WHERE estilista_id = :eid")
           ->execute(['eid' => $stylistId]);

        // Asignar de nuevo
        $stmt = $db->prepare("
            INSERT INTO estilista_servicio (estilista_id, servicio_id)
            VALUES (:eid, :sid)
        ");
        foreach ($serviceIds as $sid) {
            $stmt->execute(['eid' => $stylistId, 'sid' => $sid]);
        }
        return true;
    }

    public static function getServicesForStylist(int $stylistId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT s.id, s.nombre 
              FROM servicio s
              JOIN estilista_servicio es 
                ON s.id = es.servicio_id
             WHERE es.estilista_id = :eid
        ");
        $stmt->execute(['eid' => $stylistId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
