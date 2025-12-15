<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>
    <?= isset($horario) ? 'Editar Horario' : 'Nuevo Horario' ?> | Masuno Admin
  </title>
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
        <?= isset($horario) ? 'Editar Horario' : 'Nuevo Horario' ?>
      </h2>
      <form action="index.php?url=Horario/<?= $action ?>" method="post" class="space-y-4">
        <?php if (isset($horario)): ?>
          <input type="hidden" name="id" value="<?= $horario->id ?>">
        <?php endif; ?>

        <div>
          <label class="block mb-1">Fecha</label>
          <input type="date" name="fecha" value="<?= $horario->fecha ?? '' ?>"
            class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
          <label class="block mb-1">Hora inicio</label>
          <input type="time" name="hora_inicio" value="<?= $horario->hora_inicio ?? '' ?>"
            class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
          <label class="block mb-1">Hora fin</label>
          <input type="time" name="hora_fin" value="<?= $horario->hora_fin ?? '' ?>"
            class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
          <label class="block mb-1">Estilista</label>
          <select name="estilista_id" class="w-full border rounded px-3 py-2" required>
            <option value="">-- selecciona un estilista --</option>
            <?php foreach ($estilistas as $e): ?>
              <option value="<?= $e->id ?>"
                <?= (isset($horario) && $horario->estilista_id == $e->id) ? 'selected' : '' ?>>
                <?= htmlspecialchars($e->nombre) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block mb-1">Estado</label>
          <select name="estado" class="w-full border rounded px-3 py-2" required>
            <?php foreach ($estados as $e): ?>
              <option value="<?= $e->id ?>"
                <?= (isset($horario) && $horario->estado === $e->id) ? 'selected' : '' ?>>
                <?= htmlspecialchars($e->descripcion) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="flex justify-center space-x-4 mt-6">
            <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 transition">
              <?= isset($horario) ? 'Actualizar' : 'Guardar' ?>
            </button>
            <a href="index.php?url=Horario/index"
                class="inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition ml-4">
                Cancelar
            </a>
        </div>
      </form>
    </div>
  </main>
</body>

</html>