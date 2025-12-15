<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Cliente: <?= htmlspecialchars($cliente->nombre) ?> | Masuno Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
</head>
<body class="flex min-h-screen bg-gray-100">
  <?php include __DIR__ . '/../partials/sidebar.php'; ?>

  <main class="flex-1 ml-64 p-8 overflow-auto">
    <h1 class="text-2xl font-bold mb-2"><?= htmlspecialchars($cliente->nombre) ?></h1>
    <p class="mb-6 text-gray-700">Email: <?= htmlspecialchars($cliente->email) ?></p>

    <h2 class="text-xl font-semibold mb-4">Reservas</h2>
    <?php if (empty($reservas)): ?>
      <p class="text-gray-600">Este cliente no tiene reservas.</p>
    <?php else: ?>
      <table class="w-full bg-white shadow rounded">
        <thead class="bg-gray-200">
          <tr>
            <th class="px-4 py-2">Servicio</th>
            <th class="px-4 py-2">Fecha</th>
            <th class="px-4 py-2">Hora Inicio</th>
            <th class="px-4 py-2">Hora Fin</th>
            <th class="px-4 py-2">Estado</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($reservas as $r): ?>
          <tr class="hover:bg-gray-50">
            <td class="border px-4 py-2"><?= htmlspecialchars($r->servicio_nombre) ?></td>
            <td class="border px-4 py-2"><?= $r->fecha ?></td>
            <td class="border px-4 py-2"><?= $r->hora_inicio ?></td>
            <td class="border px-4 py-2"><?= $r->hora_fin ?></td>
            <td class="border px-4 py-2"><?= htmlspecialchars($r->estado) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </main>
</body>
</html>
