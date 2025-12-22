<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrarse | Masuno</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_URL ?>/assets/favicon-32x32.png">
</head>
<body class="flex min-h-screen bg-white text-black items-center justify-center p-4">

  <div class="w-full max-w-sm bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
    
    <div class="text-center pt-8 pb-4 px-6">
      <img src="<?= BASE_URL ?>/assets/logo.png" alt="Masuno" class="h-16 mx-auto mb-4 object-contain">
      <h1 class="text-2xl font-bold mb-1 tracking-tight">Crear Cuenta</h1>
      <p class="text-sm text-gray-500">Únete a Masuno Estilistas</p>
    </div>

    <div class="p-8 pt-2">
      
      <?php if (isset($_GET['error'])): ?>
        <div class="mb-6 bg-red-50 border-l-4 border-red-600 text-red-700 p-4 text-sm rounded-r">
             <?php 
                if($_GET['error'] == 'email_exists') echo 'Este correo ya está registrado.';
                else echo 'Ocurrió un error al registrarse.';
            ?>
        </div>
      <?php endif; ?>

      <form action="<?= BASE_URL ?>/index.php?url=Auth/register" method="post" class="space-y-4">
        
        <div>
          <label class="block text-black font-medium mb-1 text-sm">Nombre Completo</label>
          <input
            type="text"
            name="nombre"
            class="w-full border border-black rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent transition-shadow"
            placeholder="Tu nombre"
            required
          >
        </div>

        <div>
          <label class="block text-black font-medium mb-1 text-sm">Correo electrónico</label>
          <input
            type="email"
            name="email"
            class="w-full border border-black rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent transition-shadow"
            placeholder="ejemplo@correo.com"
            required
          >
        </div>

        <div>
          <label class="block text-black font-medium mb-1 text-sm">Teléfono</label>
          <input
            type="tel"
            name="telefono"
            class="w-full border border-black rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent transition-shadow"
            placeholder="999 999 999"
          >
        </div>

        <div>
          <label class="block text-black font-medium mb-1 text-sm">Contraseña</label>
          <input
            type="password"
            name="password"
            class="w-full border border-black rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent transition-shadow"
            placeholder="Mínimo 6 caracteres"
            required
          >
        </div>

        <button
          type="submit"
          class="w-full bg-red-600 text-white rounded-lg py-3 font-bold uppercase tracking-wide text-sm hover:bg-red-700 active:scale-95 transition-all shadow-md hover:shadow-lg mt-2"
        >
          Registrarme
        </button>

        <p class="text-center text-black text-sm pt-2">
          ¿Ya tienes cuenta?
          <a href="<?= BASE_URL ?>/index.php?url=Auth/showLogin" class="text-red-600 font-bold hover:underline">Inicia Sesión</a>
        </p>
      </form>
    </div>
  </div>

</body>
</html>