<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= isset($servicio) ? 'Editar Servicio' : 'Nuevo Servicio' ?> | Masuno Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
</head>
<body class="flex min-h-screen bg-gray-100">
  <!-- Sidebar admin -->
  <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

  <!-- Contenido principal -->
  <main class="flex-1 ml-64 p-8 overflow-auto">
    <div class="bg-white w-full p-6 rounded-lg shadow">
      <h2 class="text-2xl font-bold mb-4">
        <?= isset($servicio) ? 'Editar Servicio' : 'Nuevo Servicio' ?>
      </h2>
      <form action="index.php?url=Service/<?= $action ?>" method="post" class="space-y-4">
        <?php if (isset($servicio)): ?>
          <input type="hidden" name="id" value="<?= $servicio->id ?>">
        <?php endif; ?>
        <div>
          <label class="block mb-1">Nombre</label>
          <input type="text" name="nombre" value="<?= $servicio->nombre ?? '' ?>"
                 class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
          <label class="block mb-1">Duración (min)</label>
          <input type="number" name="duracion_minutes" value="<?= $servicio->duracion_minutes ?? '' ?>"
                 class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
          <label class="block mb-1">Precio</label>
          <input type="text" name="precio" value="<?= $servicio->precio ?? '' ?>"
                 class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="flex justify-center space-x-4 mt-6">
            <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 transition">
              <?= isset($servicio) ? 'Actualizar' : 'Guardar' ?>
            </button>
            <a href="index.php?url=Service/index"
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
