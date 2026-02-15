<div id="view-modal"
    class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md mx-4 relative">
        <button onclick="closeViewModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="text-center mb-6">
            <div class="h-24 w-24 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 text-3xl font-bold mx-auto mb-3 border-4 border-white shadow-md overflow-hidden"
                id="view-avatar">
            </div>

            <h3 class="text-xl font-bold text-gray-900" id="view-nombre">Nombre</h3>
            <div id="view-status-badge" class="mt-2"></div>
        </div>

        <div class="space-y-4 border-t border-gray-100 pt-4">
            <div class="flex justify-between items-center"><span class="text-gray-500 text-sm">Email:</span><span
                    class="text-gray-800 font-medium text-sm" id="view-email">-</span></div>
            <div class="flex justify-between items-center"><span class="text-gray-500 text-sm">Teléfono:</span><span
                    class="text-gray-800 font-medium text-sm" id="view-telefono">-</span></div>
            <div class="flex justify-between items-center"><span class="text-gray-500 text-sm">Registrado:</span><span
                    class="text-gray-800 font-medium text-sm" id="view-creado">-</span></div>
            <div class="flex justify-between items-center"><span class="text-gray-500 text-sm">Modificado:</span><span
                    class="text-gray-800 font-medium text-sm" id="view-actualizado">-</span></div>
        </div>

        <div class="mt-4 border-t border-gray-100 pt-3">
            <span class="text-gray-500 text-sm block mb-2 font-medium">Especialidades:</span>
            <div id="view-servicios-lista" class="flex flex-wrap gap-2">
            </div>
        </div>

        <div class="mt-6 pt-4 text-center">
            <button onclick="closeViewModal()"
                class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Cerrar</button>
        </div>
    </div>
</div>