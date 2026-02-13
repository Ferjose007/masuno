<div id="checkoutModal"
    class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
    <div
        class="bg-white rounded-xl shadow-2xl w-full max-w-2xl transform transition-all scale-100 mx-4 flex flex-col max-h-[90vh]">

        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-xl">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <svg class="w-6 h-6 mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Finalizar Venta
            </h2>
            <button onclick="closeCheckoutModal()"
                class="text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-6">

            <div
                class="bg-indigo-50 p-4 rounded-lg border border-indigo-100 flex justify-between items-center shadow-sm">
                <div>
                    <h4 class="font-bold text-indigo-900" id="checkout-servicio">Cargando servicio...</h4>
                    <p class="text-sm text-indigo-700" id="checkout-cliente">Cargando cliente...</p>
                </div>
                <div class="text-right">
                    <span class="block text-xs text-indigo-500 uppercase font-bold tracking-wider">Costo Servicio</span>
                    <span class="text-xl font-bold text-indigo-900">S/. <span
                            id="checkout-precio-base">0.00</span></span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Agregar Productos (Stock disponible)</label>
                <div class="flex gap-2">
                    <select id="checkout-select-prod"
                        class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 outline-none transition-colors text-sm bg-white">
                        <option value="">Seleccione producto...</option>
                        <?php foreach ($productos as $prod): ?>
                            <option value="<?= $prod->id ?>" data-precio="<?= $prod->precio ?>"
                                data-nombre="<?= htmlspecialchars($prod->nombre) ?>" data-stock="<?= $prod->stock ?>"
                                <?= $prod->stock <= 0 ? 'disabled' : '' ?>>
                                <?= htmlspecialchars($prod->nombre) ?>
                                (Stock: <?= $prod->stock ?>) - S/. <?= number_format($prod->precio, 2) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" onclick="addCheckoutProduct()"
                        class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-black transition shadow-sm flex items-center font-medium">
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Agregar
                    </button>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-500 font-semibold border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-2 w-2/3">Concepto</th>
                            <th class="px-4 py-2 text-right">Precio</th>
                            <th class="px-4 py-2 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody id="checkout-cart-items" class="divide-y divide-gray-100 bg-white">
                    </tbody>
                </table>
            </div>

        </div>

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-b-xl">
            <div class="flex justify-between items-center mb-4">
                <span class="text-lg font-medium text-gray-600">Total a Pagar:</span>
                <span class="text-3xl font-bold text-gray-900">S/. <span id="checkout-total">0.00</span></span>
            </div>

            <form action="<?= BASE_URL ?>/index.php?url=Reservation/finalizarVenta" method="POST" id="form-checkout">
                <input type="hidden" name="reserva_id" id="checkout-reserva-id">
                <input type="hidden" name="productos_data" id="checkout-productos-json">

                <button type="button" onclick="submitSale()"
                    class="w-full bg-green-600 text-white py-3 rounded-lg font-bold text-lg hover:bg-green-700 shadow-lg transition-transform active:scale-[0.98] flex justify-center items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Confirmar Pago y Generar Boleta
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // --- LÓGICA DEL CHECKOUT ---
    let checkoutCart = [];
    let baseServicePrice = 0;

    /**
     * Abre el modal y carga los datos de la reserva
     */

    function openCheckoutModal(reserva) {
        // 1. Resetear variables
        checkoutCart = [];
        document.getElementById('checkout-select-prod').value = "";

        // 2. Llenar datos visuales
        document.getElementById('checkout-reserva-id').value = reserva.id;
        document.getElementById('checkout-cliente').textContent = "Cliente: " + (reserva.cliente_nombre || "Cliente General");

        // Mostrar todos los servicios concatenados (ej: "Corte, Barba")
        // Si usaste el GROUP_CONCAT en el modelo, el campo se llama 'servicios_nombres'
        const serviciosTexto = reserva.servicios_nombres || reserva.servicio_nombre || "Servicios varios";
        document.getElementById('checkout-servicio').textContent = "Servicios: " + serviciosTexto;

        // 3. PRECIO BASE (AQUÍ ESTÁ LA CORRECCIÓN)
        // Intentamos leer el total sumado, si no existe, usamos el precio individual, si no, 0.
        baseServicePrice = parseFloat(reserva.precio_total_estimado || reserva.servicio_precio || 0);

        document.getElementById('checkout-precio-base').textContent = baseServicePrice.toFixed(2);

        // 4. Renderizar y Mostrar
        renderCheckoutCart();

        // RESETEAR TEXTOS DEL SELECT
        const select = document.getElementById('checkout-select-prod');
        for (let i = 0; i < select.options.length; i++) {
            const opt = select.options[i];
            if (opt.value) { // Ignorar el "Seleccione..."
                const stock = opt.getAttribute('data-stock');
                const nombre = opt.getAttribute('data-nombre');
                const precio = parseFloat(opt.getAttribute('data-precio') || 0);

                // Restaurar texto original
                opt.text = `${nombre} (Stock: ${stock}) - S/. ${precio.toFixed(2)}`;
                opt.disabled = (parseInt(stock) <= 0);
            }
        }
        document.getElementById('checkoutModal').classList.remove('hidden');
    }

    function closeCheckoutModal() {
        document.getElementById('checkoutModal').classList.add('hidden');
    }

    /**
     * Agrega un producto del select al carrito temporal
     */
    function addCheckoutProduct() {
        const select = document.getElementById('checkout-select-prod');

        if (!select.value) return;

        const option = select.options[select.selectedIndex];
        const id = select.value;
        const nombre = option.getAttribute('data-nombre');
        const precio = parseFloat(option.getAttribute('data-precio'));
        // Leemos el stock ACTUALIZADO del atributo (no del texto)
        let currentStock = parseInt(option.getAttribute('data-stock') || 0);

        // 1. VALIDACIÓN (Ya la tenías)
        // Contamos cuántos de estos ya están en el carrito
        const cantidadEnCarrito = checkoutCart.filter(item => item.id === id).length;

        if (cantidadEnCarrito + 1 > currentStock) {
            alert(`¡Stock insuficiente! Solo quedan ${currentStock} unidades.`);
            return;
        }

        // 2. AGREGAR AL CARRITO
        checkoutCart.push({ id, nombre, precio });

        // 3. --- MAGIA VISUAL: ACTUALIZAR EL TEXTO DEL SELECT ---
        // Calculamos cuánto stock visual queda
        const stockRestante = currentStock - (cantidadEnCarrito + 1);

        // Actualizamos el texto visible
        option.text = `${nombre} (Stock: ${stockRestante}) - S/. ${precio.toFixed(2)}`;

        // Si llega a 0, podrías deshabilitarlo visualmente (opcional)
        if (stockRestante === 0) {
            option.text = `⛔ ${nombre} (AGOTADO) - S/. ${precio.toFixed(2)}`;
            // option.disabled = true; // Descomenta si quieres bloquearlo totalmente
        }

        // Reiniciamos el select y renderizamos
        renderCheckoutCart();
        select.value = "";
    }

    /**
     * Elimina un producto del carrito temporal por su índice
     */
    function removeCheckoutProduct(index) {
        // 1. Identificar qué producto estamos borrando ANTES de sacarlo
        const prodId = checkoutCart[index].id;

        // 2. Sacarlo del carrito
        checkoutCart.splice(index, 1);
        renderCheckoutCart();

        // 3. --- MAGIA VISUAL: DEVOLVER EL STOCK AL SELECT ---
        const select = document.getElementById('checkout-select-prod');
        // Buscar la opción que corresponde a este ID
        for (let i = 0; i < select.options.length; i++) {
            const option = select.options[i];
            if (option.value === prodId) {

                // Recalculamos
                const stockOriginal = parseInt(option.getAttribute('data-stock'));
                const nombre = option.getAttribute('data-nombre');
                const precio = parseFloat(option.getAttribute('data-precio'));

                // Cuántos quedan ahora en el carrito?
                const enCarrito = checkoutCart.filter(item => item.id === prodId).length;
                const stockVisual = stockOriginal - enCarrito;

                // Restaurar texto
                option.text = `${nombre} (Stock: ${stockVisual}) - S/. ${precio.toFixed(2)}`;
                option.disabled = false; // Rehabilitar por si estaba agotado
                break;
            }
        }
    }

    /**
     * Dibuja la tabla HTML y actualiza el total
     */
    function renderCheckoutCart() {
        const tbody = document.getElementById('checkout-cart-items');
        tbody.innerHTML = '';

        // 1. Fila Fija: El Servicio Base (No se puede borrar aquí)
        tbody.innerHTML += `
            <tr class="bg-gray-50 text-gray-500">
                <td class="px-4 py-3 font-medium flex items-center">
                    <span class="w-2 h-2 bg-indigo-400 rounded-full mr-2"></span>
                    Servicio Base
                </td>
                <td class="px-4 py-3 text-right font-medium">S/. ${baseServicePrice.toFixed(2)}</td>
                <td></td>
            </tr>
        `;

        // 2. Filas Dinámicas: Productos Agregados
        let total = baseServicePrice;

        checkoutCart.forEach((item, index) => {
            total += item.precio;
            tbody.innerHTML += `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-2 text-gray-700 font-medium">+ ${item.nombre}</td>
                    <td class="px-4 py-2 text-right text-gray-600">S/. ${item.precio.toFixed(2)}</td>
                    <td class="px-4 py-2 text-center">
                        <button onclick="removeCheckoutProduct(${index})" class="text-red-400 hover:text-red-600 p-1 rounded-full hover:bg-red-50 transition" title="Quitar">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </td>
                </tr>
            `;
        });

        // 3. Actualizar el Total Grande
        document.getElementById('checkout-total').textContent = total.toFixed(2);
    }

    /**
     * Envía el formulario
     */
    function submitSale() {
        // Serializar el carrito a JSON para enviarlo al PHP
        document.getElementById('checkout-productos-json').value = JSON.stringify(checkoutCart);

        // Confirmación final
        if (confirm('¿Confirmar cobro y finalizar reserva?')) {
            document.getElementById('form-checkout').submit();
        }
    }

    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', function (e) {
        if (e.target == document.getElementById('checkoutModal')) closeCheckoutModal();
    });
</script>