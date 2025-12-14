<aside class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg flex flex-col justify-between">
  <div class="p-6">
    <img src="/assets/logo.png" alt="Masuno" class="h-12 mx-auto mb-6">
    <nav class="space-y-2">
      <a href="/masuno/public/index.php?url=Stylist/dashboard"
         class="flex items-center px-4 py-2 rounded-lg hover:bg-indigo-100 hover:text-indigo-700">
        <!-- icono home -->
        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 3l7 6v8a1 1 0 0 1-1 1h-4V9H8v9H4a1 1 0 0 1-1-1V9l7-6z"/>
        </svg>
        Inicio
      </a>
      <a href="/masuno/public/index.php?url=Stylist/appointments"
         class="flex items-center px-4 py-2 rounded-lg hover:bg-indigo-100 hover:text-indigo-700">
        <!-- icono calendario -->
        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M8 7V3M16 7V3M3 11h18M5 21h14a2 2 0 0 0 2-2V11H3v8a2 2 0 0 0 2 2z"/>
        </svg>
        Mis Citas
      </a>
    </nav>
  </div>
  <div class="p-6">
    <form action="/masuno/public/index.php" method="get">
      <input type="hidden" name="url" value="Auth/logout">
      <button type="submit"
              class="w-full text-center bg-red-600 text-white font-semibold py-2 rounded hover:bg-red-700 transition">
        Cerrar sesión
      </button>
    </form>
  </div>
</aside>
