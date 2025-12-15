<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gest&oacute;n de Clientes | Masuno Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
</head>
<body class="flex min-h-screen bg-white text-black">

  <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

  <main class="flex-1 ml-64 p-8 overflow-auto">
    <!-- T��tulo y bot��n Nuevo Cliente -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">Gesti&oacute;n de Clientes</h1>
      <a href="/masuno/public/index.php?url=Client/create"
         class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition">
        Nuevo Cliente
      </a>
    </div>

    <table class="w-full bg-white shadow rounded">
      <thead class="bg-gray-200">
        <tr>
          <th class="px-4 py-2">Nombre</th>
          <th class="px-4 py-2">Email</th>
          <th class="px-4 py-2">Registrado</th>
          <th class="px-4 py-2">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($clientes as $c): ?>
        <tr class="hover:bg-gray-50">
          <td class="border px-4 py-2"><?= htmlspecialchars($c->nombre) ?></td>
          <td class="border px-4 py-2"><?= htmlspecialchars($c->email) ?></td>
          <td class="border px-4 py-2"><?= htmlspecialchars($c->creado_en) ?></td>
          <td class="border px-4 py-2">
            <div class="flex justify-center space-x-2">
              <!-- Ver (verde) -->
              <a href="/masuno/public/index.php?url=Client/show&id=<?= $c->id ?>"
                 class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition">
                Ver
              </a>
              <!-- Editar (azul) -->
              <a href="/masuno/public/index.php?url=Client/edit&id=<?= $c->id ?>"
                 class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 transition">
                Editar
              </a>
              <!-- Eliminar (rojo, abre modal) -->
              <button
                class="delete-button bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition"
                data-url="/masuno/public/index.php?url=Client/delete&id=<?= $c->id ?>"
              >
                Eliminar
              </button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </main>

  <!-- Modal de confirmaci��n -->
  <div id="delete-modal"
       class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-80">
      <h2 class="text-xl font-semibold mb-4">Confirmar eliminaci&oacute;n</h2>
      <p class="mb-6">&iquest;Est&aacute;s seguro de eliminar este cliente?</p>
      <div class="flex justify-center space-x-4">
        <button id="cancel-delete"
                class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-100 transition">
          Cancelar
        </button>
        <button id="confirm-delete"
                class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 transition">
          Eliminar
        </button>
      </div>
    </div>
  </div>

  <!-- Script para manejar el modal -->
  <script>
    const modal = document.getElementById('delete-modal');
    let deleteUrl = null;

    document.querySelectorAll('.delete-button').forEach(btn => {
      btn.addEventListener('click', () => {
        deleteUrl = btn.getAttribute('data-url');
        modal.classList.remove('hidden');
      });
    });

    document.getElementById('cancel-delete').addEventListener('click', () => {
      modal.classList.add('hidden');
      deleteUrl = null;
    });

    document.getElementById('confirm-delete').addEventListener('click', () => {
      if (deleteUrl) {
        window.location.href = deleteUrl;
      }
    });
  </script>
</body>
</html>
