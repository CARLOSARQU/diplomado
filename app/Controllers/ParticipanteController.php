<?php

namespace App\Controllers;

use App\Models\SesionModel;
use App\Models\CursoParticipanteModel;
use App\Models\AsistenciaModel;
use App\Models\CursoModel;

class ParticipanteController extends BaseController
{
    /**
     * Muestra el panel del participante con las sesiones de hoy de sus cursos inscritos
     */
    public function index()
    {
        // Verificar que el usuario sea participante
        if (session('rol_nombre') !== 'usuario') {
            return redirect()->to('/')->with('error', 'No tienes acceso a esta sección.');
        }

        $participante_id = session('user_id');
        $fecha_hoy = date('Y-m-d');
        
        $sesionModel = new SesionModel();
        $cursoParticipanteModel = new CursoParticipanteModel();
        $asistenciaModel = new AsistenciaModel();

        // Obtener las sesiones de hoy de los cursos en los que está inscrito el participante
        $sesiones_hoy = $sesionModel
            ->select('sesiones.*, cursos.nombre as curso_nombre, modulos.nombre as modulo_nombre')
            ->join('modulos', 'modulos.id = sesiones.modulo_id')
            ->join('cursos', 'cursos.id = modulos.curso_id')
            ->join('curso_participante', 'curso_participante.curso_id = cursos.id')
            ->where('curso_participante.participante_id', $participante_id)
            ->where('sesiones.fecha', $fecha_hoy)
            ->where('sesiones.asistencia_habilitada', 1)
            ->where('cursos.estado', 1) // Solo cursos activos
            ->orderBy('sesiones.hora_inicio', 'ASC')
            ->findAll();

        // Para cada sesión, verificar si ya registró asistencia
        foreach ($sesiones_hoy as &$sesion) {
            $asistencia_existente = $asistenciaModel
                ->where('sesion_id', $sesion['id'])
                ->where('participante_id', $participante_id)
                ->first();
            
            $sesion['ya_registro_asistencia'] = !empty($asistencia_existente);
            $sesion['asistencia_id'] = $asistencia_existente['id'] ?? null;
            $sesion['hora_registro'] = $asistencia_existente['created_at'] ?? null;
        }

        // Obtener estadísticas del participante
        $stats = $this->obtenerEstadisticasParticipante($participante_id);

        $data = [
            'sesiones_hoy' => $sesiones_hoy,
            'estadisticas' => $stats,
            'fecha_actual' => $fecha_hoy
        ];
        
        return view('participante/dashboard', $data);
    }

    /**
     * Obtiene estadísticas del participante
     */
    private function obtenerEstadisticasParticipante($participante_id)
    {
        $cursoParticipanteModel = new CursoParticipanteModel();
        $asistenciaModel = new AsistenciaModel();
        $sesionModel = new SesionModel();

        // Cursos en los que está inscrito
        $cursos_inscritos = $cursoParticipanteModel
            ->select('cursos.id, cursos.nombre, cursos.estado')
            ->join('cursos', 'cursos.id = curso_participante.curso_id')
            ->where('curso_participante.participante_id', $participante_id)
            ->findAll();

        $total_cursos = count($cursos_inscritos);
        $cursos_activos = count(array_filter($cursos_inscritos, fn($c) => $c['estado'] == 1));

        // Total de sesiones disponibles en sus cursos
        $total_sesiones_disponibles = 0;
        if (!empty($cursos_inscritos)) {
            $curso_ids = array_column($cursos_inscritos, 'id');
            $total_sesiones_disponibles = $sesionModel
                ->join('modulos', 'modulos.id = sesiones.modulo_id')
                ->whereIn('modulos.curso_id', $curso_ids)
                ->where('sesiones.fecha <=', date('Y-m-d'))
                ->countAllResults();
        }

        // Total de asistencias registradas
        $total_asistencias = $asistenciaModel
            ->where('participante_id', $participante_id)
            ->countAllResults();

        // Porcentaje de asistencia
        $porcentaje_asistencia = $total_sesiones_disponibles > 0 
            ? round(($total_asistencias / $total_sesiones_disponibles) * 100, 1)
            : 0;

        return [
            'cursos_inscritos' => $total_cursos,
            'cursos_activos' => $cursos_activos,
            'total_sesiones_disponibles' => $total_sesiones_disponibles,
            'total_asistencias' => $total_asistencias,
            'porcentaje_asistencia' => $porcentaje_asistencia
        ];
    }

    /**
     * Muestra el historial de asistencias del participante
     */
    
    /**
     * Muestra los cursos en los que está inscrito el participante
     */
    public function misCursos()
    {
        if (session('rol_nombre') !== 'usuario') {
            return redirect()->to('/')->with('error', 'No tienes acceso a esta sección.');
        }

        $participante_id = session('user_id');
        $cursoParticipanteModel = new CursoParticipanteModel();

        // Obtener cursos inscritos con información adicional
        $mis_cursos = $cursoParticipanteModel
            ->select('cursos.*, curso_participante.created_at as fecha_inscripcion')
            ->join('cursos', 'cursos.id = curso_participante.curso_id')
            ->where('curso_participante.participante_id', $participante_id)
            ->orderBy('cursos.fecha_inicio', 'DESC')
            ->findAll();

        // Para cada curso, obtener estadísticas de asistencia
        foreach ($mis_cursos as &$curso) {
            $stats = $this->obtenerEstadisticasCurso($participante_id, $curso['id']);
            $curso['estadisticas'] = $stats;
        }

        $data = [
            'mis_cursos' => $mis_cursos
        ];

        return view('participante/mis-cursos', $data);
    }

    /**
     * Obtiene estadísticas de asistencia para un curso específico
     */
    private function obtenerEstadisticasCurso($participante_id, $curso_id)
    {
        $sesionModel = new SesionModel();
        $asistenciaModel = new AsistenciaModel();

        // Total de sesiones del curso
        $total_sesiones = $sesionModel
            ->join('modulos', 'modulos.id = sesiones.modulo_id')
            ->where('modulos.curso_id', $curso_id)
            ->where('sesiones.fecha <=', date('Y-m-d'))
            ->countAllResults();

        // Asistencias del participante en este curso
        $asistencias_curso = $asistenciaModel
            ->join('sesiones', 'sesiones.id = asistencias.sesion_id')
            ->join('modulos', 'modulos.id = sesiones.modulo_id')
            ->where('modulos.curso_id', $curso_id)
            ->where('asistencias.participante_id', $participante_id)
            ->countAllResults();

        $porcentaje = $total_sesiones > 0 
            ? round(($asistencias_curso / $total_sesiones) * 100, 1)
            : 0;

        return [
            'total_sesiones' => $total_sesiones,
            'asistencias' => $asistencias_curso,
            'porcentaje' => $porcentaje
        ];
    }
}