<div id="form-modal"
    class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg transform transition-all scale-100 mx-4">

        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-xl">
            <h2 id="modal-title" class="text-xl font-bold text-gray-800">Nuevo Cliente</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="client-form" action="" method="post" enctype="multipart/form-data" class="p-6 space-y-4">
            <input type="hidden" name="id" id="client-id">

            <div class="flex flex-col items-center mb-2">
                <div class="relative group cursor-pointer w-24 h-24">
                    <div
                        class="w-full h-full rounded-full overflow-hidden border-4 border-indigo-50 shadow-sm bg-gray-100 flex items-center justify-center relative">
                        <svg id="default-icon" class="w-10 h-10 text-gray-400 absolute" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <img id="preview-img" src="" class="w-full h-full object-cover hidden z-10">
                    </div>

                    <div
                        class="absolute inset-0 rounded-full bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all flex items-center justify-center z-20">
                        <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-all"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>

                    <input type="file" name="foto" id="foto_input" accept="image/*"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-30"
                        onchange="previewImage(event)">
                </div>
                <p class="text-xs text-gray-500 mt-2 font-medium">Click para cambiar foto</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nombre" id="client-nombre" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 transition-shadow">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">DNI <span
                            class="text-gray-400 text-xs">(Opcional)</span></label>
                    <input type="text" name="dni" id="client-dni"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500"
                        placeholder="00000000">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span
                            class="text-red-500">*</span></label>
                    <input type="email" name="email" id="client-email" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 transition-shadow">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="telefono" id="client-telefono"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 transition-shadow">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" id="label-password">Contraseña</label>
                <input type="password" name="password" id="client-password"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 transition-shadow"
                    placeholder="••••••••">
                <p class="text-xs text-gray-500 mt-1 hidden" id="hint-password">Dejar en blanco para mantener la actual.
                </p>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-50 mt-4">
                <button type="button" onclick="closeModal()"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Cancelar</button>
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Función para previsualizar foto al seleccionar
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('preview-img');
        const icon = document.getElementById('default-icon');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                icon.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>