<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= isset($old['id']) ? 'Editar Cliente' : 'Nuevo Cliente' ?> | Masuno Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
</head>
<body class="flex min-h-screen bg-white text-black">
  <?php include __DIR__ . '/../partials/sidebar.php'; ?>

  <main class="flex-1 ml-64 p-8 overflow-auto">
    <div class="bg-white w-full p-6 rounded-lg shadow">
      <h2 class="text-2xl font-bold mb-4">
        <?= isset($old['id']) ? 'Editar Cliente' : 'Nuevo Cliente' ?>
      </h2>

      <!-- Mostrar errores -->
      <?php if (!empty($errors)): ?>
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          <ul class="list-disc list-inside">
            <?php foreach ($errors as $err): ?>
              <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form action="index.php?url=Client/<?= isset($old['id']) ? 'update' : 'store' ?>"
            method="post" class="space-y-4">
        <?php if (isset($old['id'])): ?>
          <input type="hidden" name="id" value="<?= $old['id'] ?>">
        <?php endif; ?>

        <div>
          <label class="block mb-1">Nombre completo</label>
          <input type="text" name="nombre"
                 value="<?= htmlspecialchars($old['nombre'] ?? '') ?>"
                 class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-600"
                 required>
        </div>

        <div>
          <label class="block mb-1">Correo electrónico</label>
          <input type="email" name="email"
                 value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                 class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-600"
                 required>
        </div>

        <div>
          <label class="block mb-1">Contraseña</label>
          <input type="password" name="password"
                 class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-600"
                 <?= isset($old['id']) ? '' : 'required' ?>>
          <?php if (isset($old['id'])): ?>
            <p class="text-sm text-gray-600 mt-1">
              Déjalo en blanco para no cambiar la contraseña.
            </p>
          <?php endif; ?>
        </div>

        <?php if (!isset($old['id'])): ?>
        <div>
          <label class="block mb-1">Confirmar contraseña</label>
          <input type="password" name="password2"
                 class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-600"
                 required>
        </div>
        <?php endif; ?>

        <div class="flex justify-center space-x-4 mt-6">
          <button type="submit"
                  class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 transition">
            <?= isset($old['id']) ? 'Actualizar' : 'Guardar' ?>
          </button>
          <a href="index.php?url=Client/index"
             class="inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition ml-4">
            Cancelar
          </a>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
