<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Estilistas | Masuno Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_URL ?>/assets/favicon-32x32.png">
</head>

<body class="bg-gray-50 min-h-screen flex flex-col md:flex-row">

    <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

    <main class="flex-1 w-full ml-0 md:ml-64 transition-all duration-300">

    <button id="openSidebar" class="md:hidden fixed top-1/2 left-0 z-40 transform -translate-y-1/2 bg-indigo-600 text-white p-3 pr-4 rounded-r-2xl shadow-lg opacity-50 hover:opacity-100 transition-all duration-300 focus:outline-none hover:shadow-indigo-500/50">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
        </svg>
    </button>
    
        <div class="p-4 md:p-8">

            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Estilistas</h1>
                    <p class="text-gray-500 mt-1">Gestiona al personal y sus accesos.</p>
                </div>
                <button onclick="openModal('create')"
                    class="inline-flex items-center justify-center bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition shadow-sm cursor-pointer">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884.896 1.688 2 2.308 1.104-.62 2-1.424 2-2.308" />
                    </svg>
                    Nuevo Estilista
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                            <th class="px-6 py-4 font-semibold">Nombre</th>
                            <th class="px-6 py-4 font-semibold">Email</th>
                            <th class="px-6 py-4 font-semibold">Teléfono</th>
                            <th class="px-6 py-4 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($estilistas)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">No hay estilistas registrados.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($estilistas as $e): ?>
                                <tr class="hover:bg-gray-50 transition-colors <?= ($e->activo == 0) ? 'bg-gray-50 opacity-75' : '' ?>">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-sm font-bold mr-3">
                                                <?= strtoupper(substr($e->nombre, 0, 1)) ?>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-800 block"><?= htmlspecialchars($e->nombre) ?></span>
                                                <?php if ($e->activo == 0): ?>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Inactivo</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 text-sm"><?= htmlspecialchars($e->email) ?></td>
                                    <td class="px-6 py-4 text-gray-600 text-sm"><?= htmlspecialchars($e->telefono ?? '-') ?></td>
                                    <td class="px-6 py-4 text-right space-x-2">

                                        <button onclick='openViewModal(<?= json_encode($e) ?>)'
                                            class="text-blue-600 hover:text-blue-900 font-medium text-sm transition-colors cursor-pointer" title="Ver Detalles">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        <button onclick='openModal("edit", <?= json_encode($e) ?>)'
                                            class="text-indigo-600 hover:text-indigo-900 font-medium text-sm transition-colors cursor-pointer" title="Editar">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        <?php if ($e->activo == 1): ?>
                                            <button onclick="openToggleModal(<?= $e->id ?>, 'desactivar', '<?= htmlspecialchars($e->nombre) ?>')"
                                                class="text-amber-600 hover:text-amber-900 font-medium text-sm transition-colors cursor-pointer" title="Desactivar Acceso">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            </button>
                                        <?php else: ?>
                                            <button onclick="openToggleModal(<?= $e->id ?>, 'activar', '<?= htmlspecialchars($e->nombre) ?>')"
                                                class="text-green-600 hover:text-green-900 font-medium text-sm transition-colors cursor-pointer" title="Reactivar Acceso">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        <?php endif; ?>

                                        <button onclick="openDeleteModal(<?= $e->id ?>)"
                                            class="text-red-600 hover:text-red-900 font-medium text-sm transition-colors cursor-pointer" title="Eliminar">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
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

    <div id="view-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md mx-4 relative">
            <button onclick="closeViewModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="text-center mb-6">
                <div class="h-20 w-20 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 text-3xl font-bold mx-auto mb-3" id="view-avatar">A</div>
                <h3 class="text-xl font-bold text-gray-900" id="view-nombre">Nombre</h3>
                <div id="view-status-badge" class="mt-2"></div>
            </div>
            <div class="space-y-4 border-t border-gray-100 pt-4">
                <div class="flex justify-between"><span class="text-gray-500 text-sm">Email:</span><span class="text-gray-800 font-medium text-sm" id="view-email">-</span></div>
                <div class="flex justify-between"><span class="text-gray-500 text-sm">Teléfono:</span><span class="text-gray-800 font-medium text-sm" id="view-telefono">-</span></div>
                <div class="flex justify-between"><span class="text-gray-500 text-sm">Registrado:</span><span class="text-gray-800 font-medium text-sm" id="view-creado">-</span></div>
                <div class="flex justify-between"><span class="text-gray-500 text-sm">Modificado:</span><span class="text-gray-800 font-medium text-sm" id="view-actualizado">-</span></div>
            </div>
            <div class="mt-2">
                <span class="text-gray-500 text-sm block mb-1">Especialidades:</span>
                <div id="view-servicios-lista" class="flex flex-wrap gap-1">
                </div>
            </div>
            <div class="mt-6 pt-4 text-center"><button onclick="closeViewModal()" class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cerrar</button></div>
        </div>
    </div>

    <div id="form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg transform transition-all scale-100 mx-4">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-xl">
                <h2 id="modal-title" class="text-xl font-bold text-gray-800">Nuevo Estilista</h2>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="stylist-form" action="" method="post" class="p-6 space-y-4">
                <input type="hidden" name="id" id="stylist-id">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" id="stylist-nombre" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="stylist-email" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" name="telefono" id="stylist-telefono" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" id="label-password">Contraseña</label>
                    <input type="password" name="password" id="stylist-password" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500" placeholder="••••••••">
                    <p class="text-xs text-gray-500 mt-1 hidden" id="hint-password">Dejar vacío para no cambiarla.</p>
                </div>
                <div class="border-t border-gray-100 pt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Servicios que realiza:</label>
                    <div class="grid grid-cols-2 gap-2 max-h-32 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <?php foreach ($servicios as $srv): ?>
                            <label class="inline-flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="servicios[]" value="<?= $srv->id ?>"
                                    id="srv-<?= $srv->id ?>"
                                    class="form-checkbox h-4 w-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">
                                    <?= htmlspecialchars($srv->nombre) ?>
                                    <?php if ($srv->activo == 0): ?>
                                        <span class="text-red-500 text-xs font-bold">(Inactivo)</span>
                                    <?php endif; ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Selecciona al menos uno.</p>
                </div>
                <div class="pt-4 flex justify-end gap-3 border-t border-gray-50 mt-4">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-md">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="toggle-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-96 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4" id="toggle-icon-bg">
                <svg class="h-6 w-6 text-white" id="toggle-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"></svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="toggle-title">¿Estás seguro?</h3>
            <p class="mt-2 text-sm text-gray-500" id="toggle-msg">El estilista no podrá acceder.</p>
            <div class="mt-5 sm:mt-6 flex justify-center gap-3">
                <button onclick="closeToggleModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancelar</button>
                <button id="confirm-toggle-btn" class="px-4 py-2 text-white rounded-lg shadow-md">Confirmar</button>
            </div>
        </div>
    </div>

    <div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-96 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">¿Eliminar estilista?</h3>
            <p class="mt-2 text-sm text-gray-500">Se eliminará permanentemente.</p>
            <div class="mt-5 sm:mt-6 flex justify-center gap-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancelar</button>
                <button id="confirm-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 shadow-md">Sí, eliminar</button>
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

        if(btnOpen) btnOpen.addEventListener('click', toggleSidebar);
        if(btnClose) btnClose.addEventListener('click', toggleSidebar);
        if(overlay) overlay.addEventListener('click', toggleSidebar);
    </script>

    <script>
        const baseUrl = "<?= BASE_URL ?>";

        // --- Modal VER ---
        function openViewModal(data) {
            document.getElementById('view-nombre').textContent = data.nombre;
            document.getElementById('view-email').textContent = data.email;
            document.getElementById('view-telefono').textContent = data.telefono || 'No registrado';
            document.getElementById('view-avatar').textContent = data.nombre.charAt(0).toUpperCase();

            // Limpiar lista anterior
            const listaDiv = document.getElementById('view-servicios-lista');
            listaDiv.innerHTML = '';

            if (data.lista_servicios && data.lista_servicios.length > 0) {
                data.lista_servicios.forEach(s => {

                    // 1. Definimos los estilos por defecto (Activo)
                    let clases = 'bg-indigo-50 text-indigo-700 border-indigo-100';
                    let extra = '';

                    // 2. Si está Inactivo, cambiamos a Rojo y Tachado
                    if (s.activo == 0) {
                        clases = 'bg-red-50 text-red-500 border-red-100 line-through opacity-75';
                        extra = 'title="Este servicio está inactivo actualmente"';
                    }

                    // 3. Dibujamos la etiqueta
                    listaDiv.innerHTML += `
                    <span class="px-2 py-0.5 rounded text-xs border ${clases}" ${extra}>
                        ${s.nombre}
                    </span>
                `;
                });
            } else {
                listaDiv.innerHTML = '<span class="text-gray-400 text-xs italic">Ningún servicio asignado</span>';
            }

            const statusDiv = document.getElementById('view-status-badge');
            statusDiv.innerHTML = data.activo == 1 ?
                '<span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Activo</span>' :
                '<span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">Inactivo</span>';

            document.getElementById('view-creado').textContent = data.creado_en ? new Date(data.creado_en).toLocaleDateString('es-PE') : '-';
            document.getElementById('view-actualizado').textContent = data.actualizado_en ? new Date(data.actualizado_en).toLocaleDateString('es-PE') : 'Sin cambios';

            document.getElementById('view-modal').classList.remove('hidden');
        }

        function closeViewModal() {
            document.getElementById('view-modal').classList.add('hidden');
        }

        // --- Modal FORM (Crear/Editar) ---
        function openModal(mode, data = null) {
            const modal = document.getElementById('form-modal');
            const form = document.getElementById('stylist-form');
            const title = document.getElementById('modal-title');
            const passHint = document.getElementById('hint-password');
            const passInput = document.getElementById('stylist-password');
            const passLabel = document.getElementById('label-password');

            document.querySelectorAll('input[name="servicios[]"]').forEach(chk => chk.checked = false);

            modal.classList.remove('hidden');

            if (mode === 'create') {
                title.textContent = 'Nuevo Estilista';
                form.action = `${baseUrl}/index.php?url=AdminStylist/store`;
                form.reset();
                document.getElementById('stylist-id').value = '';
                passInput.required = true;
                passHint.classList.add('hidden');
                passLabel.innerHTML = 'Contraseña <span class="text-red-500">*</span>';
            } else {
                title.textContent = 'Editar Estilista';
                form.action = `${baseUrl}/index.php?url=AdminStylist/update`;
                document.getElementById('stylist-id').value = data.id;
                document.getElementById('stylist-nombre').value = data.nombre;
                document.getElementById('stylist-email').value = data.email;
                document.getElementById('stylist-telefono').value = data.telefono;
                passInput.value = '';
                passInput.required = false;
                passHint.classList.remove('hidden');
                passLabel.innerHTML = 'Nueva Contraseña';
                if (data.mis_servicios && Array.isArray(data.mis_servicios)) {
                    data.mis_servicios.forEach(srvId => {
                        const checkbox = document.getElementById(`srv-${srvId}`);
                        if (checkbox) checkbox.checked = true;
                    });
                }
            }

        }

        function closeModal() {
            document.getElementById('form-modal').classList.add('hidden');
        }

        // --- Modal TOGGLE ---
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
                msg.textContent = "El estilista perderá el acceso al sistema.";
                btn.textContent = "Sí, Anular";
                btn.className = "px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 shadow-md";
                iconBg.className = "mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 mb-4";
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />';
                icon.classList.add('text-amber-600');
                icon.classList.remove('text-white');
            } else {
                title.textContent = `¿Reactivar a ${nombre}?`;
                msg.textContent = "El estilista podrá ingresar nuevamente.";
                btn.textContent = "Sí, Reactivar";
                btn.className = "px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-md";
                iconBg.className = "mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4";
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
                icon.classList.add('text-green-600');
                icon.classList.remove('text-white');
            }
        }

        function closeToggleModal() {
            document.getElementById('toggle-modal').classList.add('hidden');
            toggleId = null;
        }
        document.getElementById('confirm-toggle-btn').addEventListener('click', function() {
            if (toggleId) window.location.href = `${baseUrl}/index.php?url=AdminStylist/toggle&id=${toggleId}`;
        });

        // --- Modal ELIMINAR ---
        let deleteId = null;

        function openDeleteModal(id) {
            deleteId = id;
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
            deleteId = null;
        }
        document.getElementById('confirm-delete-btn').addEventListener('click', function() {
            if (deleteId) window.location.href = `${baseUrl}/index.php?url=AdminStylist/delete&id=${deleteId}`;
        });

        // Clicks fuera
        window.onclick = function(event) {
            if (event.target == document.getElementById('form-modal')) closeModal();
            if (event.target == document.getElementById('delete-modal')) closeDeleteModal();
            if (event.target == document.getElementById('view-modal')) closeViewModal();
            if (event.target == document.getElementById('toggle-modal')) closeToggleModal();
        }
    </script>
</body>

</html>