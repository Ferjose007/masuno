<?php
namespace App\Models;

use Core\Database;
use PDO;

class Servicio
{
    public $id;
    public $nombre;
    public $duracion_minutes;
    public $precio;
    public $creado_en;

    public static function all(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM servicio ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function find(int $id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM servicio WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $stmt->fetch();
    }

    public static function create(array $data): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO servicio (nombre, duracion_minutes, precio)
            VALUES (:nombre, :duracion, :precio)
        ");
        return $stmt->execute([
            'nombre'   => $data['nombre'],
            'duracion' => $data['duracion_minutes'],
            'precio'   => $data['precio']
        ]);
    }

    public function update(array $data): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE servicio
               SET nombre = :nombre,
                   duracion_minutes = :duracion,
                   precio = :precio
             WHERE id = :id
        ");
        return $stmt->execute([
            'nombre'   => $data['nombre'],
            'duracion' => $data['duracion_minutes'],
            'precio'   => $data['precio'],
            'id'       => $this->id
        ]);
    }

    public function delete(): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM servicio WHERE id = :id");
        return $stmt->execute(['id' => $this->id]);
    }
}
