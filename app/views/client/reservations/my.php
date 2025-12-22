<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas | Masuno Estilistas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-indigo-600">Masuno Estilistas</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-500 text-sm">Hola, <?= htmlspecialchars($_SESSION['user']['nombre']) ?></span>
                    <a href="<?= BASE_URL ?>/index.php?url=Auth/logout" class="text-sm font-medium text-red-600 hover:text-red-800">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Mis Reservas</h1>
            </div>

        <?php if(empty($reservas)): ?>
            <div class="bg-white rounded-xl shadow-sm p-10 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">No tienes reservas</h3>
                <p class="mt-1 text-gray-500">Aún no has agendado ninguna cita con nosotros.</p>
            </div>
        <?php else: ?>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($reservas as $r): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                        <div class="p-5">
                            <div class="flex justify-between items-start mb-4">
                                <span class="px-2 py-1 rounded text-xs font-semibold uppercase 
                                    <?php 
                                        if($r->estado == 'pendiente') echo 'bg-yellow-100 text-yellow-800';
                                        elseif($r->estado == 'confirmada') echo 'bg-blue-100 text-blue-800';
                                        elseif($r->estado == 'completada') echo 'bg-green-100 text-green-800';
                                        else echo 'bg-red-100 text-red-800 line-through';
                                    ?>">
                                    <?= $r->estado ?>
                                </span>
                                <span class="text-sm text-gray-400">#<?= $r->id ?></span>
                            </div>
                            
                            <h3 class="text-lg font-bold text-gray-900 mb-1"><?= htmlspecialchars($r->servicio_nombre) ?></h3>
                            <p class="text-indigo-600 font-medium mb-4">S/. <?= number_format($r->servicio_precio, 2) ?></p>
                            
                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <?= date('d/m/Y', strtotime($r->fecha_cita)) ?>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <?= date('h:i A', strtotime($r->hora_cita)) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>