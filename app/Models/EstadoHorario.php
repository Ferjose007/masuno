<?php
namespace App\Models;

use Core\Database;
use PDO;

class EstadoHorario
{
    public $id;
    public $descripcion;

    public static function all(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM estado_horario ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }
}
