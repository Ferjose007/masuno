<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin | Masuno</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_URL ?>/assets/favicon-32x32.png">
</head>

<body class="bg-gray-50 min-h-screen">

    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="ml-64 p-8 w-auto">
    
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Panel de Control</h1>
            <p class="text-gray-500">Bienvenido de nuevo, aquí tienes el resumen de hoy.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Citas para Hoy</p>
                    <h3 class="text-2xl font-bold text-gray-800"><?= $stats['citas_hoy'] ?></h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Ingresos (Mes)</p>
                    <h3 class="text-2xl font-bold text-gray-800">$<?= number_format($stats['ingresos_mes'], 2) ?></h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Clientes Registrados</p>
                    <h3 class="text-2xl font-bold text-gray-800"><?= $stats['total_clientes'] ?></h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
                <div class="p-3 rounded-full bg-pink-100 text-pink-600 mr-4">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Estilistas</p>
                    <h3 class="text-2xl font-bold text-gray-800"><?= $stats['total_stylists'] ?></h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">Próximas Citas</h2>
                <a href="<?= BASE_URL ?>/index.php?url=Reservation/index" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Ver todas →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-gray-500 text-sm uppercase bg-gray-50 border-b">
                            <th class="px-6 py-3 font-medium">Cliente</th>
                            <th class="px-6 py-3 font-medium">Servicio</th>
                            <th class="px-6 py-3 font-medium">Fecha y Hora</th>
                            <th class="px-6 py-3 font-medium">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($upcoming_appointments)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">
                                    No hay citas próximas programadas.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($upcoming_appointments as $cita): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-800">
                                        <?= htmlspecialchars($cita->cliente_nombre ?? 'Cliente') ?>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        <?= htmlspecialchars($cita->servicio_nombre ?? 'Servicio') ?>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        <div class="flex flex-col">
                                            <span class="font-medium"><?= date('d/m/Y', strtotime($cita->fecha)) ?></span>
                                            <span class="text-xs text-gray-500"><?= substr($cita->hora_inicio, 0, 5) ?> hrs</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php
                                        // Lógica simple para colores de estado
                                        $estadoColor = match ($cita->estado) {
                                            'confirmada' => 'bg-green-100 text-green-700',
                                            'pendiente' => 'bg-yellow-100 text-yellow-700',
                                            'cancelada' => 'bg-red-100 text-red-700',
                                            default => 'bg-gray-100 text-gray-700'
                                        };
                                        ?>
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $estadoColor ?>">
                                            <?= ucfirst($cita->estado) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</body>

</html>