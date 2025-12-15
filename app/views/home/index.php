<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Bienvenido a Masuno</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
</head>
<body class="flex min-h-screen bg-white text-black items-center justify-center p-4">

  <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 text-center">
    <!-- Logo -->
    <img src="<?= BASE_URL ?>/assets/logo.png" alt="Masuno" class="h-16 mx-auto mb-6">

    <p class="text-lg mb-8">
      Tu sistema de reservas para salón de belleza.
    </p>

    <a href="<?= BASE_URL ?>/index.php?url=Auth/showLogin"
       class="inline-block bg-red-600 text-white px-8 py-3 rounded-lg font-semibold
              hover:bg-red-700 transition">
      Iniciar Sesión
    </a>
  </div>

</body>
</html>
