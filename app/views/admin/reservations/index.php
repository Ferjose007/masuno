<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reservas | Masuno Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_URL ?>/assets/favicon-32x32.png">
</head>

<body class="bg-gray-50 min-h-screen flex flex-col md:flex-row">

    <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

    <main class="flex-1 w-full ml-0 md:ml-64 transition-all duration-300">

        <button id="openSidebar" class="md:hidden fixed top-1/2 left-0 z-40 transform -translate-y-1/2 bg-indigo-600 text-white p-3 pr-4 rounded-r-2xl shadow-lg opacity-50 hover:opacity-100 transition-all duration-300 focus:outline-none hover:shadow-indigo-500/50">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
            </svg>
        </button>

        <div class="p-4 md:p-8">

            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Reservas</h1>
                    <p class="text-gray-500 mt-1">Agenda y control de citas.</p>
                </div>
                <button onclick="openModal('create')"
                    class="inline-flex items-center justify-center bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition shadow-sm cursor-pointer">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Nueva Reserva
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[600px]">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                <th class="px-6 py-4 font-semibold">Cliente</th>
                                <th class="px-6 py-4 font-semibold">Servicio</th>
                                <th class="px-6 py-4 font-semibold">Fecha y Hora</th>
                                <th class="px-6 py-4 font-semibold">Estado</th>
                                <th class="px-6 py-4 font-semibold text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($reservas)): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">No hay reservas registradas.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($reservas as $r): ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 font-medium text-gray-800">
                                            <?= htmlspecialchars($r->cliente_nombre) ?>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600">
                                            <?= htmlspecialchars($r->servicio_nombre) ?>
                                            <div class="text-xs text-gray-400">S/. <?= $r->servicio_precio ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-800">
                                                <?= date('d/m/Y', strtotime($r->fecha_cita)) ?>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <?= date('h:i A', strtotime($r->hora_cita)) ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php
                                            $colors = [
                                                'pendiente'  => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                                                'confirmada' => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                                                'en_proceso' => 'bg-purple-100 text-purple-800 border border-purple-200 animate-pulse', // animate-pulse le da un efecto de latido suave
                                                'completada' => 'bg-green-100 text-green-800 border border-green-200',
                                                'cancelada'  => 'bg-red-50 text-red-800 border border-red-100 line-through opacity-75',
                                            ];
                                            $badgeColor = $colors[$r->estado] ?? 'bg-gray-100 text-gray-800';
                                            // Texto amigable para "en_proceso"
                                            $textoEstado = $r->estado === 'en_proceso' ? 'En Proceso' : ucfirst($r->estado);
                                            ?>
                                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide <?= $badgeColor ?>">
                                                <?= $textoEstado ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-1 flex justify-end items-center">

                                            <button onclick='openViewModal(<?= json_encode($r) ?>)'
                                                class="p-1 text-gray-400 hover:text-gray-600 transition-colors" title="Ver Detalles">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>

                                            <?php if ($r->estado === 'pendiente'): ?>

                                                <button onclick="changeStatus(<?= $r->id ?>, 'confirmada')"
                                                    class="p-1 text-indigo-600 hover:text-indigo-900 transition-colors" title="Avanzar: Confirmar Cita">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </button>

                                                <button onclick="openCancelModal(<?= $r->id ?>)" class="p-1 text-amber-600 hover:text-amber-900" title="Cancelar">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                </button>

                                            <?php elseif ($r->estado === 'confirmada'): ?>

                                                <button onclick="changeStatus(<?= $r->id ?>, 'pendiente')"
                                                    class="p-1 text-gray-400 hover:text-gray-600 transition-colors" title="Retroceder: Volver a Pendiente">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                    </svg>
                                                </button>

                                                <button onclick="changeStatus(<?= $r->id ?>, 'en_proceso')"
                                                    class="p-1 text-purple-600 hover:text-purple-900 transition-colors" title="Avanzar: Iniciar Atención">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </button>

                                                <button onclick="openCancelModal(<?= $r->id ?>)" class="p-1 text-amber-600 hover:text-amber-900" title="Cancelar">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                </button>

                                            <?php elseif ($r->estado === 'en_proceso'): ?>

                                                <button onclick="changeStatus(<?= $r->id ?>, 'confirmada')"
                                                    class="p-1 text-gray-400 hover:text-gray-600 transition-colors" title="Retroceder: Volver a Confirmada">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                    </svg>
                                                </button>

                                                <button onclick='openCheckoutModal(<?= json_encode($r) ?>)'
                                                    class="p-1 text-green-600 hover:text-green-900 transition-colors" title="Finalizar: Cobrar y Cerrar">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </button>

                                                <button onclick="openCancelModal(<?= $r->id ?>)" class="p-1 text-amber-600 hover:text-amber-900" title="Cancelar">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                </button>

                                            <?php elseif ($r->estado === 'completada'): ?>

                                                <button onclick="changeStatus(<?= $r->id ?>, 'en_proceso')"
                                                    class="p-1 text-gray-400 hover:text-gray-600 transition-colors" title="Retroceder: Volver a En Proceso">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                    </svg>
                                                </button>

                                                <button onclick="openCancelModal(<?= $r->id ?>)" class="p-1 text-amber-600 hover:text-amber-900" title="Cancelar / Reembolsar">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                </button>

                                            <?php elseif ($r->estado === 'cancelada'): ?>

                                                <button onclick="changeStatus(<?= $r->id ?>, 'pendiente')"
                                                    class="p-1 text-blue-500 hover:text-blue-700 transition-colors" title="Reactivar (Volver a Pendiente)">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                </button>

                                            <?php endif; ?>

                                            <button onclick="openDeleteModal(<?= $r->id ?>)" class="p-1 text-red-300 hover:text-red-600 transition-colors" title="Borrar de BD">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
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

    <div id="form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-xl">
                <h2 id="modal-title" class="text-xl font-bold text-gray-800">Nueva Reserva</h2>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg></button>
            </div>
            <form id="reserva-form" action="" method="post" class="p-6 space-y-4">
                <input type="hidden" name="id" id="reserva-id">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                    <select name="usuario_id" id="reserva-cliente" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white" required>
                        <option value="">Seleccione un cliente...</option>
                        <?php foreach ($clientes as $c): ?>
                            <option value="<?= $c->id ?>"><?= htmlspecialchars($c->nombre) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Servicio</label>
                    <select name="servicio_id" id="reserva-servicio" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white" required>
                        <option value="">Seleccione un servicio...</option>
                        <?php foreach ($servicios as $s): ?>
                            <option value="<?= $s->id ?>"><?= htmlspecialchars($s->nombre) ?> - S/.<?= $s->precio ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                        <input type="date" name="fecha_cita" id="reserva-fecha" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora</label>
                        <input type="time" name="hora_cita" id="reserva-hora" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas Adicionales</label>
                    <textarea name="notas" id="reserva-notas" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2"></textarea>
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-gray-50 mt-4">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-md">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="view-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md mx-4">
            <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Detalle de la Reserva</h3>
            <div class="space-y-3 text-sm">
                <p><span class="font-bold text-gray-700">Cliente:</span> <span id="view-cliente" class="text-gray-600"></span></p>
                <p><span class="font-bold text-gray-700">Servicio:</span> <span id="view-servicio" class="text-gray-600"></span></p>
                <p><span class="font-bold text-gray-700">Precio:</span> S/. <span id="view-precio" class="text-gray-600"></span></p>
                <p><span class="font-bold text-gray-700">Fecha:</span> <span id="view-fecha-full" class="text-gray-600"></span></p>
                <p><span class="font-bold text-gray-700">Estado:</span> <span id="view-estado" class="font-semibold uppercase"></span></p>
                <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-100 mt-2">
                    <span class="font-bold text-yellow-800 block text-xs mb-1">NOTAS:</span>
                    <p id="view-notas" class="text-gray-700 italic">-</p>
                </div>
            </div>
            <div class="mt-6 text-right"><button onclick="closeViewModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Cerrar</button></div>
        </div>
    </div>

    <div id="cancel-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-96 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 mb-4">
                <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900">¿Cancelar Cita?</h3>
            <p class="mt-2 text-sm text-gray-500">Pasará a estado "Cancelada".</p>
            <div class="mt-5 flex justify-center gap-3">
                <button onclick="closeCancelModal()" class="px-4 py-2 bg-gray-100 rounded-lg">Volver</button>
                <button id="confirm-cancel-btn" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700">Sí, Cancelar</button>
            </div>
        </div>
    </div>

    <div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-96 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900">¿Eliminar registro?</h3>
            <p class="mt-2 text-sm text-gray-500">Desaparecerá de la base de datos.</p>
            <div class="mt-5 flex justify-center gap-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-100 rounded-lg">Cancelar</button>
                <button id="confirm-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg">Eliminar</button>
            </div>
        </div>
    </div>

    <div id="checkout-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl transform transition-all scale-100 mx-4 flex flex-col max-h-[90vh]">

            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-xl">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Finalizar Venta
                </h2>
                <button onclick="closeCheckoutModal()" class="text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-6">

                <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100 flex justify-between items-center">
                    <div>
                        <h4 class="font-bold text-indigo-900" id="checkout-servicio">Servicio...</h4>
                        <p class="text-sm text-indigo-700" id="checkout-cliente">Cliente...</p>
                    </div>
                    <div class="text-right">
                        <span class="block text-xs text-indigo-500 uppercase font-bold tracking-wider">Costo Servicio</span>
                        <span class="text-xl font-bold text-indigo-900">S/. <span id="checkout-precio-base">0.00</span></span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Agregar Productos Adicionales</label>
                    <div class="flex gap-2">
                        <select id="select-producto" class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 outline-none transition-colors text-sm">
                            <option value="">Seleccionar producto del inventario...</option>
                            <?php
                            // Asegúrate de tener disponible la clase Producto o pasarlos desde el controlador
                            $allProducts = \App\Models\Producto::getActive();
                            foreach ($allProducts as $prod):
                            ?>
                                <option value="<?= $prod->id ?>" data-precio="<?= $prod->precio ?>" data-nombre="<?= $prod->nombre ?>">
                                    <?= htmlspecialchars($prod->nombre) ?> - S/. <?= $prod->precio ?> (Stock: <?= $prod->stock ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" onclick="addProductToCart()" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-black transition shadow-sm flex items-center">
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
                                <th class="px-4 py-2">Ítem</th>
                                <th class="px-4 py-2 text-right">Precio</th>
                                <th class="px-4 py-2 text-center w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="cart-items" class="divide-y divide-gray-100 bg-white">
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-b-xl">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-lg font-medium text-gray-600">Total a Cobrar:</span>
                    <span class="text-3xl font-bold text-gray-900">S/. <span id="checkout-total">0.00</span></span>
                </div>

                <form action="<?= BASE_URL ?>/index.php?url=Reservation/finalizarVenta" method="POST" id="form-checkout">
                    <input type="hidden" name="reserva_id" id="checkout-reserva-id">
                    <input type="hidden" name="productos_data" id="checkout-productos-json">

                    <button type="button" onclick="submitSale()" class="w-full bg-green-600 text-white py-3 rounded-lg font-bold text-lg hover:bg-green-700 shadow-md transition-transform active:scale-95 flex justify-center items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Confirmar Pago y Finalizar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // --- LOGICA DE CAJA / CHECKOUT ---
        let cart = [];
        let baseServicePrice = 0;

        function openCheckoutModal(reserva) {
            // 1. Limpiar
            cart = [];
            document.getElementById('select-producto').value = "";

            // 2. Llenar datos de reserva
            document.getElementById('checkout-reserva-id').value = reserva.id;
            document.getElementById('checkout-cliente').textContent = "Cliente: " + reserva.cliente_nombre;
            document.getElementById('checkout-servicio').textContent = "Servicio: " + reserva.servicio_nombre;

            // 3. Precio Base (Asegúrate que tu controlador mande 'servicio_precio')
            baseServicePrice = parseFloat(reserva.servicio_precio || 0);

            renderCart(); // Dibujar tabla inicial
            document.getElementById('checkout-modal').classList.remove('hidden');
        }

        function closeCheckoutModal() {
            document.getElementById('checkout-modal').classList.add('hidden');
        }

        function addProductToCart() {
            const select = document.getElementById('select-producto');
            const option = select.options[select.selectedIndex];

            if (!select.value) return; // Si no eligió nada, salir

            const id = select.value;
            const nombre = option.getAttribute('data-nombre');
            const precio = parseFloat(option.getAttribute('data-precio'));

            // Agregamos al carrito
            cart.push({
                id,
                nombre,
                precio
            });

            renderCart();
            select.value = ""; // Reset del select
        }

        function removeProduct(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function renderCart() {
            const tbody = document.getElementById('cart-items');
            tbody.innerHTML = '';

            // 1. Fila obligatoria del servicio
            tbody.innerHTML += `
        <tr class="bg-gray-50 text-gray-500">
            <td class="px-4 py-2 font-medium">Servicio Base</td>
            <td class="px-4 py-2 text-right">S/. ${baseServicePrice.toFixed(2)}</td>
            <td></td>
        </tr>
    `;

            // 2. Filas de productos
            let total = baseServicePrice;

            cart.forEach((item, index) => {
                total += item.precio;
                tbody.innerHTML += `
            <tr>
                <td class="px-4 py-2 text-gray-700">+ ${item.nombre}</td>
                <td class="px-4 py-2 text-right">S/. ${item.precio.toFixed(2)}</td>
                <td class="px-4 py-2 text-center">
                    <button onclick="removeProduct(${index})" class="text-red-400 hover:text-red-600 font-bold px-2">×</button>
                </td>
            </tr>
        `;
            });

            // Actualizar Total Grande
            document.getElementById('checkout-total').textContent = total.toFixed(2);
        }

        function submitSale() {
            if (!confirm('¿Confirmar que se recibió el pago? Esto finalizará la cita.')) return;

            // Guardar array en input hidden para enviarlo a PHP
            document.getElementById('checkout-productos-json').value = JSON.stringify(cart);
            document.getElementById('form-checkout').submit();
        }
    </script>

    <script>
        const btnOpen = document.getElementById('openSidebar');
        const btnClose = document.getElementById('closeSidebar');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        if (btnOpen) btnOpen.addEventListener('click', toggleSidebar);
        if (btnClose) btnClose.addEventListener('click', toggleSidebar);
        if (overlay) overlay.addEventListener('click', toggleSidebar);
    </script>

    <script>
        const baseUrl = "<?= BASE_URL ?>";

        // --- VIEW MODAL ---
        function openViewModal(r) {
            document.getElementById('view-cliente').textContent = r.cliente_nombre;
            document.getElementById('view-servicio').textContent = r.servicio_nombre;
            document.getElementById('view-precio').textContent = r.servicio_precio;
            document.getElementById('view-fecha-full').textContent = `${r.fecha_cita} a las ${r.hora_cita}`;
            document.getElementById('view-estado').textContent = r.estado;
            document.getElementById('view-notas').textContent = r.notas || 'Sin notas';
            document.getElementById('view-modal').classList.remove('hidden');
        }

        function closeViewModal() {
            document.getElementById('view-modal').classList.add('hidden');
        }

        // --- FORM MODAL ---
        function openModal(mode, data = null) {
            const modal = document.getElementById('form-modal');
            const form = document.getElementById('reserva-form');
            const title = document.getElementById('modal-title');

            modal.classList.remove('hidden');

            if (mode === 'create') {
                title.textContent = 'Nueva Reserva';
                form.action = `${baseUrl}/index.php?url=Reservation/store`;
                form.reset();
                document.getElementById('reserva-id').value = '';
            } else {
                title.textContent = 'Reprogramar Reserva';
                form.action = `${baseUrl}/index.php?url=Reservation/update`;

                document.getElementById('reserva-id').value = data.id;
                document.getElementById('reserva-cliente').value = data.usuario_id;
                document.getElementById('reserva-servicio').value = data.servicio_id;
                document.getElementById('reserva-fecha').value = data.fecha_cita;
                document.getElementById('reserva-hora').value = data.hora_cita; // Asegúrate formato HH:MM
                document.getElementById('reserva-notas').value = data.notas;
            }
        }

        function closeModal() {
            document.getElementById('form-modal').classList.add('hidden');
        }

        // --- STATUS ACTIONS ---
        function changeStatus(id, status) {
            window.location.href = `${baseUrl}/index.php?url=Reservation/changeStatus&id=${id}&status=${status}`;
        }

        // --- CANCEL MODAL ---
        let cancelId = null;

        function openCancelModal(id) {
            cancelId = id;
            document.getElementById('cancel-modal').classList.remove('hidden');
        }

        function closeCancelModal() {
            cancelId = null;
            document.getElementById('cancel-modal').classList.add('hidden');
        }

        document.getElementById('confirm-cancel-btn').addEventListener('click', function() {
            if (cancelId) changeStatus(cancelId, 'cancelada');
        });

        // --- DELETE MODAL ---
        let deleteId = null;

        function openDeleteModal(id) {
            deleteId = id;
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            deleteId = null;
            document.getElementById('delete-modal').classList.add('hidden');
        }

        document.getElementById('confirm-delete-btn').addEventListener('click', function() {
            if (deleteId) window.location.href = `${baseUrl}/index.php?url=Reservation/delete&id=${deleteId}`;
        });

        // Close on click outside
        window.onclick = function(e) {
            if (e.target == document.getElementById('form-modal')) closeModal();
            if (e.target == document.getElementById('view-modal')) closeViewModal();
            if (e.target == document.getElementById('cancel-modal')) closeCancelModal();
            if (e.target == document.getElementById('delete-modal')) closeDeleteModal();
        }
    </script>
</body>

</html>