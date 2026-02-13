<div id="formModal"
    class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
    <div
        class="bg-white rounded-xl shadow-2xl w-full max-w-lg transform transition-all scale-100 mx-4 flex flex-col max-h-[90vh]">

        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-xl">
            <h2 id="modalTitle" class="text-xl font-bold text-gray-800">Nueva Reserva</h2>
            <button onclick="closeFormModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg></button>
        </div>

        <div class="flex-1 overflow-y-auto p-6">
            <form id="reservationForm" action="" method="POST" class="space-y-4">
                <input type="hidden" name="id" id="reserva_id">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                        <select name="cliente_id" id="cliente_id" required class="...">
                            <option value="">Seleccione un cliente...</option>

                            <?php foreach ($clientes as $c): ?>
                                <option value="<?= $c->id ?>"><?= $c->nombre ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estilista</label>
                        <select name="estilista_id" id="estilista_id" required class="...">
                            <option value="">Seleccione un estilista...</option>

                            <?php foreach ($estilistas as $e): ?>
                                <option value="<?= $e->id ?>"><?= $e->nombre ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                        <input type="date" name="fecha_cita" id="fecha_cita" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora</label>
                        <input type="time" name="hora_cita" id="hora_cita" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="space-y-3 pt-2 border-t border-gray-100">
                    <div class="flex justify-between items-center">
                        <label class="block text-sm font-bold text-gray-700">Servicios</label>
                        <button type="button" onclick="agregarFilaServicio()"
                            class="text-xs bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full font-bold hover:bg-indigo-100">+
                            Agregar</button>
                    </div>
                    <div id="contenedor-servicios" class="space-y-2">
                    </div>
                    <div class="text-right text-xs text-gray-500">Total estimado: <span id="total-estimado"
                            class="font-bold text-gray-800">S/. 0.00</span></div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                    <textarea name="notas" id="notas" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
                </div>
            </form>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 rounded-b-xl flex justify-end gap-3">
            <button onclick="closeFormModal()"
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">Cancelar</button>
            <button type="submit" form="reservationForm"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-md font-medium">Guardar</button>
        </div>
    </div>
</div>

<script>
    // --- LÓGICA DE SERVICIOS MÚLTIPLES ---
    // Debes tener la variable $servicios disponible en PHP
    const listaServicios = [<?php foreach ($servicios as $s): ?> { id: <?= $s->id ?>, nombre: "<?= $s->nombre ?>", precio: <?= $s->precio ?> }, <?php endforeach; ?>];

    function generarOptions(selId) {
        let h = '<option value="">Seleccione...</option>';
        listaServicios.forEach(s => h += `<option value="${s.id}" data-precio="${s.precio}" ${s.id == selId ? 'selected' : ''}>${s.nombre} (S/.${s.precio})</option>`);
        return h;
    }

    function agregarFilaServicio(selId = null) {
        const d = document.createElement('div');
        d.className = "flex gap-2 items-center fila-servicio";
        d.innerHTML = `<select name="servicios[]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm select-svc" onchange="calcTotal()">${generarOptions(selId)}</select>
                       <button type="button" onclick="eliminarFila(this)" class="text-red-400 hover:text-red-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>`;
        document.getElementById('contenedor-servicios').appendChild(d);
        calcTotal();
    }

    function eliminarFila(btn) {
        if (document.getElementById('contenedor-servicios').children.length > 1) { btn.parentElement.remove(); calcTotal(); }
    }

    function calcTotal() {
        let t = 0;
        document.querySelectorAll('.select-svc').forEach(s => {
            if (s.value) t += parseFloat(s.options[s.selectedIndex].getAttribute('data-precio') || 0);
        });
        document.getElementById('total-estimado').textContent = "S/. " + t.toFixed(2);
    }

    function openFormModal(mode, data = null) {
        // 1. Mostrar el modal
        document.getElementById('formModal').classList.remove('hidden');

        // 2. Referencias a los elementos
        const form = document.getElementById('reservationForm');
        const title = document.getElementById('modalTitle');
        const selCliente = document.getElementById('cliente_id');
        const selEstilista = document.getElementById('estilista_id');
        const inpFecha = document.getElementById('fecha_cita');
        const inpHora = document.getElementById('hora_cita');
        const txtNotas = document.getElementById('notas');

        // 3. Limpiar Servicios (Resetear dinámicos)
        document.getElementById('contenedor-servicios').innerHTML = '';

        if (mode === 'create') {
            // --- MODO CREAR ---
            title.textContent = 'Nueva Reserva';
            form.action = '<?= BASE_URL ?>/index.php?url=Reservation/store';

            // Limpiamos el formulario completamente
            form.reset();

            // FORZAMOS QUE LOS SELECTS ESTÉN VACÍOS (Soluciona tu problema 1)
            selCliente.value = "";
            selEstilista.value = "";

            // Agregamos 1 fila de servicio vacía
            agregarFilaServicio();

        } else {
            // --- MODO EDITAR ---
            title.textContent = 'Editar Reserva';
            form.action = '<?= BASE_URL ?>/index.php?url=Reservation/update';

            // Llenar IDs básicos
            document.getElementById('reserva_id').value = data.id;

            // --- AQUÍ SOLUCIONAMOS EL PROBLEMA 2 (Llenar Selects) ---
            // Verifica en la consola del navegador qué nombres de propiedad trae 'data'
            // Usualmente es data.cliente_id, pero a veces viene como string.
            selCliente.value = data.cliente_id;
            selEstilista.value = data.estilista_id;

            // Llenar Fecha y Hora
            inpFecha.value = data.fecha_cita;
            // Cortamos los segundos (HH:mm:ss -> HH:mm) para que el input type="time" lo lea
            inpHora.value = data.hora_cita ? data.hora_cita.substring(0, 5) : '';

            txtNotas.value = data.notas || '';

            // Llenar Servicios
            if (data.servicios_ids) {
                // Convertimos la cadena "1,5,8" en un array ["1", "5", "8"]
                // El toString() es por seguridad si viniera como número
                const ids = data.servicios_ids.toString().split(',');

                ids.forEach(id => {
                    agregarFilaServicio(id); // Tu función ya sabe seleccionar el ID correcto
                });

            } else if (data.servicio_id) {
                // Fallback por si es una reserva antigua con el sistema viejo
                agregarFilaServicio(data.servicio_id);
            } else {
                // Si no hay nada, al menos una vacía
                agregarFilaServicio();
            }

            calcTotal(); // Recalcular total visual
        }
    }
    function closeFormModal() { document.getElementById('formModal').classList.add('hidden'); }
</script>