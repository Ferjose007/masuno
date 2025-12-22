<?php
namespace App\Models;

use Core\Database;
use PDO;

class Servicio
{
    public $id;
    public $nombre;
    public $descripcion;
    public $duracion_minutes;
    public $precio;
    public $creado_en;
    public $activo; // <--- Nuevo campo

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
        // Agregamos 'activo' por defecto en 1
        $stmt = $db->prepare("
            INSERT INTO servicio (nombre, descripcion, duracion_minutes, precio, activo)
            VALUES (:nombre, :descripcion, :duracion, :precio, 1)
        ");
        return $stmt->execute([
            'nombre'      => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'duracion'    => $data['duracion_minutes'],
            'precio'      => $data['precio']
        ]);
    }

    public function update(array $data): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE servicio
               SET nombre = :nombre,
                   descripcion = :descripcion,
                   duracion_minutes = :duracion,
                   precio = :precio
             WHERE id = :id
        ");
        return $stmt->execute([
            'nombre'      => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'duracion'    => $data['duracion_minutes'],
            'precio'      => $data['precio'],
            'id'          => $this->id
        ]);
    }

    public function delete(): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM servicio WHERE id = :id");
        return $stmt->execute(['id' => $this->id]);
    }

    // Nuevo método: Toggle Activo/Inactivo
    public function toggleStatus()
    {
        $db = Database::getInstance();
        $nuevoEstado = $this->activo == 1 ? 0 : 1;
        
        $stmt = $db->prepare("UPDATE servicio SET activo = :activo WHERE id = :id");
        return $stmt->execute([
            'activo' => $nuevoEstado,
            'id'     => $this->id
        ]);
    }

    // Obtener SOLO los servicios activos (Para desplegables públicos o de reservas)
    public static function getActive()
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM servicio WHERE activo = 1 ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }
}