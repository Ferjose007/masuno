<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes | Masuno Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_URL ?>/assets/favicon-32x32.png">
</head>

<body class="bg-gray-50 min-h-screen flex flex-col md:flex-row">

    <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

    <main class="flex-1 w-full ml-0 md:ml-64 transition-all duration-300">
        <button id="openSidebar"
            class="md:hidden fixed top-1/2 left-0 z-40 transform -translate-y-1/2 bg-indigo-600 text-white p-3 pr-4 rounded-r-2xl shadow-lg opacity-50 hover:opacity-100 transition-all duration-300 focus:outline-none hover:shadow-indigo-500/50">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
            </svg>
        </button>

        <div class="p-4 md:p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Clientes</h1>
                    <p class="text-gray-500 mt-1">Gestiona la base de datos de tus clientes.</p>
                </div>
                <button onclick="openModal('create')"
                    class="inline-flex items-center justify-center bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition shadow-sm cursor-pointer">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Nuevo Cliente
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[600px]">
                        <thead>
                            <tr
                                class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                <th class="px-6 py-4 font-semibold">Nombre</th>
                                <th class="px-6 py-4 font-semibold">Email</th>
                                <th class="px-6 py-4 font-semibold">Teléfono</th>
                                <th class="px-6 py-4 font-semibold text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($clientes)): ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-400">No hay clientes registrados.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($clientes as $c): ?>
                                    <tr
                                        class="hover:bg-gray-50 transition-colors <?= ($c->activo == 0) ? 'bg-gray-50 opacity-75' : '' ?>">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div
                                                    class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold mr-3 overflow-hidden">
                                                    <?php if (!empty($c->foto)): ?>
                                                        <img src="<?= BASE_URL ?>/uploads/users/<?= $c->foto ?>"
                                                            class="w-full h-full object-cover">
                                                    <?php else: ?>
                                                        <?= strtoupper(substr($c->nombre, 0, 1)) ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <span
                                                        class="font-medium text-gray-800 block"><?= htmlspecialchars($c->nombre) ?></span>
                                                    <?php if ($c->activo == 0): ?>
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Inactivo</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 text-sm"><?= htmlspecialchars($c->email) ?></td>
                                        <td class="px-6 py-4 text-gray-600 text-sm"><?= htmlspecialchars($c->telefono ?? '-') ?>
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-2">
                                            <button onclick='openViewModal(<?= json_encode($c) ?>)'
                                                class="text-blue-600 hover:text-blue-900 font-medium text-sm transition-colors cursor-pointer"
                                                title="Ver Detalles">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button onclick='openModal("edit", <?= json_encode($c) ?>)'
                                                class="text-indigo-600 hover:text-indigo-900 font-medium text-sm transition-colors cursor-pointer"
                                                title="Editar">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <?php if ($c->activo == 1): ?>
                                                <button
                                                    onclick="openToggleModal(<?= $c->id ?>, 'desactivar', '<?= htmlspecialchars($c->nombre) ?>')"
                                                    class="text-amber-600 hover:text-amber-900 font-medium text-sm transition-colors cursor-pointer"
                                                    title="Anular"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg></button>
                                            <?php else: ?>
                                                <button
                                                    onclick="openToggleModal(<?= $c->id ?>, 'activar', '<?= htmlspecialchars($c->nombre) ?>')"
                                                    class="text-green-600 hover:text-green-900 font-medium text-sm transition-colors cursor-pointer"
                                                    title="Reactivar"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg></button>
                                            <?php endif; ?>
                                            <button onclick="openDeleteModal(<?= $c->id ?>)"
                                                class="text-red-600 hover:text-red-900 font-medium text-sm transition-colors cursor-pointer"
                                                title="Eliminar"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/partials/modal_view.php'; ?>
    <?php include __DIR__ . '/partials/modal_form.php'; ?>

    <div id="toggle-modal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-96 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4" id="toggle-icon-bg">
                <svg class="h-6 w-6 text-white" id="toggle-icon" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor"></svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="toggle-title">¿Estás seguro?</h3>
            <p class="mt-2 text-sm text-gray-500" id="toggle-msg">El usuario no podrá acceder al sistema.</p>
            <div class="mt-5 sm:mt-6 flex justify-center gap-3">
                <button onclick="closeToggleModal()"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancelar</button>
                <button id="confirm-toggle-btn"
                    class="px-4 py-2 text-white rounded-lg shadow-md transition-colors">Confirmar</button>
            </div>
        </div>
    </div>

    <div id="delete-modal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-96 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">¿Eliminar cliente?</h3>
            <p class="mt-2 text-sm text-gray-500">Se borrarán también sus reservas futuras.</p>
            <div class="mt-5 sm:mt-6 flex justify-center gap-3">
                <button onclick="closeDeleteModal()"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancelar</button>
                <button id="confirm-delete-btn"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 shadow-md">Sí, eliminar</button>
            </div>
        </div>
    </div>

    <script>
        const btnOpen = document.getElementById('openSidebar');
        const btnClose = document.getElementById('closeSidebar');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        if (btnOpen) btnOpen.addEventListener('click', toggleSidebar);
        if (btnClose) btnClose.addEventListener('click', toggleSidebar);
        if (overlay) overlay.addEventListener('click', toggleSidebar);
    </script>

    <script>
        const baseUrl = "<?= BASE_URL ?>";

        // --- 1. Modal VER (ACTUALIZADO CON FOTOS) ---
        function openViewModal(data) {
            // Textos
            document.getElementById('view-nombre').textContent = data.nombre;
            document.getElementById('view-email').textContent = data.email;
            document.getElementById('view-telefono').textContent = data.telefono || 'No registrado';

            // FOTO: Detectar si existe y mostrarla
            const avatarDiv = document.getElementById('view-avatar');
            avatarDiv.innerHTML = ''; // Limpiar
            avatarDiv.className = "h-24 w-24 rounded-full flex items-center justify-center mx-auto mb-3 border-4 border-white shadow-md overflow-hidden";

            if (data.foto && data.foto.trim() !== "") {
                // TIENE FOTO (Usamos ruta relativa uploads/...)
                avatarDiv.innerHTML = `<img src="${baseUrl}/uploads/users/${data.foto}" class="w-full h-full object-cover">`;
                avatarDiv.classList.add('bg-gray-100');
            } else {
                // NO TIENE FOTO (Iniciales)
                avatarDiv.innerHTML = `<span class="text-4xl font-bold">${data.nombre.charAt(0).toUpperCase()}</span>`;
                avatarDiv.classList.add('bg-indigo-50', 'text-indigo-600');
            }

            // Badge Estado
            const statusDiv = document.getElementById('view-status-badge');
            if (data.activo == 1) {
                statusDiv.innerHTML = '<span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Activo</span>';
            } else {
                statusDiv.innerHTML = '<span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">Inactivo (Anulado)</span>';
            }

            // Fechas
            const fechaCreacion = data.creado_en ? new Date(data.creado_en).toLocaleDateString('es-PE', { year: 'numeric', month: 'long', day: 'numeric' }) : 'Fecha desconocida';
            document.getElementById('view-creado').textContent = fechaCreacion;

            let fechaMod = 'Sin modificaciones recientes';
            if (data.actualizado_en) {
                fechaMod = new Date(data.actualizado_en).toLocaleDateString('es-PE', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
            }
            document.getElementById('view-actualizado').textContent = fechaMod;

            document.getElementById('view-modal').classList.remove('hidden');
        }

        function closeViewModal() {
            document.getElementById('view-modal').classList.add('hidden');
        }

        // --- 2. Modal CREAR / EDITAR (ACTUALIZADO CON INPUT FOTO) ---
        function openModal(mode, data = null) {
            const modal = document.getElementById('form-modal');
            const form = document.getElementById('client-form');
            const title = document.getElementById('modal-title');

            // Inputs Textos
            const inpPass = document.getElementById('client-password');
            const hintPass = document.getElementById('hint-password');
            const lblPass = document.getElementById('label-password');

            // Inputs Foto
            const imgPreview = document.getElementById('preview-img');
            const defIcon = document.getElementById('default-icon');
            const inpFoto = document.getElementById('foto_input');

            modal.classList.remove('hidden');

            if (mode === 'create') {
                title.textContent = 'Nuevo Cliente';
                form.action = `${baseUrl}/index.php?url=Client/store`;
                form.reset();
                document.getElementById('client-id').value = '';

                // Resetear foto visualmente
                imgPreview.src = '';
                imgPreview.classList.add('hidden');
                defIcon.classList.remove('hidden');

                // Pass obligatorio
                inpPass.required = true;
                hintPass.classList.add('hidden');
                lblPass.innerHTML = 'Contraseña <span class="text-red-500">*</span>';
            } else {
                title.textContent = 'Editar Cliente';
                form.action = `${baseUrl}/index.php?url=Client/update`;

                document.getElementById('client-id').value = data.id;
                document.getElementById('client-nombre').value = data.nombre;
                document.getElementById('client-email').value = data.email;
                document.getElementById('client-telefono').value = data.telefono;

                // LÓGICA FOTO AL EDITAR
                if (data.foto && data.foto.trim() !== "") {
                    imgPreview.src = `${baseUrl}/uploads/users/${data.foto}`;
                    imgPreview.classList.remove('hidden');
                    defIcon.classList.add('hidden');
                } else {
                    imgPreview.src = '';
                    imgPreview.classList.add('hidden');
                    defIcon.classList.remove('hidden');
                }

                // Pass opcional
                inpPass.value = '';
                inpPass.required = false;
                hintPass.classList.remove('hidden');
                lblPass.innerHTML = 'Nueva Contraseña';
            }
        }

        function closeModal() {
            document.getElementById('form-modal').classList.add('hidden');
        }

        // --- 3. Modal TOGGLE y DELETE (Sin cambios) ---
        let toggleId = null;
        function openToggleModal(id, action, nombre) {
            toggleId = id;
            const modal = document.getElementById('toggle-modal');
            const title = document.getElementById('toggle-title');
            const msg = document.getElementById('toggle-msg');
            const btn = document.getElementById('confirm-toggle-btn');
            const iconBg = document.getElementById('toggle-icon-bg');
            const icon = document.getElementById('toggle-icon');

            modal.classList.remove('hidden');

            if (action === 'desactivar') {
                title.textContent = `¿Anular a ${nombre}?`;
                msg.textContent = "El usuario no podrá iniciar sesión.";
                btn.textContent = "Sí, Anular";
                btn.className = "px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 shadow-md transition";
                iconBg.className = "mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 mb-4";
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />';
                icon.classList.add('text-amber-600'); icon.classList.remove('text-white');
            } else {
                title.textContent = `¿Reactivar a ${nombre}?`;
                msg.textContent = "El usuario podrá volver a iniciar sesión.";
                btn.textContent = "Sí, Reactivar";
                btn.className = "px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-md transition";
                iconBg.className = "mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4";
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
                icon.classList.add('text-green-600'); icon.classList.remove('text-white');
            }
        }
        function closeToggleModal() {
            document.getElementById('toggle-modal').classList.add('hidden');
            toggleId = null;
        }
        document.getElementById('confirm-toggle-btn').addEventListener('click', function () {
            if (toggleId) window.location.href = `${baseUrl}/index.php?url=Client/toggle&id=${toggleId}`;
        });

        let deleteId = null;
        function openDeleteModal(id) {
            deleteId = id;
            document.getElementById('delete-modal').classList.remove('hidden');
        }
        function closeDeleteModal() {
            deleteId = null;
            document.getElementById('delete-modal').classList.add('hidden');
        }
        document.getElementById('confirm-delete-btn').addEventListener('click', function () {
            if (deleteId) window.location.href = `${baseUrl}/index.php?url=Client/delete&id=${deleteId}`;
        });

        window.onclick = function (event) {
            if (event.target == document.getElementById('form-modal')) closeModal();
            if (event.target == document.getElementById('delete-modal')) closeDeleteModal();
            if (event.target == document.getElementById('view-modal')) closeViewModal();
            if (event.target == document.getElementById('toggle-modal')) closeToggleModal();
        }
    </script>
</body>

</html>