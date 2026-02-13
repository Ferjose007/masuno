<?php
namespace App\Models;

use Core\Database;
use PDO;

class Producto
{
    public $id;
    public $nombre;
    public $descripcion;
    public $precio;
    public $stock;
    public $activo;
    public $creado_en;
    public $actualizado_en;

    // 1. Obtener todos (Admin)
    public static function all()
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM producto ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    // 2. Obtener solo activos (Para la venta)
    public static function getActive()
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM producto WHERE activo = 1 AND stock > 0"); // O sin stock > 0 si quieres ver los agotados
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // 3. Buscar por ID
    public static function find($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM producto WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchObject(self::class);
    }

    // 4. Crear
    public static function create($data)
    {
        $db = Database::getInstance();
        $sql = "INSERT INTO producto (nombre, descripcion, precio, stock) VALUES (:nombre, :descripcion, :precio, :stock)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? '',
            'precio' => $data['precio'],
            'stock' => $data['stock']
        ]);
    }

    // 5. Actualizar
    public function update($data)
    {
        $db = Database::getInstance();
        $sql = "UPDATE producto SET nombre = :nombre, descripcion = :descripcion, precio = :precio, stock = :stock WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? '',
            'precio' => $data['precio'],
            'stock' => $data['stock'],
            'id' => $this->id
        ]);
    }

    // 6. Eliminar (Soft Delete o Hard Delete según prefieras, aquí usaremos Hard Delete simple)
    public function delete()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM producto WHERE id = :id");
        return $stmt->execute(['id' => $this->id]);
    }

    // 7. Toggle Estado (Activo/Inactivo)
    public function toggleStatus()
    {
        $db = Database::getInstance();
        $nuevo = $this->activo == 1 ? 0 : 1;
        $stmt = $db->prepare("UPDATE producto SET activo = :activo WHERE id = :id");
        return $stmt->execute(['activo' => $nuevo, 'id' => $this->id]);
    }
}