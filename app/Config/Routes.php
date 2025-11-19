<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --- Rutas Públicas (no requieren login) ---
//$routes->get('/', 'AuthController::login');
$routes->get('login', 'AuthController::login');
$routes->get('auth/register', 'AuthController::register');
$routes->get('/', 'ParticipanteLoginController::index');
$routes->get('/ingreso', 'ParticipanteLoginController::index');
$routes->post('ingreso/verificar', 'ParticipanteLoginController::verifyDni');
$routes->post('ingreso/entrar', 'ParticipanteLoginController::attemptLogin');

$routes->group('auth', static function ($routes) {
    $routes->post('login', 'AuthController::attemptLogin');
    $routes->post('register', 'AuthController::attemptRegister');
});


// --- Rutas Protegidas (Requieren Autenticación con el filtro 'auth') ---
$routes->group('', ['filter' => 'auth'], static function ($routes) {

    // PANELES PRINCIPALES
    

        $routes->get('mi-panel', 'ParticipanteController::index'); // Para Participantes
        $routes->post('asistencia/registrar/(:num)', 'AsistenciaController::registrar/$1'); // Acción del participante

        $routes->get('participante/mis-cursos', 'ParticipanteController::misCursos');
        $routes->get('participante/historial', 'AsistenciaController::misAsistencias');
    
$routes->get('asistencias/reporte', 'AsistenciaController::reporteAdmin', ['filter' => 'auth']);
$routes->get('asistencias/exportar-reporte-excel', 'AsistenciaController::exportarReporteExcel', ['filter' => 'auth']);
    $routes->group('', ['filter' => 'admin:1,2'], static function ($routes) {

    $routes->get('dashboard', 'DashboardController::index'); // Para Admins
    $routes->get('cursos/asignar/(:num)', 'CursoController::asignar/$1');
    // Ruta para procesar el formulario de asignación
    $routes->post('cursos/asignar/(:num)', 'CursoController::guardarAsignacion/$1');
    // Dentro del grupo protegido ['filter' => 'auth']

    // Aqui ruta para notas de admin
    $routes->group('notas', static function ($routes) {
        // Vista para gestionar notas de un módulo específico
        $routes->get('modulo/(:num)', 'NotasController::gestionarNotas/$1');
        
        // Acción para guardar una nota (individual o múltiples)
        $routes->post('guardar', 'NotasController::guardarNota');
        
        // Reportes de notas por curso (opcional)
        $routes->get('reporte/curso/(:num)', 'NotasController::reporteCurso/$1');
    });

    // Ruta para mostrar la página de inscripción
    $routes->get('cursos/inscribir/(:num)', 'CursoController::inscribir/$1');
    // Ruta para procesar el formulario de inscripción
    $routes->get('cursos/inscribir/(:num)', 'CursoController::inscribir/$1');
    $routes->post('cursos/guardar-inscripcion/(:num)', 'CursoController::guardarInscripcion/$1');
    $routes->post('cursos/quitar-inscripcion/(:num)/(:num)', 'CursoController::quitarInscripcion/$1/$2');
    $routes->post('cursos/quitar-inscripciones-multiples/(:num)', 'CursoController::quitarInscripcionesMultiples/$1');
    
        $routes->get('pagos', 'PagoAdminController::index');
        $routes->get('pagos/revisar/(:num)', 'PagoAdminController::revisar/$1');
        $routes->post('pagos/aprobar/(:num)', 'PagoAdminController::aprobar/$1');
        $routes->post('pagos/rechazar/(:num)', 'PagoAdminController::rechazar/$1');
        $routes->get('pagos/reportes', 'PagoAdminController::reportes');
        $routes->post('pagos/editar-datos/(:num)', 'PagoAdminController::editarDatos/$1');

    // ACCIONES
    $routes->get('auth/logout', 'AuthController::logout');
    // GESTIÓN DE CURSOS
    $routes->group('cursos', static function ($routes) {
        $routes->get('/', 'CursoController::index');
        $routes->post('/', 'CursoController::create');
        $routes->post('update/(:num)', 'CursoController::update/$1');
        $routes->post('delete/(:num)', 'CursoController::delete/$1');
    });

    // GESTIÓN DE MÓDULOS
    $routes->get('cursos/(:num)/modulos', 'ModuloController::index/$1');
    $routes->post('cursos/(:num)/modulos', 'ModuloController::create/$1');
    $routes->post('modulos/update/(:num)', 'ModuloController::update/$1');
    $routes->post('modulos/delete/(:num)', 'ModuloController::delete/$1');
    
    // GESTIÓN DE SESIONES
    $routes->get('modulos/(:num)/sesiones', 'SesionController::index/$1');
    $routes->post('modulos/(:num)/sesiones', 'SesionController::create/$1');
    $routes->post('sesiones/update/(:num)', 'SesionController::update/$1');
    $routes->post('sesiones/delete/(:num)', 'SesionController::delete/$1');

    // GESTIÓN DE ASISTENCIAS (Vistas y Acciones)
    $routes->get('sesiones/(:num)/asistencias', 'AsistenciaController::index/$1'); // Vista de admin
    //$routes->get('mis-asistencias', 'AsistenciaController::misAsistencias'); // Vista de historial del participante
});
// GESTIÓN DE USUARIOS
    $routes->group('', ['filter' => 'admin:1'], static function ($routes) {

    $routes->get('usuarios/exportar-excel', 'UserController::exportarExcel');

    $routes->resource('usuarios', ['controller' => 'UserController']);
    });
        $routes->get('logout', 'ParticipanteLoginController::logout');

});


// Rutas para participantes
$routes->group('participante', ['filter' => 'auth'], static function ($routes) {
    
    $routes->get('mis-pagos', 'PagoController::index');
    // Rutas para participantes
    $routes->get('mis-notas', 'NotasController::misNotas');
    $routes->get('notas/curso/(:num)', 'NotasController::verNotasCurso/$1');

    $routes->get('subir-comprobante/(:num)', 'PagoController::subirComprobante/$1');
    $routes->post('procesar-comprobante', 'PagoController::procesarComprobante');

});
