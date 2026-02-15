<div id="history-modal"
    class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl mx-4 relative flex flex-col max-h-[90vh]">

        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-xl">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Historial de Citas</h2>
                <p class="text-sm text-gray-500" id="history-client-name">Cargando...</p>
            </div>
            <button onclick="closeHistoryModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-6 overflow-y-auto flex-1">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                        <th class="px-4 py-3">Cita</th>
                        <th class="px-4 py-3">Consumo</th>
                        <th class="px-4 py-3 text-center">Nota</th>
                        <th class="px-4 py-3">Cronología</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody id="history-table-body" class="divide-y divide-gray-100 text-sm text-gray-600">
                </tbody>
            </table>

            <div id="history-loading" class="text-center py-8 hidden">
                <svg class="animate-spin h-8 w-8 text-indigo-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <p class="mt-2 text-gray-400">Cargando historial...</p>
            </div>
            <p id="history-empty" class="text-center py-8 text-gray-400 hidden">Este cliente no tiene citas registradas.
            </p>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-xl text-right">
            <button onclick="closeHistoryModal()"
                class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Cerrar</button>
        </div>
    </div>
</div>

<div id="note-view-modal"
    class="fixed inset-0 z-[60] flex items-center justify-center hidden bg-black bg-opacity-60 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-lg shadow-2xl p-6 w-80 md:w-96 transform scale-100 relative animate-fade-in-up">

        <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
            <span>📝</span> Nota de la Cita
        </h3>

        <div class="bg-yellow-50 border border-yellow-100 rounded p-4 text-gray-700 text-sm italic max-h-60 overflow-y-auto"
            id="note-content">
        </div>

        <div class="mt-4 text-right">
            <button onclick="document.getElementById('note-view-modal').classList.add('hidden')"
                class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700 transition shadow-md">
                Entendido
            </button>
        </div>
    </div>
</div>