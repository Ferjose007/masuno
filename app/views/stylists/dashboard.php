<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Estilista | Masuno</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
</head>
<body class="flex min-h-screen bg-gray-100">

  <?php include __DIR__ . '/../partials/stylist_nav.php'; ?>

  <main class="flex-1 ml-64 p-8 overflow-auto">
    <div class="space-y-6">
      <div class="bg-white p-6 rounded-lg shadow">
        <h1 class="text-3xl font-bold">¡Hola, <?= htmlspecialchars($nombre) ?>!</h1>
        <p class="mt-2 text-gray-700">
          Tienes <span class="font-semibold"><?= $count ?></span> cita<?= $count !== 1 ? 's' : '' ?> próximas.
        </p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-semibold mb-4">Próximas Citas</h2>
        <?php if (empty($next5)): ?>
          <p class="text-gray-600">No hay citas agendadas.</p>
        <?php else: ?>
          <table class="w-full">
            <thead class="bg-gray-200">
              <tr>
                <th class="px-4 py-2 text-left">Cliente</th>
                <th class="px-4 py-2 text-left">Servicio</th>
                <th class="px-4 py-2 text-left">Fecha</th>
                <th class="px-4 py-2 text-left">Hora</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($next5 as $r): ?>
              <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-2"><?= htmlspecialchars($r->cliente_nombre) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($r->servicio_nombre) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($r->fecha) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($r->hora_inicio) ?>–<?= htmlspecialchars($r->hora_fin) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
        <div class="mt-4 text-right">
          <a href="/masuno/public/index.php?url=Stylist/appointments"
             class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            Ver todas las citas →
          </a>
        </div>
      </div>
    </div>
  </main>

</body>
</html>
