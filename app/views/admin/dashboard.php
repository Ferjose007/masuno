<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin | Masuno</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
</head>

<body class="flex min-h-screen bg-gray-100">

    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <!-- Contenido Principal -->
    <main class="flex-1 ml-64 p-8 overflow-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">
            Bienvenido, <?= htmlspecialchars($_SESSION['user']['nombre']) ?>
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6"></div>

        <!-- Card de Servicios -->
        <div class="mt-8 bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Servicios</h2>
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b">
                        <th class="pb-2">Nombre</th>
                        <th class="pb-2">Duración</th>
                        <th class="pb-2">Precio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($servicios as $s): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2"><?= htmlspecialchars($s->nombre) ?></td>
                            <td class="py-2"><?= $s->duracion_minutes ?> min</td>
                            <td class="py-2">S/. <?= number_format($s->precio, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="<?= BASE_URL ?>/index.php?url=Service/index"
                class="mt-4 inline-block text-indigo-600 hover:underline">
                Ir a Gestión completa de Servicios →
            </a>
        </div>

        <!-- Card de Horarios -->
        <div class="mt-8 bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Próximos Horarios</h2>
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b">
                        <th class="pb-2">Fecha</th>
                        <th class="pb-2">Inicio</th>
                        <th class="pb-2">Fin</th>
                        <th class="pb-2">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($horarios as $h): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2"><?= htmlspecialchars($h->fecha) ?></td>
                            <td class="py-2"><?= htmlspecialchars($h->hora_inicio) ?></td>
                            <td class="py-2"><?= htmlspecialchars($h->hora_fin) ?></td>
                            <td class="py-2"><?= htmlspecialchars($h->estado_desc) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="<?= BASE_URL ?>/index.php?url=Horario/index"
                class="mt-4 inline-block text-indigo-600 hover:underline">
                Ver todos los Horarios →
            </a>
        </div>

        <!-- Card de Clientes -->
        <div class="mt-8 bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Clientes</h2>
            <p class="text-3xl font-bold mb-4"><?= count($clientes) ?></p>
            <a href="<?= BASE_URL ?>/index.php?url=Client/index"
                class="inline-block text-indigo-600 hover:underline">
                Ver todos los Clientes →
            </a>
        </div>

        <div class="mt-8 bg-white p-6 rounded-lg shadow">
            <h3 class="text-xl font-semibold mb-4">Últimas Reservas</h3>
            <table class="w-full text-left">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2">Cliente</th>
                        <th class="px-4 py-2">Servicio</th>
                        <th class="px-4 py-2">Fecha</th>
                        <th class="px-4 py-2">Hora</th>
                        <th class="px-4 py-2">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($reservas, 0, 5) as $r): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2"><?= htmlspecialchars($r->cliente_nombre) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($r->servicio_nombre) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($r->fecha) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($r->hora_inicio) ?>–<?= htmlspecialchars($r->hora_fin) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($r->estado) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        </div>
    </main>

</body>

</html>