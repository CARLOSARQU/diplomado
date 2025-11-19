<?php

namespace App\Controllers;

use App\Models\NotaModuloModel;
use App\Models\CursoParticipanteModel;
use App\Models\ModuloModel;
use App\Models\CursoModel;

class NotasController extends BaseController
{
    protected $notaModuloModel;
    protected $cursoParticipanteModel;
    protected $moduloModel;
    protected $cursoModel;

    public function __construct()
    {
        $this->notaModuloModel = new NotaModuloModel();
        $this->cursoParticipanteModel = new CursoParticipanteModel();
        $this->moduloModel = new ModuloModel();
        $this->cursoModel = new CursoModel();
    }

    /**
     * Vista de notas para el participante (similar al dashboard)
     */
    public function misNotas()
    {
        // Verificar que el usuario sea participante
        if (session('rol_id') != 3) { // 3 = participante
            return redirect()->to('/')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $participante_id = session('user_id');

        // Obtener cursos inscritos del participante
        $cursosInscritos = $this->cursoParticipanteModel->getCursosByParticipante($participante_id);

        $misCursos = [];
        foreach ($cursosInscritos as $inscripcion) {
            $curso_id = $inscripcion['curso_id'];
            
            // Obtener módulos del curso
            $modulos = $this->moduloModel->where('curso_id', $curso_id)->orderBy('orden', 'ASC')->findAll();
            
            // Obtener notas por módulo
            $notasPorModulo = [];
            $totalNotas = 0;
            $sumaNotas = 0;
            $modulosConNota = 0;

            foreach ($modulos as $modulo) {
                $nota = $this->notaModuloModel->getNotaParticipanteByModulo($participante_id, $modulo['id']);
                
                $notasPorModulo[] = [
                    'modulo_id' => $modulo['id'],
                    'modulo_nombre' => $modulo['nombre'],
                    'orden' => $modulo['orden'],
                    'nota' => $nota['nota'] ?? null,
                    'observaciones' => $nota['observaciones'] ?? null,
                    'fecha_registro' => $nota['fecha_registro'] ?? null,
                    'tiene_nota' => isset($nota['nota'])
                ];

                if (isset($nota['nota'])) {
                    $sumaNotas += $nota['nota'];
                    $modulosConNota++;
                }
                $totalNotas++;
            }

            // Calcular promedio
            $promedio = $modulosConNota > 0 ? round($sumaNotas / $modulosConNota, 2) : null;

            $misCursos[] = [
                'curso_id' => $curso_id,
                'curso_nombre' => $inscripcion['nombre'],
                'curso_descripcion' => $inscripcion['descripcion'],
                'fecha_inicio' => $inscripcion['fecha_inicio'],
                'fecha_fin' => $inscripcion['fecha_fin'],
                'estado' => $inscripcion['estado'],
                'modulos' => $notasPorModulo,
                'total_modulos' => $totalNotas,
                'modulos_calificados' => $modulosConNota,
                'promedio' => $promedio,
                'progreso_porcentaje' => $totalNotas > 0 ? round(($modulosConNota / $totalNotas) * 100) : 0
            ];
        }

        // Calcular estadísticas generales
        $totalCursos = count($misCursos);
        $totalModulos = 0;
        $totalCalificados = 0;
        $sumaPromedios = 0;
        $cursosConPromedio = 0;

        foreach ($misCursos as $curso) {
            $totalModulos += $curso['total_modulos'];
            $totalCalificados += $curso['modulos_calificados'];
            if ($curso['promedio'] !== null) {
                $sumaPromedios += $curso['promedio'];
                $cursosConPromedio++;
            }
        }

        $promedioGeneral = $cursosConPromedio > 0 ? round($sumaPromedios / $cursosConPromedio, 2) : null;

        $estadisticas = [
            'cursos_inscritos' => $totalCursos,
            'total_modulos' => $totalModulos,
            'modulos_calificados' => $totalCalificados,
            'promedio_general' => $promedioGeneral,
            'porcentaje_progreso' => $totalModulos > 0 ? round(($totalCalificados / $totalModulos) * 100) : 0
        ];

        $data = [
            'mis_cursos' => $misCursos,
            'estadisticas' => $estadisticas
        ];

        return view('participante/mis-notas', $data);
    }

    /**
     * Ver notas de un curso específico (para participante)
     */
    public function verNotasCurso($curso_id)
    {
        // Verificar que el usuario sea participante
        if (session('rol_id') != 3) {
            return redirect()->to('/')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $participante_id = session('user_id');

        // Verificar que el participante esté inscrito en el curso
        if (!$this->cursoParticipanteModel->isParticipanteInscrito($curso_id, $participante_id)) {
            return redirect()->to('participante/mis-notas')->with('error', 'No estás inscrito en este curso.');
        }

        // Obtener información del curso
        $curso = $this->cursoModel->find($curso_id);
        if (!$curso) {
            return redirect()->to('participante/mis-notas')->with('error', 'Curso no encontrado.');
        }

        // Obtener notas del participante en el curso
        $notas = $this->notaModuloModel->getNotasParticipanteByCurso($participante_id, $curso_id);

        // Calcular promedio
        $promedio = $this->notaModuloModel->getPromedioParticipanteByCurso($participante_id, $curso_id);

        $data = [
            'curso' => $curso,
            'notas' => $notas,
            'promedio' => $promedio
        ];

        return view('participante/notas_curso_detalle', $data);
    }

    /**
     * Gestionar notas de un módulo (para docentes/admin)
     */
    public function gestionarNotas($modulo_id)
    {
        // Verificar permisos (solo admin o docente)
        if (!in_array(session('rol_id'), [1, 2])) { // 1 = admin, 2 = docente
            return redirect()->to('/')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        // Obtener información del módulo
        $modulo = $this->moduloModel->find($modulo_id);
        if (!$modulo) {
            return redirect()->back()->with('error', 'Módulo no encontrado.');
        }

        // Obtener curso
        $curso = $this->cursoModel->find($modulo['curso_id']);

        // Obtener participantes del curso
        $participantes = $this->cursoParticipanteModel->getParticipantesByCurso($modulo['curso_id']);

        // Obtener notas existentes del módulo
        $notasExistentes = $this->notaModuloModel->getNotasByModulo($modulo_id);

        // Mapear notas por participante_id
        $notasMap = [];
        foreach ($notasExistentes as $nota) {
            $notasMap[$nota['participante_id']] = $nota;
        }

        // Combinar participantes con sus notas
        $participantesConNotas = [];
        foreach ($participantes as $participante) {
            $participante_id = $participante['participante_id'];
            $participantesConNotas[] = [
                'participante_id' => $participante_id,
                'nombres' => $participante['nombres'],
                'apellidos' => $participante['apellidos'],
                'dni' => $participante['dni'],
                'correo' => $participante['correo'],
                'nota' => $notasMap[$participante_id]['nota'] ?? null,
                'observaciones' => $notasMap[$participante_id]['observaciones'] ?? null,
                'fecha_registro' => $notasMap[$participante_id]['fecha_registro'] ?? null
            ];
        }

        // Obtener estadísticas del módulo
        $estadisticas = $this->notaModuloModel->getEstadisticasModulo($modulo_id);

        $data = [
            'modulo' => $modulo,
            'curso' => $curso,
            'participantes' => $participantesConNotas,
            'estadisticas' => $estadisticas
        ];

        return view('notas/gestionar_notas', $data);
    }

    /**
     * Guardar nota de un participante en un módulo
     */
    public function guardarNota()
    {
        // Verificar permisos
        if (!in_array(session('rol_id'), [1, 2])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción.'
            ]);
        }

        $modulo_id = $this->request->getPost('modulo_id');
        $participante_id = $this->request->getPost('participante_id');
        $nota = $this->request->getPost('nota');
        $observaciones = $this->request->getPost('observaciones');

        // Validar nota
        if ($nota !== '' && $nota !== null) {
            $nota = floatval($nota);
            if ($nota < 0 || $nota > 20) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'La nota debe estar entre 0 y 20.'
                ]);
            }
        } else {
            $nota = null;
        }

        // Registrar nota
        $resultado = $this->notaModuloModel->registrarNota(
            $modulo_id,
            $participante_id,
            $nota,
            $observaciones,
            session('user_id')
        );

        if ($resultado) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Nota guardada exitosamente.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al guardar la nota.'
            ]);
        }
    }

    /**
     * Reporte completo de notas de un curso (opcional)
     */
    public function reporteCurso($curso_id)
    {
        // Verificar permisos
        if (!in_array(session('rol_id'), [1, 2])) {
            return redirect()->to('/')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        // Obtener curso
        $curso = $this->cursoModel->find($curso_id);
        if (!$curso) {
            return redirect()->back()->with('error', 'Curso no encontrado.');
        }

        // Obtener todos los módulos del curso
        $modulos = $this->moduloModel->where('curso_id', $curso_id)->orderBy('orden', 'ASC')->findAll();

        // Obtener todos los participantes del curso
        $participantes = $this->cursoParticipanteModel->getParticipantesByCurso($curso_id);

        // Construir matriz de notas
        $reporte = [];
        foreach ($participantes as $participante) {
            $participante_id = $participante['participante_id'];
            $notasParticipante = [];
            $sumaNotas = 0;
            $contadorNotas = 0;

            foreach ($modulos as $modulo) {
                $nota = $this->notaModuloModel->getNotaParticipanteByModulo($participante_id, $modulo['id']);
                $notasParticipante[$modulo['id']] = $nota['nota'] ?? null;
                
                if ($nota && isset($nota['nota'])) {
                    $sumaNotas += $nota['nota'];
                    $contadorNotas++;
                }
            }

            $promedio = $contadorNotas > 0 ? round($sumaNotas / $contadorNotas, 2) : null;

            $reporte[] = [
                'participante_id' => $participante_id,
                'dni' => $participante['dni'],
                'nombres' => $participante['nombres'],
                'apellidos' => $participante['apellidos'],
                'correo' => $participante['correo'],
                'notas' => $notasParticipante,
                'promedio' => $promedio
            ];
        }

        $data = [
            'curso' => $curso,
            'modulos' => $modulos,
            'reporte' => $reporte
        ];

        return view('notas/reporte_curso', $data);
    }
}