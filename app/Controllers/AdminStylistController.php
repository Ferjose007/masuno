<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\User;
use App\Models\Servicio; // <--- Importante

class AdminStylistController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();
        $estilistas = User::getAllStylists();

        // 1. Cargamos servicios para el formulario
        $servicios = Servicio::all();

        // 2. Para cada estilista, cargamos sus servicios actuales (para pintar el checklist al editar)
        // Esto es un pequeño truco para enviarlo ya listo a la vista
        foreach ($estilistas as $estilista) {
            $estilista->mis_servicios = User::getServiceIds($estilista->id); // Array [1, 2]
            $estilista->lista_servicios = User::getServicesObj($estilista->id); // Objetos para ver detalles
        }

        $this->view('admin/stylists/index', compact('estilistas', 'servicios'));
    }

    public function store()
    {
        $this->authorizeAdmin();

        if (!empty($_POST['nombre']) && !empty($_POST['email'])) {
            $data = $_POST;
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $data['rol'] = 'estilista'; // Rol fijo para este controlador
            $data['activo'] = 1; // Aseguramos que nazca activo

            // 1. Verificar duplicados antes de subir nada
            if (User::findByEmail($data['email'])) {
                header('Location: ' . BASE_URL . '/index.php?url=AdminStylist/index&error=email_exists');
                exit;
            }

            // 2. --- LOGICA FOTO (NUEVO) ---
            // Intentamos subir la foto. Si devuelve nombre, lo metemos al array $data
            $nombreFoto = $this->subirFoto();
            if ($nombreFoto) {
                $data['foto'] = $nombreFoto;
            }
            // -----------------------------

            // 3. Crear el usuario (Ahora $data ya lleva la foto si se subió)
            // Nota: Si tu User::create retorna el ID, úsalo directo.
            // Si no, mantenemos tu lógica de buscarlo después.
            $newId = User::create($data);

            // 4. Guardar Servicios
            // Si tu create devuelve el ID, usa $newId. Si no, usa tu búsqueda por email.
            $userId = $newId ?: User::findByEmail($data['email'])->id;

            if (isset($_POST['servicios']) && is_array($_POST['servicios'])) {
                User::syncServices($userId, $_POST['servicios']);
            }
        }

        header('Location: ' . BASE_URL . '/index.php?url=AdminStylist/index');
        exit;
    }

    public function update()
    {
        $this->authorizeAdmin();

        $id = $_POST['id'] ?? null;
        $user = User::find((int) $id);

        if ($user) {
            $data = $_POST;

            // Lógica Password
            if (!empty($_POST['password'])) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            } else {
                unset($data['password']);
            }

            // --- LOGICA FOTO (NUEVO) ---
            $nombreFoto = $this->subirFoto();
            if ($nombreFoto) {
                $data['foto'] = $nombreFoto;
            }
            // --------------------------

            // Actualizamos datos básicos + foto
            $user->update($data);

            // Actualizar Servicios
            $serviciosSeleccionados = $_POST['servicios'] ?? [];
            User::syncServices($user->id, $serviciosSeleccionados);
        }

        header('Location: ' . BASE_URL . '/index.php?url=AdminStylist/index');
        exit;
    }

    // ... delete y toggle se mantienen igual ...
    // 4. Eliminar Estilista (AQUÍ ESTABA EL VACÍO)
    public function delete()
    {
        $this->authorizeAdmin();

        $id = $_GET['id'] ?? null;
        $user = User::find((int) $id);

        if ($user) {
            $user->delete();
        }

        header('Location: ' . BASE_URL . '/index.php?url=AdminStylist/index');
        exit;
    }

    // 5. Anular / Activar Toggle (AQUÍ ESTABA EL VACÍO)
    public function toggle()
    {
        $this->authorizeAdmin();

        $id = $_GET['id'] ?? null;
        $user = User::find((int) $id);

        if ($user) {
            $user->toggleStatus();
        }

        header('Location: ' . BASE_URL . '/index.php?url=AdminStylist/index');
        exit;
    }

    /**
     * Función privada para procesar la subida de imagen
     */
    private function subirFoto()
    {
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['foto']['tmp_name'];
            $fileName = $_FILES['foto']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp');

            if (in_array($fileExtension, $allowedfileExtensions)) {
                // Usamos prefijo 'stylist_' para diferenciarlos
                $newFileName = 'stylist_' . md5(time() . $fileName) . '.' . $fileExtension;

                // Ruta: subimos 2 niveles desde Controllers hasta public/uploads/users/
                $uploadFileDir = __DIR__ . '/../../public/uploads/users/';

                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                    return $newFileName;
                }
            }
        }
        return null;
    }
}