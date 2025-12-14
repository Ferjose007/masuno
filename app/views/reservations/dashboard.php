<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Cliente | Masuno</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
</head>
<body class="flex min-h-screen bg-gray-100">

  <!-- Sidebar/Client-Nav según rol -->
  <?php
    if ($_SESSION['user']['rol'] === 'admin') {
      include __DIR__ . '/../partials/sidebar.php';
    } else {
      include __DIR__ . '/../partials/client_nav.php';
    }
  ?>

  <main class="flex-1 ml-64 p-8 overflow-auto">
    <div class="w-full h-full space-y-6">

      <!-- Saludo -->
      <div class="bg-white p-6 rounded-lg shadow">
        <h1 class="text-3xl font-bold">¡Hola, <?= htmlspecialchars($nombre) ?>!</h1>
        <p class="mt-2 text-gray-700">
          Tienes <span class="font-semibold"><?= $count ?></span> reserva<?= $count !== 1 ? 's' : '' ?>.
        </p>
      </div>

      <!-- Próximas reservas -->
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-semibold mb-4">Próximas Reservas</h2>

        <?php if (empty($upcoming)): ?>
          <p class="text-gray-600">Por el momento no tienes citas agendadas.</p>
        <?php else: ?>
          <table class="w-full bg-white">
            <thead class="bg-gray-200">
              <tr>
                <th class="px-4 py-2 text-left">Servicio</th>
                <th class="px-4 py-2 text-left">Fecha</th>
                <th class="px-4 py-2 text-left">Hora</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($upcoming as $r): ?>
              <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-2"><?= htmlspecialchars($r->servicio_nombre) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($r->fecha) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($r->hora_inicio) ?>–<?= htmlspecialchars($r->hora_fin) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>

        <div class="mt-4 text-right">
          <a href="/masuno/public/index.php?url=Reservation/my"
             class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            Ver todas mis reservas →
          </a>
        </div>
      </div>

    </div>
  </main>

</body>
</html>
