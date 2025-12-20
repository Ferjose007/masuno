<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Servicios | Masuno Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_URL ?>/assets/favicon-32x32.png">
</head>
<body class="bg-gray-50 min-h-screen">

  <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

  <main class="ml-64 p-8 w-auto">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Servicios</h1>
        <p class="text-gray-500 mt-1">Administra el catálogo de tratamientos.</p>
      </div>
      <button onclick="openModal('create')" 
         class="inline-flex items-center justify-center bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition shadow-sm cursor-pointer">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo Servicio
      </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="px-6 py-4 font-semibold">Nombre del Servicio</th>
                    <th class="px-6 py-4 font-semibold">Duración</th>
                    <th class="px-6 py-4 font-semibold">Precio</th>
                    <th class="px-6 py-4 font-semibold text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if(empty($servicios)): ?>
                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">No hay servicios registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($servicios as $s): ?>
                    <tr class="hover:bg-gray-50 transition-colors <?= ($s->activo == 0) ? 'bg-gray-50 opacity-75' : '' ?>">
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-800"><?= htmlspecialchars($s->nombre) ?></span>
                            <?php if($s->activo == 0): ?>
                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactivo
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                <?= $s->duracion_minutes ?> min
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-800">S/. <?= number_format($s->precio, 2) ?></td>
                        <td class="px-6 py-4 text-right space-x-2">
                            
                            <button onclick='showDescription(<?= json_encode($s->nombre) ?>, <?= json_encode($s->descripcion ?? "") ?>)' 
                                class="text-blue-600 hover:text-blue-900 font-medium text-sm transition-colors cursor-pointer" title="Ver Descripción">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </button>

                            <button onclick='openModal("edit", <?= json_encode($s) ?>)'
                               class="text-indigo-600 hover:text-indigo-900 font-medium text-sm transition-colors cursor-pointer" title="Editar">
                               <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </button>
                            
                            <?php if($s->activo == 1): ?>
                                <button onclick="openToggleModal(<?= $s->id ?>, 'desactivar', '<?= htmlspecialchars($s->nombre) ?>')"
                                    class="text-amber-600 hover:text-amber-900 font-medium text-sm transition-colors cursor-pointer" title="Anular Servicio">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                </button>
                            <?php else: ?>
                                <button onclick="openToggleModal(<?= $s->id ?>, 'activar', '<?= htmlspecialchars($s->nombre) ?>')"
                                    class="text-green-600 hover:text-green-900 font-medium text-sm transition-colors cursor-pointer" title="Reactivar Servicio">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                            <?php endif; ?>

                            <button onclick="openDeleteModal(<?= $s->id ?>)"
                                class="text-red-600 hover:text-red-900 font-medium text-sm transition-colors cursor-pointer" title="Eliminar Definitivamente">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
  </main>

  <div id="desc-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md transform transition-all scale-100 mx-4">
        <div class="flex justify-between items-start mb-4">
            <h3 id="desc-title" class="text-xl font-bold text-gray-800">Nombre del Servicio</h3>
            <button onclick="closeDescModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 text-gray-600 text-sm leading-relaxed" id="desc-content">
            </div>
        <div class="mt-6 text-right">
            <button onclick="closeDescModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Entendido</button>
        </div>
    </div>
  </div>

  <div id="form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg transform transition-all scale-100 mx-4">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-xl">
            <h2 id="modal-title" class="text-xl font-bold text-gray-800">Nuevo Servicio</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="service-form" action="" method="post" class="p-6 space-y-4">
            <input type="hidden" name="id" id="service-id">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Servicio <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" id="service-nombre" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 transition-colors" placeholder="Ej. Corte Premium">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción <span class="text-gray-400 text-xs">(Opcional)</span></label>
                <textarea name="descripcion" id="service-descripcion" rows="3"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 transition-colors" placeholder="Detalles del servicio..."></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duración (min) <span class="text-red-500">*</span></label>
                    <input type="number" name="duracion_minutes" id="service-duracion" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500" placeholder="30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio (S/.) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="precio" id="service-precio" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500" placeholder="0.00">
                </div>
            </div>
            <div class="pt-4 flex justify-end gap-3 border-t border-gray-50 mt-4">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-md">Guardar</button>
            </div>
        </form>
    </div>
  </div>

  <div id="toggle-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-96 text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4" id="toggle-icon-bg">
            <svg class="h-6 w-6 text-white" id="toggle-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                </svg>
        </div>
        <h3 class="text-lg leading-6 font-medium text-gray-900" id="toggle-title">¿Estás seguro?</h3>
        <p class="mt-2 text-sm text-gray-500" id="toggle-msg">El servicio no estará disponible.</p>
        
        <div class="mt-5 sm:mt-6 flex justify-center gap-3">
            <button onclick="closeToggleModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancelar</button>
            <button id="confirm-toggle-btn" class="px-4 py-2 text-white rounded-lg shadow-md transition-colors">Confirmar</button>
        </div>
    </div>
  </div>

  <div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-96 text-center">
      <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      </div>
      <h3 class="text-lg leading-6 font-medium text-gray-900">¿Eliminar servicio?</h3>
      <p class="mt-2 text-sm text-gray-500">Esta acción no se puede deshacer.</p>
      <div class="mt-5 sm:mt-6 flex justify-center gap-3">
        <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancelar</button>
        <button id="confirm-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 shadow-md">Sí, eliminar</button>
      </div>
    </div>
  </div>

  <script>
    const baseUrl = "<?= BASE_URL ?>";

    // --- Modal Descripción (Ojito) ---
    function showDescription(title, content) {
        document.getElementById('desc-title').textContent = title;
        document.getElementById('desc-content').textContent = content && content.trim() !== '' ? content : 'No hay descripción disponible para este servicio.';
        document.getElementById('desc-modal').classList.remove('hidden');
    }
    function closeDescModal() {
        document.getElementById('desc-modal').classList.add('hidden');
    }

    // --- Modal Formulario (Crear/Editar) ---
    function openModal(mode, data = null) {
        const modal = document.getElementById('form-modal');
        const form = document.getElementById('service-form');
        const title = document.getElementById('modal-title');
        
        modal.classList.remove('hidden');

        if (mode === 'create') {
            title.textContent = 'Nuevo Servicio';
            form.action = `${baseUrl}/index.php?url=Service/store`;
            form.reset();
            document.getElementById('service-id').value = ''; 
        } else {
            title.textContent = 'Editar Servicio';
            form.action = `${baseUrl}/index.php?url=Service/update`;
            
            document.getElementById('service-id').value = data.id;
            document.getElementById('service-nombre').value = data.nombre;
            document.getElementById('service-descripcion').value = data.descripcion || ''; 
            document.getElementById('service-duracion').value = data.duracion_minutes;
            document.getElementById('service-precio').value = data.precio;
        }
    }
    function closeModal() {
        document.getElementById('form-modal').classList.add('hidden');
    }

    // --- Modal Toggle (Anular/Activar) ---
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
            title.textContent = `¿Anular servicio: ${nombre}?`;
            msg.textContent = "No se podrá reservar este servicio, pero se mantendrá en el historial.";
            btn.textContent = "Sí, Anular";
            btn.className = "px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 shadow-md transition";
            iconBg.className = "mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 mb-4";
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />';
            icon.classList.add('text-amber-600');
            icon.classList.remove('text-white');
        } else {
            title.textContent = `¿Reactivar servicio: ${nombre}?`;
            msg.textContent = "El servicio volverá a estar disponible para reservas.";
            btn.textContent = "Sí, Reactivar";
            btn.className = "px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-md transition";
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
        if(toggleId) window.location.href = `${baseUrl}/index.php?url=Service/toggle&id=${toggleId}`;
    });

    // --- Modal Eliminar ---
    let deleteId = null;
    function openDeleteModal(id) {
        deleteId = id;
        document.getElementById('delete-modal').classList.remove('hidden');
    }
    function closeDeleteModal() {
        deleteId = null;
        document.getElementById('delete-modal').classList.add('hidden');
    }
    document.getElementById('confirm-delete-btn').addEventListener('click', function() {
        if(deleteId) window.location.href = `${baseUrl}/index.php?url=Service/delete&id=${deleteId}`;
    });

    // Cerrar al dar click fuera
    window.onclick = function(event) {
        if (event.target == document.getElementById('form-modal')) closeModal();
        if (event.target == document.getElementById('delete-modal')) closeDeleteModal();
        if (event.target == document.getElementById('desc-modal')) closeDescModal();
        if (event.target == document.getElementById('toggle-modal')) closeToggleModal();
    }
  </script>
</body>
</html>