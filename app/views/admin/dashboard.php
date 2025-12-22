<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control | Masuno Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_URL ?>/assets/favicon-32x32.png">
</head>
<body class="bg-gray-50 min-h-screen flex flex-col md:flex-row">

  <?php include __DIR__ . '/../partials/sidebar.php'; ?>

  <main class="flex-1 w-full ml-0 md:ml-64 transition-all duration-300">
    
    <button id="openSidebar" class="md:hidden fixed top-1/2 left-0 z-40 transform -translate-y-1/2 bg-indigo-600 text-white p-3 pr-4 rounded-r-2xl shadow-lg opacity-50 hover:opacity-100 transition-all duration-300 focus:outline-none hover:shadow-indigo-500/50">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
        </svg>
    </button>

    <div class="p-4 md:p-8">
        <div class="mb-6 md:mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Panel de Control</h1>
            <p class="text-sm md:text-base text-gray-500 mt-1">Bienvenido al sistema de gestión Masuno.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
            
            <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-indigo-50 p-3 rounded-lg text-indigo-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-xs font-bold text-gray-400 uppercase">Hoy</span>
                </div>
                <h3 class="text-2xl md:text-3xl font-bold text-gray-800"><?= $stats['citas_hoy'] ?></h3>
                <p class="text-xs md:text-sm text-gray-500 mt-1">Citas programadas</p>
            </div>

            <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-green-50 p-3 rounded-lg text-green-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded">Cerrado</span>
                </div>
                <h3 class="text-2xl md:text-3xl font-bold text-gray-800">S/. <?= number_format($stats['ingresos_hoy'], 2) ?></h3>
                <p class="text-xs md:text-sm text-gray-500 mt-1">Ingresos hoy</p>
            </div>

            <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-50 p-3 rounded-lg text-blue-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
                <h3 class="text-2xl md:text-3xl font-bold text-gray-800"><?= $stats['total_clientes'] ?></h3>
                <p class="text-xs md:text-sm text-gray-500 mt-1">Clientes totales</p>
            </div>

            <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-50 p-3 rounded-lg text-purple-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                    </div>
                </div>
                <h3 class="text-2xl md:text-3xl font-bold text-gray-800"><?= $stats['total_stylists'] ?></h3>
                <p class="text-xs md:text-sm text-gray-500 mt-1">Estilistas</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">Próximas Citas</h2>
                <a href="<?= BASE_URL ?>/index.php?url=Reservation/index" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Ver todo &rarr;</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]"> <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                            <th class="px-6 py-4 font-semibold">Cliente</th>
                            <th class="px-6 py-4 font-semibold">Servicio</th>
                            <th class="px-6 py-4 font-semibold">Horario</th>
                            <th class="px-6 py-4 font-semibold text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($upcoming)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                    No hay citas próximas programadas.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($upcoming as $r): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold mr-3">
                                            <?= strtoupper(substr($r->cliente_nombre, 0, 1)) ?>
                                        </div>
                                        <span class="font-medium text-gray-800 whitespace-nowrap"><?= htmlspecialchars($r->cliente_nombre) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 text-sm whitespace-nowrap">
                                    <?= htmlspecialchars($r->servicio_nombre) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-800">
                                        <?= date('d/m/Y', strtotime($r->fecha_cita)) ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <?= date('h:i A', strtotime($r->hora_cita)) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <?php 
                                        $colors = [
                                            'pendiente'  => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                                            'confirmada' => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                                            'en_proceso' => 'bg-purple-100 text-purple-800 border border-purple-200 animate-pulse',
                                            'completada' => 'bg-green-100 text-green-800 border border-green-200',
                                            'cancelada'  => 'bg-red-50 text-red-800 border border-red-100 line-through opacity-75',
                                        ];
                                        $badgeColor = $colors[$r->estado] ?? 'bg-gray-100 text-gray-800';
                                        $textoEstado = $r->estado === 'en_proceso' ? 'En Proceso' : ucfirst($r->estado);
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide <?= $badgeColor ?>">
                                        <?= $textoEstado ?>
                                    </span>
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

  <script>
    const btnOpen = document.getElementById('openSidebar');
    const btnClose = document.getElementById('closeSidebar');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    // Función para abrir
    btnOpen.addEventListener('click', () => {
        sidebar.classList.remove('-translate-x-full'); // Muestra sidebar
        overlay.classList.remove('hidden');            // Muestra fondo oscuro
    });

    // Función para cerrar (con botón X o clic afuera)
    const closeMenu = () => {
        sidebar.classList.add('-translate-x-full'); // Esconde sidebar
        overlay.classList.add('hidden');            // Esconde fondo
    };

    btnClose.addEventListener('click', closeMenu);
    overlay.addEventListener('click', closeMenu);

    // Eventos
    if(btnOpen) btnOpen.addEventListener('click', toggleSidebar);
    if(btnClose) btnClose.addEventListener('click', toggleSidebar);
    if(overlay) overlay.addEventListener('click', toggleSidebar);
  </script>

</body>
</html>