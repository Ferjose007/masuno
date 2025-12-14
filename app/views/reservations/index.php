<!-- app/views/reservations/index.php -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nueva Reserva | Masuno</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
</head>
<body class="flex min-h-screen bg-gray-100">

  <?php
  // Incluir la barra lateral según el rol
  if (isset($_SESSION['user']) && $_SESSION['user']['rol'] === 'admin') {
      include __DIR__ . '/../partials/sidebar.php';
  } else {
      include __DIR__ . '/../partials/client_nav.php';
  }
  ?>

  <!-- Contenido principal junto al sidebar -->
  <main class="flex-1 ml-64 p-8 overflow-auto">
    <div class="bg-white w-full p-6 rounded-lg shadow">
      <h2 class="text-2xl font-bold mb-4">Nueva Reserva</h2>
      <form action="/masuno/public/index.php?url=Reservation/store" method="post" class="space-y-4">
        <div>
          <label class="block mb-1">Servicio</label>
          <select name="servicio_id" required class="w-full border rounded px-3 py-2">
            <option value="">-- Selecciona --</option>
            <?php foreach ($servicios as $s): ?>
            <option value="<?= $s->id ?>">
              <?= htmlspecialchars($s->nombre) ?> — <?= $s->duracion_minutes ?> min — $<?= number_format($s->precio,2) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block mb-1">Horario</label>
          <select name="horario_id" required class="w-full border rounded px-3 py-2">
            <option value="">-- Selecciona --</option>
            <?php foreach ($horarios as $h): ?>
            <option value="<?= $h->id ?>">
              <?= htmlspecialchars($h->fecha) ?> <?= htmlspecialchars($h->hora_inicio) ?>–<?= htmlspecialchars($h->hora_fin) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="flex justify-center space-x-4 mt-6">
            <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 transition">
              Guardar
            </button>
            <a href="index.php?url=Reservation/my"
                class="inline-block bg-red-600 text-white px-4 py-2 rounded
                hover:bg-red-700 transition ml-4">
                Cancelar
            </a>
        </div>
      </form>
    </div>
  </main>

</body>
</html>
