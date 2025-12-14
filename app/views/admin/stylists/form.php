<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>
    <?= isset($stylist) ? 'Editar Estilista' : 'Nuevo Estilista' ?> | Masuno Admin
  </title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
</head>
<body class="flex min-h-screen bg-gray-100">

  <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

  <main class="flex-1 ml-64 p-8 overflow-auto">
    <div class="bg-white w-full p-6 rounded-lg shadow">
      <h2 class="text-2xl font-bold mb-4">
        <?= isset($stylist) ? 'Editar Estilista' : 'Nuevo Estilista' ?>
      </h2>
      <form action="index.php?url=AdminStylist/<?= $action ?>" method="post" class="space-y-4">
        <?php if(isset($stylist)): ?>
          <input type="hidden" name="id" value="<?= $stylist->id ?>">
        <?php endif; ?>

        <div>
          <label class="block mb-1">Nombre</label>
          <input type="text" name="nombre"
                 value="<?= $stylist->nombre ?? '' ?>"
                 class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
          <label class="block mb-1">Email</label>
          <input type="email" name="email"
                 value="<?= $stylist->email ?? '' ?>"
                 class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
          <label class="block mb-1">Contraseña <?= isset($stylist)? '(dejar en blanco para no cambiar)' : '' ?></label>
          <input type="password" name="password"
                 class="w-full border rounded px-3 py-2" <?= isset($stylist)? '' : 'required' ?>>
        </div>

        <div>
          <label class="block mb-1">Servicios</label>
          <div class="grid grid-cols-2 gap-2">
            <?php foreach($services as $srv): ?>
              <label class="inline-flex items-center">
                <input type="checkbox" name="services[]"
                       value="<?= $srv->id ?>"
                       <?= in_array($srv->id, $assignedIds ?? []) ? 'checked' : '' ?>
                       class="form-checkbox">
                <span class="ml-2"><?= htmlspecialchars($srv->nombre) ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="flex justify-center space-x-4 mt-6">
            <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 transition">
              <?= isset($stylist) ? 'Actualizar' : 'Guardar' ?>
            </button>
            <a href="index.php?url=AdminStylist/index"
                class="inline-block bg-red-600 text-white px-4 py-2 rounded
                hover:bg-red-700 transition ml-4">
                Cancelar
            </a>
        </div>
    </div>
  </main>
</body>
</html>
