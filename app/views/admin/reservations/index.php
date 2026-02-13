<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reservas | Masuno </title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col md:flex-row">

    <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

    <main class="flex-1 w-full ml-0 md:ml-64 transition-all duration-300">
        <div class="p-4 md:p-8 pb-24">

            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Reservas</h1>
                    <p class="text-gray-500 mt-1">Agenda y control de citas.</p>
                </div>
                <button onclick="openFormModal('create')"
                    class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg hover:bg-indigo-700 transition shadow-sm font-medium flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Reserva
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[800px]">
                        <thead
                            class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Fecha/Hora</th>
                                <th class="px-6 py-4">Cliente</th>
                                <th class="px-6 py-4">Servicio</th>
                                <th class="px-6 py-4">Precio</th>
                                <th class="px-6 py-4">Estado</th>
                                <th class="px-6 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($reservas)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-gray-400">No hay reservas registradas.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($reservas as $r): ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-800">
                                                <?= date('d/m/Y', strtotime($r->fecha_cita)) ?>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <?= date('H:i A', strtotime($r->hora_cita)) ?>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 text-gray-700 font-medium">
                                            <?= htmlspecialchars($r->cliente_nombre ?? 'Cliente General') ?>
                                        </td>

                                        <td class="text-gray-600">
                                            <?= htmlspecialchars($r->servicios_nombres ?? 'Sin servicios') ?>
                                        </td>

                                        <td class="px-6 py-4 text-gray-600 font-bold">
                                            S/. <?= number_format($r->precio_total_estimado ?? 0, 2) ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php
                                            $colors = [
                                                'pendiente' => 'bg-gray-100 text-gray-600',
                                                'confirmada' => 'bg-blue-100 text-blue-700',
                                                'en_proceso' => 'bg-purple-100 text-purple-700',
                                                'completada' => 'bg-green-100 text-green-700',
                                                'cancelada' => 'bg-red-100 text-red-700'
                                            ];
                                            $clase = $colors[$r->estado] ?? 'bg-gray-100';
                                            $label = ucfirst(str_replace('_', ' ', $r->estado));
                                            ?>
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $clase ?>">
                                                <?= $label ?>
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end items-center gap-1">

                                                <?php if ($r->estado === 'pendiente'): ?>

                                                    <a href="<?= BASE_URL ?>/index.php?url=Reservation/changeStatus&id=<?= $r->id ?>&status=confirmada"
                                                        class="p-1 text-indigo-600 hover:text-indigo-900 transition-colors"
                                                        title="Confirmar Cita">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </a>

                                                    <?php
                                                    // TRUCO: Cargamos la lista de servicios 'extra' para pasársela al JS
                                                    // Esto hace una consulta extra por fila (N+1 problem), pero para empezar está bien.
                                                    // Si tienes muchas reservas, deberías optimizarlo en el SQL principal con GROUP_CONCAT.
                                                    // Llamamos a la clase Reserva directamente (método estático)
                                                    $r->servicios = \App\Models\Reserva::getServiciosPorReserva($r->id);
                                                    ?>

                                                    <button onclick='openFormModal("edit", <?= json_encode($r) ?>)'
                                                        class="p-1 text-gray-500 hover:text-indigo-600 transition" title="Editar">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>

                                                    <button
                                                        onclick="openAlert('warning', '¿Cancelar Cita?', 'La cita pasará a estado cancelado.', '<?= BASE_URL ?>/index.php?url=Reservation/changeStatus&id=<?= $r->id ?>&status=cancelada')"
                                                        class="p-1 text-amber-500 hover:text-amber-700 transition" title="Cancelar">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                        </svg>
                                                    </button>

                                                <?php elseif ($r->estado === 'confirmada'): ?>

                                                    <a href="<?= BASE_URL ?>/index.php?url=Reservation/changeStatus&id=<?= $r->id ?>&status=pendiente"
                                                        class="p-1 text-gray-400 hover:text-gray-600 transition-colors"
                                                        title="Retroceder a Pendiente">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                                                        </svg>
                                                    </a>

                                                    <a href="<?= BASE_URL ?>/index.php?url=Reservation/changeStatus&id=<?= $r->id ?>&status=en_proceso"
                                                        class="p-1 text-purple-600 hover:text-purple-900 transition-colors"
                                                        title="Iniciar Atención">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </a>

                                                    <button onclick='openFormModal("edit", <?= json_encode($r) ?>)'
                                                        class="p-1 text-gray-500 hover:text-indigo-600 transition" title="Editar">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>

                                                    <button
                                                        onclick="openAlert('warning', '¿Cancelar Cita?', 'La cita pasará a estado cancelado.', '<?= BASE_URL ?>/index.php?url=Reservation/changeStatus&id=<?= $r->id ?>&status=cancelada')"
                                                        class="p-1 text-amber-500 hover:text-amber-700 transition">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                        </svg>
                                                    </button>

                                                <?php elseif ($r->estado === 'en_proceso'): ?>

                                                    <a href="<?= BASE_URL ?>/index.php?url=Reservation/changeStatus&id=<?= $r->id ?>&status=confirmada"
                                                        class="p-1 text-gray-400 hover:text-gray-600 transition-colors"
                                                        title="Retroceder a Confirmada">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                                                        </svg>
                                                    </a>

                                                    <button onclick='openCheckoutModal(<?= json_encode($r) ?>)'
                                                        class="p-1 text-green-600 hover:text-green-800 transition-colors"
                                                        title="Finalizar y Cobrar">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </button>

                                                    <button
                                                        onclick="openAlert('warning', '¿Cancelar Cita?', 'Interrumpir el servicio y cancelar.', '<?= BASE_URL ?>/index.php?url=Reservation/changeStatus&id=<?= $r->id ?>&status=cancelada')"
                                                        class="p-1 text-amber-500 hover:text-amber-700 transition">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                        </svg>
                                                    </button>

                                                <?php elseif ($r->estado === 'completada'): ?>

                                                    <button
                                                        onclick="openAlert('warning', '¿Reabrir Cita?', 'Volverá a estado En Proceso. Útil si hubo error en el cobro.', '<?= BASE_URL ?>/index.php?url=Reservation/changeStatus&id=<?= $r->id ?>&status=en_proceso')"
                                                        class="p-1 text-gray-400 hover:text-gray-600 transition"
                                                        title="Reabrir (Volver a En Proceso)">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                        </svg>
                                                    </button>

                                                    <a href="<?= BASE_URL ?>/index.php?url=Reservation/ticket&id=<?= $r->id ?>"
                                                        target="_blank" class="p-1 text-gray-600 hover:text-gray-900 transition"
                                                        title="Ver Boleta">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2-4h6a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6a2 2 0 012-2zm9-2V3a1 1 0 00-1-1H6a1 1 0 00-1 1v4" />
                                                        </svg>
                                                    </a>

                                                <?php elseif ($r->estado === 'cancelada'): ?>

                                                    <button
                                                        onclick="openAlert('success', '¿Reactivar Cita?', 'Volverá a estado Pendiente.', '<?= BASE_URL ?>/index.php?url=Reservation/changeStatus&id=<?= $r->id ?>&status=pendiente')"
                                                        class="p-1 text-blue-500 hover:text-blue-700 transition" title="Reactivar">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                        </svg>
                                                    </button>

                                                <?php endif; ?>

                                                <button
                                                    onclick="openAlert('delete', '¿Eliminar Reserva?', 'Se borrará permanentemente de la base de datos.', '<?= BASE_URL ?>/index.php?url=Reservation/delete&id=<?= $r->id ?>')"
                                                    class="p-1 text-red-300 hover:text-red-600 transition ml-2"
                                                    title="Eliminar Definitivamente">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>

                                            </div>
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

    <?php include __DIR__ . '/../../partials/modals/alert.php'; ?>
    <?php include __DIR__ . '/partials/modal_form.php'; ?> <?php include __DIR__ . '/partials/modal_view.php'; ?>
    <?php include __DIR__ . '/partials/modal_checkout.php'; ?>
</body>

</html>