<?php
// 1. Detectar la URL actual (si no existe, asumimos que es el dashboard)
$current_url = $_GET['url'] ?? 'Admin/dashboard';

// 2. Función helper para decidir qué clases usar
// Si la URL actual empieza con el $path esperado, devolvemos las clases "activas"
function getNavLinkClass($current_url, $path) {
    $active_classes   = "bg-indigo-100 text-indigo-700"; // Fondo azulito, texto oscuro
    $inactive_classes = "text-gray-700 hover:bg-indigo-50 hover:text-indigo-700"; // Gris, hover azulito

    // Verificamos coincidencia. Usamos strpos para que sub-páginas (ej: Client/create)
    // mantengan activo el menú padre (Clientes).
    if (strpos($current_url, $path) === 0) {
        return $active_classes;
    }
    return $inactive_classes;
}
?>

<aside class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg flex flex-col justify-between z-10">
    <div class="p-6">
        <div class="flex justify-center mb-8">
            <img src="<?= BASE_URL ?>/assets/logo.png" alt="Masuno" class="h-12">
        </div>

        <nav class="space-y-2">
            <a href="<?= BASE_URL ?>/index.php?url=Admin/dashboard"
               class="flex items-center px-4 py-2 rounded-lg transition-colors <?= getNavLinkClass($current_url, 'Admin/dashboard') ?>">
                <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Inicio
            </a>

            <a href="<?= BASE_URL ?>/index.php?url=Service/index"
               class="flex items-center px-4 py-2 rounded-lg transition-colors <?= getNavLinkClass($current_url, 'Service') ?>">
                <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 7.938V3c0-1.105.895-2 2-2z"/>
                </svg>
                Servicios
            </a>

            <a href="<?= BASE_URL ?>/index.php?url=Client/index"
               class="flex items-center px-4 py-2 rounded-lg transition-colors <?= getNavLinkClass($current_url, 'Client') ?>">
                <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Clientes
            </a>

            <a href="<?= BASE_URL ?>/index.php?url=AdminStylist/index"
               class="flex items-center px-4 py-2 rounded-lg transition-colors <?= getNavLinkClass($current_url, 'AdminStylist') ?>">
                <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884.896 1.688 2 2.308 1.104-.62 2-1.424 2-2.308"/>
                </svg>
                Estilistas
            </a>

            <a href="<?= BASE_URL ?>/index.php?url=Reservation/my"
               class="flex items-center px-4 py-2 rounded-lg transition-colors <?= getNavLinkClass($current_url, 'Reservation') ?>">
                <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Reservas
            </a>

            <a href="<?= BASE_URL ?>/index.php?url=Horario/index"
               class="flex items-center px-4 py-2 rounded-lg transition-colors <?= getNavLinkClass($current_url, 'Horario') ?>">
                <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Horarios
            </a>
        </nav>
    </div>

    <div class="p-6 border-t border-gray-100">
        <form action="<?= BASE_URL ?>/index.php" method="get">
            <input type="hidden" name="url" value="Auth/logout">
            <button type="submit"
                class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Cerrar Sesión
            </button>
        </form>
    </div>
</aside>