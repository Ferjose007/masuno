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
    public $rol;
    public $creado_en;

    // Buscar usuario por ID
    public static function findById(int $id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM usuario WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $stmt->fetch();
    }

    // Buscar usuario por email
    public static function findByEmail(string $email)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM usuario WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $stmt->fetch();
    }

    // Obtener todos los usuarios con rol 'cliente'
    public static function getAllClients(): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM usuario WHERE rol = 'cliente' ORDER BY nombre");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    // Obtener todos los usuarios con rol 'Estilista'
    public static function getAllStylists(): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT * 
              FROM usuario 
             WHERE rol = 'estilista' 
             ORDER BY nombre
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    // Crear usuario (cliente/estilista/admin)
    public static function create(array $data)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO usuario (nombre, email, password, rol)
            VALUES (:nombre, :email, :password, :rol)
        ");
        return $stmt->execute([
            'nombre'   => $data['nombre'],
            'email'    => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'rol'      => $data['rol'] ?? 'cliente'
        ]);
    }

    // Verificar contraseña
    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password);
    }
}
