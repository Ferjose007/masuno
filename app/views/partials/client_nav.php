<!-- app/views/partials/client_nav.php -->
<aside class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg flex flex-col justify-between">
  <div class="p-6">
    <img src="<?= BASE_URL ?>/assets/logo.png" alt="Masuno" class="h-12 mx-auto mb-6">
    <nav class="space-y-2">
      <!-- Enlace al Dashboard de cliente -->
      <a href="<?= BASE_URL ?>/index.php?url=Reservation/dashboard"
         class="flex items-center px-4 py-2 rounded-lg hover:bg-indigo-100 hover:text-indigo-700">
        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 3l7 6v8a1 1 0 0 1-1 1h-4V9H8v9H4a1 1 0 0 1-1-1V9l7-6z"/>
        </svg>
        Inicio
      </a>

      <!-- Enlace a Nueva Reserva -->
      <a href="<?= BASE_URL ?>/index.php?url=Reservation/create"
         class="flex items-center px-4 py-2 rounded-lg hover:bg-indigo-100 hover:text-indigo-700">
        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M12 4v16m8-8H4"/>
        </svg>
        Nueva Reserva
      </a>

      <!-- Enlace a Mis Reservas -->
      <a href="<?= BASE_URL ?>/index.php?url=Reservation/my"
         class="flex items-center px-4 py-2 rounded-lg hover:bg-indigo-100 hover:text-indigo-700">
        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M3 7h18M3 12h18M3 17h18"/>
        </svg>
        Mis Reservas
      </a>
    </nav>
  </div>

  <div class="p-6">
    <form action="<?= BASE_URL ?>/index.php" method="get">
      <input type="hidden" name="url" value="Auth/logout">
      <button type="submit" class="w-full text-center bg-red-600 text-white font-semibold py-2 rounded hover:bg-red-700 transition">
        Cerrar sesión
      </button>
    </form>
  </div>
</aside>
