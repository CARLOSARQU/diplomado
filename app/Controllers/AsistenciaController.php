<?php

namespace App\Controllers;

use App\Models\AsistenciaModel;
use App\Models\SesionModel;
use App\Models\ModuloModel;
use App\Models\CursoModel;

class AsistenciaController extends BaseController
{
    /**
     * Muestra la lista de asistencias para el Administrador.
     */
    public function index($sesion_id)
    {
        // Solo admin o superadmin
        if (!in_array(session('rol_nombre'), ['admin', 'superadmin'])) {
            return redirect()->to('dashboard');
        }

        $asistenciaModel = new AsistenciaModel();
        $sesionModel     = new SesionModel();
        $moduloModel     = new ModuloModel();
        $cursoModel      = new CursoModel();

        $sesion = $sesionModel->find($sesion_id);
        if (!$sesion) {
            return redirect()->back()->with('error', 'La sesión no existe.');
        }

        $modulo = $moduloModel->find($sesion['modulo_id']);
        $curso  = $modulo ? $cursoModel->find($modulo['curso_id']) : null;

        $data = [
            'asistencias' => $asistenciaModel->getAsistenciasBySesion($sesion_id),
            'sesion'      => $sesion,
            'modulo'      => $modulo,
            'curso'       => $curso,
        ];

        return view('asistencias/admin_index', $data);
    }

    /**
     * Registra la asistencia del participante autenticado.
     */
    public function registrar($sesion_id)
    {
        // Solo participantes
        if (session('rol_nombre') !== 'usuario') {
            return redirect()->back()->with('error', 'No tienes permisos para registrar asistencia.');
        }

        $participante_id = session('user_id');

        $asistenciaModel = new AsistenciaModel();
        $sesionModel     = new SesionModel();

        $sesion = $sesionModel->find($sesion_id);
        if (!$sesion || (int)($sesion['asistencia_habilitada'] ?? 0) !== 1) {
            return redirect()->back()->with('error', 'La asistencia para esta sesión no está habilitada.');
        }

        // Evitar duplicados
        if ($asistenciaModel->yaRegistroAsistencia($sesion_id, $participante_id)) {
            return redirect()->back()->with('error', 'Ya has registrado tu asistencia para esta sesión.');
        }

        // "presente" o "tarde" en observaciones (el modelo NO tiene columna 'estado')
        $ahora      = time();
        $inicio     = strtotime($sesion['fecha'] . ' ' . $sesion['hora_inicio']);
        $tolerancia = 240 * 60; // 4 horas
        $obs        = ($ahora > $inicio + $tolerancia) ? 'ausente' : 'presente';

        // Usa el método del modelo que valida e inserta
        $resultado = $asistenciaModel->registrarAsistencia($sesion_id, $participante_id, $obs);

        if (!empty($resultado['success'])) {
            return redirect()->back()->with('success', '¡Asistencia registrada con éxito!');
        }

        return redirect()->back()->with('error', $resultado['message'] ?? 'Error al registrar la asistencia.');
    }

    /**
     * Historial de asistencias del participante autenticado.
     */
    public function misAsistencias()
{
    if (session('rol_nombre') !== 'usuario') {
        return redirect()->to('/')->with('error', 'No tienes acceso a esta sección.');
    }

    $participante_id = session('user_id');
    
    // Validar que tenemos un ID válido
    if (empty($participante_id)) {
        log_message('error', 'misAsistencias: No se encontró user_id en la sesión');
        return redirect()->to('participante/dashboard')->with('error', 'Error de sesión. Por favor, inicia sesión nuevamente.');
    }

    $asistenciaModel = new AsistenciaModel();

    // Obtener historial
    $historial = $asistenciaModel->getAsistenciasByParticipante($participante_id);
    
    // Log para debugging
    log_message('debug', "misAsistencias - User ID: $participante_id, Historial count: " . count($historial));
    
    // Si está en desarrollo, agregar información de debug
    $data = [
        'historial' => $historial
    ];
    
    if (ENVIRONMENT === 'development') {
        // Obtener asistencias simples para comparar
        $asistenciasSimples = $asistenciaModel->where('participante_id', $participante_id)->findAll();
        
        $data['debug_info'] = [
            'participante_id' => $participante_id,
            'session_data' => [
                'user_id' => session('user_id'),
                'rol_nombre' => session('rol_nombre'),
                'nombres' => session('nombres'),
                'apellidos' => session('apellidos')
            ],
            'asistencias_simples_count' => count($asistenciasSimples),
            'historial_completo_count' => count($historial),
            'primera_asistencia_simple' => !empty($asistenciasSimples) ? $asistenciasSimples[0] : null,
            'primer_historial' => !empty($historial) ? $historial[0] : null
        ];
    }

    return view('participante/historial', $data);
}
}
