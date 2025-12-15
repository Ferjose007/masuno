<!-- app/views/partials/sidebar.php -->
<aside class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg flex flex-col justify-between">
    <div class="p-6">
        <img src="<?= BASE_URL ?>/assets/logo.png" alt="Masuno" class="h-12 mx-auto mb-6">
        <nav class="space-y-2">
            <a href="<?= BASE_URL ?>/index.php?url=Admin/dashboard"
                class="flex items-center px-4 py-2 rounded-lg hover:bg-indigo-100 hover:text-indigo-700">
                <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 3l7 6v8a1 1 0 0 1-1 1h-4V9H8v9H4a1 1 0 0 1-1-1V9l7-6z" />
                </svg>
                Inicio
            </a>
            <a href="<?= BASE_URL ?>/index.php?url=Service/index"
                class="flex items-center px-4 py-2 rounded-lg hover:bg-indigo-100 hover:text-indigo-700">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M3 7h18M3 12h18M3 17h18" />
                </svg>
                Servicios
            </a>
            <a href="<?= BASE_URL ?>/index.php?url=Client/index"
                class="flex items-center px-4 py-2 rounded-lg hover:bg-indigo-100 hover:text-indigo-700">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M5 13l4 4L19 7" />
                </svg>
                Clientes
            </a>
            <a href="<?= BASE_URL ?>/index.php?url=AdminStylist/index"
                class="flex items-center px-4 py-2 rounded-lg hover:bg-indigo-100 hover:text-indigo-700">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M5.121 17.804A2 2 0 0 0 7 21h10a2 2 0 0 0 1.879-3.196l-5-8.5a2 2 0 0 0-3.758 0l-5 8.5z" />
                </svg>
                Estilistas
            </a>
            <a href="<?= BASE_URL ?>/index.php?url=Reservation/my"
                class="flex items-center px-4 py-2 rounded-lg hover:bg-indigo-100 hover:text-indigo-700">
                <!-- Calendario icono para Reservas -->
                <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM5 20V9h14v11H5z" />
                    <path d="M7 11h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2zm-8 4h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2z" />
                </svg>
                Reservas
            </a>
            <a href="<?= BASE_URL ?>/index.php?url=Horario/index"
                class="flex items-center px-4 py-2 rounded-lg hover:bg-indigo-100 hover:text-indigo-700">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M8 7V3M16 7V3M3 11h18M5 21h14a2 2 0 0 0 2-2V11H3v8a2 2 0 0 0 2 2z" />
                </svg>
                Horarios
            </a>
            <!-- Más módulos aquí -->
        </nav>
    </div>
    <div class="p-6">
        <form action="<?= BASE_URL ?>/index.php" method="get">
            <input type="hidden" name="url" value="Auth/logout">
            <button type="submit"
                class="w-full text-center bg-red-600 text-white font-semibold py-2 rounded hover:bg-red-700 transition">
                Cerrar sesión
            </button>
        </form>
    </div>
</aside>