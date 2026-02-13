<div id="universalModal"
    class="fixed inset-0 z-[60] flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-96 text-center transform transition-all scale-100 mx-4">

        <div id="u-icon-bg"
            class="mx-auto flex items-center justify-center h-14 w-14 rounded-full mb-4 transition-colors duration-300">
            <svg id="u-icon" class="h-8 w-8 transition-colors duration-300" fill="none" viewBox="0 0 24 24"
                stroke="currentColor"></svg>
        </div>

        <h3 id="u-title" class="text-xl font-bold text-gray-900 mb-2">Titulo</h3>
        <p id="u-msg" class="text-sm text-gray-500 mb-6 leading-relaxed">Mensaje de confirmación...</p>

        <div class="flex justify-center gap-3">
            <button onclick="closeAlert()"
                class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium focus:outline-none">
                Cancelar
            </button>
            <a id="u-btn-confirm" href="#"
                class="px-5 py-2.5 text-white rounded-lg shadow-md transition font-medium focus:outline-none flex items-center justify-center">
                Confirmar
            </a>
        </div>
    </div>
</div>

<script>
    /**
     * Abre el modal universal configurado según el tipo de acción.
     * @param {string} type - 'delete' (Rojo), 'warning' (Naranja/Anular), 'success' (Verde/Activar)
     * @param {string} title - El título en negrita (Ej: "¿Eliminar Reserva?")
     * @param {string} message - El mensaje explicativo
     * @param {string} url - La URL a la que ir si se confirma
     */
    function openAlert(type, title, message, url) {
        const modal = document.getElementById('universalModal');
        const iconBg = document.getElementById('u-icon-bg');
        const icon = document.getElementById('u-icon');
        const hTitle = document.getElementById('u-title');
        const pMsg = document.getElementById('u-msg');
        const btn = document.getElementById('u-btn-confirm');

        // 1. Configurar Textos y Link
        hTitle.textContent = title;
        pMsg.textContent = message;
        btn.href = url;

        // 2. Configurar Estilos según el Tipo
        if (type === 'delete') {
            // --- ESTILO ROJO (Danger) ---
            iconBg.className = "mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-100 mb-4";
            icon.setAttribute('class', 'h-8 w-8 text-red-600');
            // Icono Basura
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />';

            btn.className = "px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-md transition font-medium";
            btn.textContent = "Sí, Eliminar";

        } else if (type === 'warning') {
            // --- ESTILO AMBAR (Warning / Anular) ---
            iconBg.className = "mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-amber-100 mb-4";
            icon.setAttribute('class', 'h-8 w-8 text-amber-600');
            // Icono Prohibido/Ban
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />';

            btn.className = "px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg shadow-md transition font-medium";
            btn.textContent = "Sí, Anular";

        } else if (type === 'success') {
            // --- ESTILO VERDE (Success / Activar) ---
            iconBg.className = "mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-green-100 mb-4";
            icon.setAttribute('class', 'h-8 w-8 text-green-600');
            // Icono Check
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';

            btn.className = "px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-md transition font-medium";
            btn.textContent = "Confirmar";
        }

        // 3. Mostrar Modal
        modal.classList.remove('hidden');
    }

    function closeAlert() {
        document.getElementById('universalModal').classList.add('hidden');
    }

    // Cerrar al hacer clic fuera del modal (backdrop)
    window.onclick = function (event) {
        const modal = document.getElementById('universalModal');
        if (event.target == modal) {
            closeAlert();
        }
        // Nota: Si tienes otros modales en la página principal, 
        // deberás coordinar este evento window.onclick o usar eventListeners individuales.
    }
</script>