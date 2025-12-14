<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis Reservas | Masuno</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
</head>
<body class="flex min-h-screen bg-gray-100">

  <!-- Sidebar según rol -->
  <?php
    if ($_SESSION['user']['rol'] === 'admin') {
        include __DIR__ . '/../partials/sidebar.php';
    } else {
        include __DIR__ . '/../partials/client_nav.php';
    }
  ?>

  <!-- Contenido principal -->
  <main class="flex-1 ml-64 p-8 overflow-auto">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">Mis Reservas</h1>
      
      <a href="index.php?url=Reservation/create"
           class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition">
          Nueva Reserva
      </a>
    </div>

    <div>
      <?php if (empty($reservas)): ?>
        <p class="text-gray-600">No tienes reservas aún.</p>
      <?php else: ?>
        <table class="w-full bg-white">
          <thead class="bg-gray-200">
            <tr>
              <th class="px-4 py-2 text-left">Servicio</th>
              <th class="px-4 py-2 text-left">Fecha</th>
              <th class="px-4 py-2 text-left">Inicio</th>
              <th class="px-4 py-2 text-left">Fin</th>
              <th class="px-4 py-2 text-left">Estado</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reservas as $r): 
              $classes = match($r->estado) {
                'confirmada' => 'bg-green-100 text-green-800',
                'cancelada'  => 'bg-red-100 text-red-800',
                default      => 'bg-yellow-100 text-yellow-800'
              };
            ?>
            <tr class="border-b hover:bg-gray-50">
              <td class="px-4 py-2"><?= htmlspecialchars($r->servicio_nombre) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($r->fecha) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($r->hora_inicio) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($r->hora_fin) ?></td>
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
