<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// --- AGREGAR ESTO ---
// Detectar automáticamente la URL base (http://localhost/tu-carpeta/public)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
// Ajusta '/masuno/public' si tu carpeta local tiene otro nombre, 
// o usa '/' si usas "php -S localhost:8000"
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
define('BASE_URL', $protocol . "://" . $host . $scriptName);
// --------------------

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../config/database.php';

use Core\Router;

// Inicializar Router
$router = new Router();

// Definir rutas

//Home
$router->get('', 'HomeController@index');
//Auth
$router->get('Auth/showRegister', 'AuthController@showRegister');
$router->post('Auth/register',    'AuthController@register');
$router->get('Auth/showLogin',    'AuthController@showLogin');
$router->post('Auth/login',       'AuthController@login');
$router->get('Auth/logout',       'AuthController@logout');
// Gestión de Servicios (solo admin)
$router->get('Service/index',   'ServiceController@index');
$router->get('Service/create',  'ServiceController@create');
$router->post('Service/store',  'ServiceController@store');
$router->get('Service/edit',    'ServiceController@edit');
$router->post('Service/update','ServiceController@update');
$router->get('Service/delete',  'ServiceController@delete');
$router->get('Admin/dashboard', 'AdminController@dashboard');
// CRUD de Horarios (solo admin)
$router->get('Horario/index',   'HorarioController@index');
$router->get('Horario/create',  'HorarioController@create');
$router->post('Horario/store',  'HorarioController@store');
$router->get('Horario/edit',    'HorarioController@edit');
$router->post('Horario/update', 'HorarioController@update');
$router->get('Horario/delete',  'HorarioController@delete');
// Clientes (solo admin)
$router->get('Client/index',   'ClientController@index');
$router->get('Client/show',    'ClientController@show');
$router->get('Client/create',  'ClientController@create');    // <<<
$router->post('Client/store',  'ClientController@store');     // <<<
$router->get('Client/edit',    'ClientController@edit');
$router->post('Client/update', 'ClientController@update');
$router->get('Client/delete',  'ClientController@delete');
// Formulario de reserva
$router->get('Reservation/create','ReservationController@create');
// (si quieres que index también funcione como alias)
$router->get('Reservation/index', 'ReservationController@index');
$router->post('Reservation/store','ReservationController@store');
$router->get('Reservation/my',    'ReservationController@my');
// Dashboard de cliente
$router->get('Reservation/dashboard', 'ReservationController@dashboard');
// Lado del Estilista
$router->get('Stylist/dashboard',     'StylistController@dashboard');
$router->get('Stylist/appointments',  'StylistController@appointments');
// CRUD Estilistas (solo admin)
$router->get('AdminStylist/index',  'AdminStylistController@index');
$router->get('AdminStylist/create', 'AdminStylistController@create');
$router->post('AdminStylist/store', 'AdminStylistController@store');
$router->get('AdminStylist/edit',   'AdminStylistController@edit');
$router->post('AdminStylist/update','AdminStylistController@update');
$router->get('AdminStylist/delete', 'AdminStylistController@delete');


// Ejecutar
$router->run();
