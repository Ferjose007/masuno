<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis Citas | Masuno</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
</head>
<body class="flex min-h-screen bg-gray-100">

  <?php include __DIR__ . '/../partials/stylist_nav.php'; ?>

  <main class="flex-1 ml-64 p-8 overflow-auto">
    <div class="bg-white p-6 rounded-lg shadow">
      <h2 class="text-2xl font-semibold mb-4">Todas las Citas</h2>
      <?php if (empty($reservas)): ?>
        <p class="text-gray-600">No hay citas registradas.</p>
      <?php else: ?>
        <table class="w-full">
          <thead class="bg-gray-200">
            <tr>
              <th class="px-4 py-2 text-left">Cliente</th>
              <th class="px-4 py-2 text-left">Servicio</th>
              <th class="px-4 py-2 text-left">Fecha</th>
              <th class="px-4 py-2 text-left">Hora</th>
              <th class="px-4 py-2 text-left">Estado</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reservas as $r): ?>
            <?php 
              $classes = match($r->estado) {
                'confirmada' => 'bg-green-100 text-green-800',
                'cancelada'  => 'bg-red-100 text-red-800',
                default      => 'bg-yellow-100 text-yellow-800'
              };
            ?>
            <tr class="border-b hover:bg-gray-50">
              <td class="px-4 py-2"><?= htmlspecialchars($r->cliente_nombre) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($r->servicio_nombre) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($r->fecha) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($r->hora_inicio) ?>–<?= htmlspecialchars($r->hora_fin) ?></td>
              <td class="px-4 py-2">
                <span class="px-2 py-1 rounded-full text-sm <?= $classes ?>">
                  <?= htmlspecialchars($r->estado) ?>
                </span>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </main>

</body>
</html>
