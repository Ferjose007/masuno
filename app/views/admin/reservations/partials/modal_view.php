<div id="viewModal"
    class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all scale-100 mx-4">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 rounded-t-xl flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Detalles de la Cita</h3>
            <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg></button>
        </div>
        <div class="p-6 space-y-4 text-sm text-gray-600">
            <div class="flex justify-between border-b pb-2">
                <span class="font-bold text-gray-800">Cliente:</span>
                <span id="view-cliente"></span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="font-bold text-gray-800">Estilista:</span>
                <span id="view-estilista"></span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="font-bold text-gray-800">Fecha/Hora:</span>
                <span id="view-fecha"></span>
            </div>
            <div>
                <span class="font-bold text-gray-800 block mb-1">Servicio(s):</span>
                <div id="view-servicios" class="bg-gray-50 p-2 rounded text-gray-700"></div>
            </div>
            <div>
                <span class="font-bold text-gray-800 block mb-1">Notas:</span>
                <p id="view-notas" class="italic text-gray-500">Ninguna</p>
            </div>
        </div>
        <div class="p-4 bg-gray-50 rounded-b-xl text-center">
            <button onclick="closeViewModal()"
                class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 font-medium">Cerrar</button>
        </div>
    </div>
</div>

<script>
    function openViewModal(data) {
        document.getElementById('viewModal').classList.remove('hidden');
        document.getElementById('view-cliente').textContent = data.cliente_nombre;
        document.getElementById('view-estilista').textContent = data.estilista_nombre;
        document.getElementById('view-fecha').textContent = data.fecha_cita + ' ' + data.hora_cita;
        document.getElementById('view-servicios').textContent = data.servicio_nombre; // Ojo: mejorar si hay múltiples
        document.getElementById('view-notas').textContent = data.notas || 'Sin notas.';
    }
    function closeViewModal() { document.getElementById('viewModal').classList.add('hidden'); }
</script>