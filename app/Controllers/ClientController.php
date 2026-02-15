<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\User;
use Exception;

class ClientController extends Controller
{
    // 1. Listar Clientes (Vista Única con Modales)
    public function index()
    {
        $this->authorizeAdmin(); // Seguridad (heredada de Core\Controller)

        $clientes = User::getAllClients();

        // Carga la vista que contiene la tabla y todos los modales
        $this->view('admin/clients/index', compact('clientes'));
    }

    // 2. Guardar Nuevo Cliente
    public function store()
    {
        $this->authorizeAdmin();

        // Validar campos obligatorios
        if (!empty($_POST['nombre']) && !empty($_POST['email']) && !empty($_POST['password'])) {
            $data = $_POST;

            // Seguridad: Encriptar contraseña
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);

            // Forzar rol de cliente
            $data['rol'] = 'cliente';

            // Verificar duplicados
            if (\App\Models\User::findByEmail($data['email'])) {
                header('Location: ' . BASE_URL . '/index.php?url=Client/index&error=email_exists');
                exit;
            }

            // --- NUEVO: SUBIR FOTO ---
            // Intentamos subir la foto. Si devuelve un nombre, lo agregamos al array.
            $nombreFoto = $this->subirFoto();
            if ($nombreFoto) {
                $data['foto'] = $nombreFoto;
            }
            // -------------------------

            \App\Models\User::create($data);
        }

        header('Location: ' . BASE_URL . '/index.php?url=Client/index');
        exit;
    }

    // 3. Actualizar Cliente
    public function update()
    {
        $this->authorizeAdmin();

        $id = $_POST['id'] ?? null;
        $user = \App\Models\User::find((int) $id);

        if ($user) {
            $data = $_POST;

            // Lógica de Contraseña
            if (!empty($_POST['password'])) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            } else {
                unset($data['password']);
            }

            // --- NUEVO: SUBIR FOTO AL EDITAR ---
            // Solo si el usuario subió una nueva, la procesamos.
            $nombreFoto = $this->subirFoto();
            if ($nombreFoto) {
                $data['foto'] = $nombreFoto;
                // Nota: Aquí no borramos la anterior del disco para no complicarnos,
                // pero la base de datos se actualizará con la nueva.
            }
            // ------------------------------------

            $user->update($data);
        }

        header('Location: ' . BASE_URL . '/index.php?url=Client/index');
        exit;
    }

    // 4. Eliminar Cliente (Borrado Permanente)
    public function delete()
    {
        $this->authorizeAdmin();

        $id = $_GET['id'] ?? null;
        $user = User::find((int) $id);

        if ($user) {
            $user->delete();
        }

        header('Location: ' . BASE_URL . '/index.php?url=Client/index');
        exit;
    }

    // 5. Anular / Activar Cliente (Soft Delete / Toggle)
    public function toggle()
    {
        $this->authorizeAdmin();

        $id = $_GET['id'] ?? null;
        $user = User::find((int) $id);

        if ($user) {
            // Llama al método del modelo que cambia 1 a 0 y viceversa
            $user->toggleStatus();
        }

        header('Location: ' . BASE_URL . '/index.php?url=Client/index');
        exit;
    }

    /**
     * Función auxiliar para subir la foto al servidor
     * Retorna el nombre del archivo generado o NULL si no se subió nada
     */
    private function subirFoto()
    {
        // 1. Verificamos si se envió un archivo y si no hubo errores técnicos
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {

            // Datos del archivo
            $fileTmpPath = $_FILES['foto']['tmp_name'];
            $fileName = $_FILES['foto']['name'];
            $fileType = $_FILES['foto']['type'];

            // 2. Extraer extensión (jpg, png, etc)
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // 3. Extensiones permitidas (Seguridad)
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp');

            if (in_array($fileExtension, $allowedfileExtensions)) {
                // 4. Crear nombre único para evitar que se sobrescriban
                // Ejemplo: cliente_65a8f9_foto.jpg
                $newFileName = 'cliente_' . md5(time() . $fileName) . '.' . $fileExtension;

                // 5. Definir carpeta de destino
                // __DIR__ es la carpeta actual (Controllers), subimos 2 niveles y entramos a public
                $uploadFileDir = __DIR__ . '/../../public/uploads/users/';

                // Crear carpeta si no existe
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                // 6. MOVER EL ARCHIVO
                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    return $newFileName; // ¡Éxito! Retornamos el nombre
                }
            }
        }
        return null; // Si no subió nada o falló algo
    }

    // En ClientController.php

    public function history()
    {
        header('Content-Type: application/json');
        try {
            $id = $_GET['id'] ?? null;
            if (!$id)
                throw new Exception("ID requerido");

            $db = \Core\Database::getInstance();

            $sql = "SELECT 
                        r.id, r.fecha_cita, r.hora_cita, r.estado, r.notas, r.precio_final,
                        -- Todas las fechas importantes
                        r.creado_en, r.confirmado_en, r.iniciado_en, r.finalizado_en, r.cancelado_en, r.reactivado_en,
                        
                        u.nombre as estilista,
                        GROUP_CONCAT(DISTINCT s.nombre SEPARATOR ', ') as servicios_nombres,
                        GROUP_CONCAT(DISTINCT CONCAT(p.nombre, ' (x', rp.cantidad, ')') SEPARATOR ', ') as productos_nombres,
                        
                        (COALESCE(SUM(DISTINCT rs.precio_momento), 0) + COALESCE(SUM(DISTINCT rp.precio_unitario * rp.cantidad), 0)) as total_calculado

                    FROM reserva r
                    LEFT JOIN usuario u ON r.estilista_id = u.id
                    LEFT JOIN reserva_servicio rs ON r.id = rs.reserva_id
                    LEFT JOIN servicio s ON rs.servicio_id = s.id
                    LEFT JOIN reserva_producto rp ON r.id = rp.reserva_id
                    LEFT JOIN producto p ON rp.producto_id = p.id
                    
                    WHERE r.usuario_id = :id
                    GROUP BY r.id
                    ORDER BY r.fecha_cita DESC, r.hora_cita DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => $id]);
            echo json_encode($stmt->fetchAll(\PDO::FETCH_ASSOC));
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}