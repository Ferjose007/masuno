<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión | Masuno</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
</head>
<body class="flex min-h-screen bg-white text-black items-center justify-center p-4">

  <div class="w-full max-w-sm bg-white rounded-2xl shadow-xl overflow-hidden">
    <!-- Header con logo -->
    <div class="text-center py-6">
      <img src="/assets/logo.png" alt="Masuno" class="h-12 mx-auto mb-4">
      <h1 class="text-2xl font-bold mb-2">Bienvenido de nuevo</h1>
      <p class="text-sm text-gray-700">Accede para gestionar tus citas</p>
    </div>

    <div class="p-8">
      <?php if (!empty($error)): ?>
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form action="/Auth/login" method="post" class="space-y-6">
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

        <button
          type="submit"
          class="w-full bg-red-600 text-white rounded-lg py-2 font-semibold
                 hover:bg-red-700 transition"
        >
          Entrar
        </button>

        <p class="text-center text-black text-sm">
          ¿No tienes cuenta?
          <a href="/masuno/public/index.php?url=Auth/showRegister" class="text-red-600 hover:underline">Regístrate aquí</a>
        </p>
      </form>
    </div>
  </div>

</body>
</html>
