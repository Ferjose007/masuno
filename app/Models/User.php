<?php
namespace App\Models;

use Core\Database;
use PDO;

class User
{
    public $id;
    public $nombre;
    public $email;
    public $password;
    public $telefono;
    public $rol;
    public $creado_en;
    public $actualizado_en;
    public $activo; // Estado 1 o 0

    // 1. Obtener todos los clientes (rol = 'cliente')
    public static function getAllClients()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM usuario WHERE rol = 'cliente' ORDER BY creado_en DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    // 2. Buscar por ID
    public static function find($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM usuario WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $stmt->fetch();
    }

    // 3. Buscar por Email (ESTE ERA EL QUE FALTABA)
    public static function findByEmail($email)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM usuario WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $stmt->fetch();
    }

    // 4. Crear Usuario
    public static function create(array $data)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO usuario (nombre, email, password, telefono, rol, creado_en, activo)
            VALUES (:nombre, :email, :password, :telefono, :rol, NOW(), 1)
        ");
        return $stmt->execute([
            'nombre'   => $data['nombre'],
            'email'    => $data['email'],
            'password' => $data['password'],
            'telefono' => $data['telefono'] ?? null,
            'rol'      => $data['rol']
        ]);
    }

    // 5. Actualizar Usuario
    public function update(array $data)
    {
        $db = Database::getInstance();
        
        $fields = [];
        $params = ['id' => $this->id];

        if (!empty($data['nombre'])) {
            $fields[] = "nombre = :nombre";
            $params['nombre'] = $data['nombre'];
        }
        if (!empty($data['email'])) {
            $fields[] = "email = :email";
            $params['email'] = $data['email'];
        }
        if (isset($data['telefono'])) {
            $fields[] = "telefono = :telefono";
            $params['telefono'] = $data['telefono'];
        }
        if (!empty($data['password'])) {
            $fields[] = "password = :password";
            $params['password'] = $data['password'];
        }
        
        // Siempre actualizamos la fecha de modificación
        $fields[] = "actualizado_en = NOW()";

        if (empty($fields)) return false;

        $sql = "UPDATE usuario SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    // 6. Eliminar Usuario
    public function delete()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM usuario WHERE id = :id");
        return $stmt->execute(['id' => $this->id]);
    }

    // 7. Cambiar Estado (Activar/Anular)
    public function toggleStatus()
    {
        $db = Database::getInstance();
        $nuevoEstado = $this->activo == 1 ? 0 : 1;
        
        $stmt = $db->prepare("UPDATE usuario SET activo = :activo, actualizado_en = NOW() WHERE id = :id");
        return $stmt->execute([
            'activo' => $nuevoEstado,
            'id'     => $this->id
        ]);
    }
    
    // 8. Contar por roles (Para el Dashboard)
    public static function countByRole($role) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM usuario WHERE rol = :rol");
        $stmt->execute([':rol' => $role]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['total'] ?? 0;
    }
}