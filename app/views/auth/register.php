<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro | Masuno</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
</head>
<body class="flex min-h-screen bg-white text-black items-center justify-center p-4">

  <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">
    <!-- Header con logo -->
    <div class="text-center py-6">
      <img src="<?= BASE_URL ?>/assets/logo.png" alt="Masuno" class="h-12 mx-auto mb-4">
      <h1 class="text-2xl font-bold mb-2">Crear Cuenta</h1>
      <p class="text-sm text-gray-700">Únete a Masuno y reserva tus citas fácilmente</p>
    </div>

    <div class="p-8">
      <?php if (!empty($errors)): ?>
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          <ul class="list-disc list-inside">
            <?php foreach ($errors as $err): ?>
              <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form action="<?= BASE_URL ?>/Auth/register" method="post" class="space-y-6">
        <div>
          <label class="block text-black mb-1">Nombre completo</label>
          <input
            type="text"
            name="nombre"
            class="w-full border border-black rounded-lg px-4 py-2 focus:outline-none
                   focus:ring-2 focus:ring-red-600"
            value="<?= htmlspecialchars($old['nombre'] ?? '') ?>"
            required
          >
        </div>

        <div>
          <label class="block text-black mb-1">Correo electrónico</label>
          <input
            type="email"
            name="email"
            class="w-full border border-black rounded-lg px-4 py-2 focus:outline-none
                   focus:ring-2 focus:ring-red-600"
            value="<?= htmlspecialchars($old['email'] ?? '') ?>"
            required
          >
        </div>

        <div class="grid grid-cols-1 gap-6">
          <div>
            <label class="block text-black mb-1">Contraseña</label>
            <input
              type="password"
              name="password"
              class="w-full border border-black rounded-lg px-4 py-2 focus:outline-none
                     focus:ring-2 focus:ring-red-600"
              required
            >
          </div>
          <div>
            <label class="block text-black mb-1">Confirmar contraseña</label>
            <input
              type="password"
              name="password2"
              class="w-full border border-black rounded-lg px-4 py-2 focus:outline-none
                     focus:ring-2 focus:ring-red-600"
              required
            >
          </div>
        </div>

        <button
          type="submit"
          class="w-full bg-red-600 text-white rounded-lg py-2 font-semibold
                 hover:bg-red-700 transition"
        >
          Registrar
        </button>

        <p class="text-center text-black text-sm">
          ¿Ya tienes cuenta?
          <a href="<?= BASE_URL ?>/index.php?url=Auth/showLogin" class="text-red-600 hover:underline">Inicia sesión</a>
        </p>
      </form>
    </div>
  </div>

</body>
</html>
