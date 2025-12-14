<?php

namespace App\Models;

use Core\Database;
use PDO;

class Horario
{
    public $id;
    public $fecha;
    public $hora_inicio;
    public $hora_fin;
    public $estado;
    public $creado_en;
    
    public int $estilista_id;


    public static function all(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT h.*, e.descripcion AS estado_desc
              FROM horario h
              JOIN estado_horario e ON h.estado = e.id
             ORDER BY fecha, hora_inicio
        ");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public static function find(int $id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT * 
              FROM horario 
             WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $stmt->fetch();
    }

    public static function create(array $data): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO horario (fecha, hora_inicio, hora_fin, estado, estilista_id)
        VALUES (:fecha, :hora_inicio, :hora_fin, :estado, :estilista_id)
        ");
        return $stmt->execute([
            'fecha'       => $data['fecha'],
            'hora_inicio' => $data['hora_inicio'],
            'hora_fin'    => $data['hora_fin'],
            'estado'      => $data['estado'],
            'estilista_id'   => $data['estilista_id'],
        ]);
    }

    public function update(array $data): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
        UPDATE horario
           SET fecha = :fecha,
               hora_inicio = :hora_inicio,
               hora_fin = :hora_fin,
               estado = :estado,
               estilista_id = :estilista_id
         WHERE id = :id
    ");
        return $stmt->execute([
            'fecha'          => $data['fecha'],
            'hora_inicio'    => $data['hora_inicio'],
            'hora_fin'       => $data['hora_fin'],
            'estado'         => $data['estado'],
            'estilista_id'   => $data['estilista_id'],   // ← aquí
            'id'             => $this->id,
        ]);
    }

    public function delete(): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM horario WHERE id = :id");
        return $stmt->execute(['id' => $this->id]);
    }
}
