<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario de Productos | Masuno</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_URL ?>/assets/favicon-32x32.png">
</head>
<body class="bg-gray-50 min-h-screen flex flex-col md:flex-row">

    <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

    <main class="flex-1 w-full ml-0 md:ml-64 transition-all duration-300">
        
        <div class="p-4 md:p-8">

            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Inventario</h1>
                    <p class="text-gray-500 mt-1">Gestiona los productos y el stock disponible.</p>
                </div>
                <button onclick="openFormModal('create')" class="inline-flex items-center justify-center bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 cursor-pointer">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Nuevo Producto
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[700px]">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Producto</th>
                                <th class="px-6 py-4">Precio</th>
                                <th class="px-6 py-4">Stock</th>
                                <th class="px-6 py-4">Estado</th>
                                <th class="px-6 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($productos)): ?>
                                <tr><td colspan="5" class="text-center py-8 text-gray-400">No hay productos registrados.</td></tr>
                            <?php else: ?>
                                <?php foreach ($productos as $p): ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-800"><?= htmlspecialchars($p->nombre) ?></div>
                                            <div class="text-xs text-gray-400 truncate max-w-xs"><?= htmlspecialchars($p->descripcion) ?></div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 font-medium">S/. <?= number_format($p->precio, 2) ?></td>
                                        <td class="px-6 py-4">
                                            <?php if ($p->stock == 0): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800">AGOTADO</span>
                                            <?php elseif ($p->stock < 5): ?>
                                                <span class="inline-flex items-center text-red-600 font-bold">
                                                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                                    <?= $p->stock ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-gray-700 font-bold"><?= $p->stock ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if ($p->activo): ?>
                                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold border border-green-200">Activo</span>
                                            <?php else: ?>
                                                <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded-full text-xs border border-gray-200">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end items-center space-x-2">
                                                <button onclick='openFormModal("edit", <?= json_encode($p) ?>)' class="text-indigo-600 hover:text-indigo-900 transition-colors p-1" title="Editar">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                </button>
                                                <button onclick="openToggleModal(<?= $p->id ?>, <?= $p->activo ?>)" class="<?= $p->activo ? 'text-amber-600 hover:text-amber-900' : 'text-green-600 hover:text-green-900' ?> transition-colors p-1" title="Cambiar Estado">
                                                    <?php if($p->activo): ?>
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                                    <?php else: ?>
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    <?php endif; ?>
                                                </button>
                                                <button onclick="openDeleteModal(<?= $p->id ?>)" class="text-red-400 hover:text-red-700 transition-colors p-1" title="Eliminar">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </div>
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

    <div id="formModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all scale-100">
            <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center">
                <h3 id="formModalTitle" class="text-lg font-bold text-white tracking-wide">Nuevo Producto</h3>
                <button onclick="closeFormModal()" class="text-indigo-200 hover:text-white transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form id="productForm" action="" method="POST" class="p-6 space-y-5">
                <input type="hidden" name="id" id="prod_id">
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre</label>
                    <input type="text" name="nombre" id="prod_nombre" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción</label>
                    <textarea name="descripcion" id="prod_desc" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 outline-none resize-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Precio (S/.)</label>
                        <input type="number" step="0.01" name="precio" id="prod_precio" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Stock</label>
                        <input type="number" name="stock" id="prod_stock" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>
                <div class="pt-4 flex justify-end space-x-3 border-t border-gray-100 mt-4">
                    <button type="button" onclick="closeFormModal()" class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-md">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="toggleModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden text-center p-6">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 mb-4">
                <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <h3 id="toggleTitle" class="text-lg font-bold text-gray-900 mb-2">¿Cambiar Estado?</h3>
            <p id="toggleMessage" class="text-sm text-gray-500 mb-6">Esta acción cambiará la visibilidad del producto.</p>
            <div class="flex justify-center space-x-3">
                <button onclick="closeToggleModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</button>
                <a id="btnConfirmToggle" href="#" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 shadow-md">Confirmar</a>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden text-center p-6">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">¿Eliminar Producto?</h3>
            <p class="text-sm text-gray-500 mb-6">Esta acción no se puede deshacer. Se eliminará permanentemente.</p>
            <div class="flex justify-center space-x-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</button>
                <a id="btnConfirmDelete" href="#" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 shadow-md">Sí, Eliminar</a>
            </div>
        </div>
    </div>

    <button id="openSidebar" class="md:hidden fixed top-1/2 left-0 z-40 transform -translate-y-1/2 bg-indigo-600 text-white p-3 pr-4 rounded-r-2xl shadow-lg opacity-50 hover:opacity-100 transition-all duration-300 focus:outline-none hover:shadow-indigo-500/50">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>
    </button>

    <script>
        // --- JS SIDEBAR ---
        const btnOpen = document.getElementById('openSidebar');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const btnClose = document.getElementById('closeSidebar');

        function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
        }

        if (btnOpen) btnOpen.addEventListener('click', toggleSidebar);
        if (btnClose) btnClose.addEventListener('click', toggleSidebar);
        if (overlay) overlay.addEventListener('click', toggleSidebar);

        // --- JS MODALES ---
        
        // Formulario (Crear / Editar)
        function openFormModal(mode, data = null) {
            const modal = document.getElementById('formModal');
            const form = document.getElementById('productForm');
            const title = document.getElementById('formModalTitle');
            
            modal.classList.remove('hidden');
            
            if (mode === 'create') {
                title.textContent = 'Nuevo Producto';
                form.action = '<?= BASE_URL ?>/index.php?url=Product/store';
                form.reset();
            } else {
                title.textContent = 'Editar Producto';
                form.action = '<?= BASE_URL ?>/index.php?url=Product/update';
                document.getElementById('prod_id').value = data.id;
                document.getElementById('prod_nombre').value = data.nombre;
                document.getElementById('prod_desc').value = data.descripcion;
                document.getElementById('prod_precio').value = data.precio;
                document.getElementById('prod_stock').value = data.stock;
            }
        }
        function closeFormModal() { document.getElementById('formModal').classList.add('hidden'); }

        // Toggle (Anular)
        function openToggleModal(id, isActive) {
            const modal = document.getElementById('toggleModal');
            const btn = document.getElementById('btnConfirmToggle');
            const title = document.getElementById('toggleTitle');
            const msg = document.getElementById('toggleMessage');

            btn.href = '<?= BASE_URL ?>/index.php?url=Product/toggle&id=' + id;

            if(isActive) {
                title.textContent = '¿Desactivar?';
                msg.textContent = 'El producto ya no aparecerá disponible para la venta.';
                btn.className = "px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 shadow-md";
                btn.textContent = 'Desactivar';
            } else {
                title.textContent = '¿Activar?';
                msg.textContent = 'El producto volverá a estar disponible.';
                btn.className = "px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-md";
                btn.textContent = 'Activar';
            }
            modal.classList.remove('hidden');
        }
        function closeToggleModal() { document.getElementById('toggleModal').classList.add('hidden'); }

        // Eliminar
        function openDeleteModal(id) {
            const modal = document.getElementById('deleteModal');
            document.getElementById('btnConfirmDelete').href = '<?= BASE_URL ?>/index.php?url=Product/delete&id=' + id;
            modal.classList.remove('hidden');
        }
        function closeDeleteModal() { document.getElementById('deleteModal').classList.add('hidden'); }
    </script>
</body>
</html>