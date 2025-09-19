<?php

namespace App\Controllers;

use App\Models\CursoModel; // Asegúrate de crear este modelo
use App\Models\ModuloModel;
use App\Models\UserModel; 
use App\Models\CursoDocenteModel;
use App\Models\CursoParticipanteModel;
use Config\Database;

class CursoController extends BaseController
{
    // Muestra la lista de cursos
    public function index()
    {
        $cursoModel = new CursoModel();
        $userRole = session('rol_nombre');
        $userId = session('user_id');
        $cursos = [];

        if ($userRole === 'superadmin') {
            // El superadmin ve todos los cursos, sin filtro.
            $cursos = $cursoModel->findAll();
        } 
        else if ($userRole === 'admin') {
            // El admin/docente solo ve los cursos a los que está asignado.
            $cursos = $cursoModel
                ->select('cursos.*')
                ->join('curso_docente', 'curso_docente.curso_id = cursos.id')
                ->where('curso_docente.docente_id', $userId)
                ->findAll();
        } else {
            // Cualquier otro rol (como 'usuario') es redirigido a su panel.
            return redirect()->to('mi-panel')->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        return view('cursos/index', ['cursos' => $cursos]);
    }

    public function asignar($curso_id)
    {
        if (session('rol_nombre') !== 'superadmin') {
            return redirect()->to('cursos')->with('error', 'Solo los superadministradores pueden asignar cursos.');
        }

        $cursoModel = new CursoModel();
        $userModel = new UserModel();
        $cursoDocenteModel = new CursoDocenteModel();

        $data = [
            'curso' => $cursoModel->find($curso_id),
            // Obtiene solo los usuarios que son 'admin' (docentes)
            'docentes' => $userModel->join('roles', 'roles.id = users.rol_id')->where('roles.nombre', 'admin')->findAll(),
            // Obtiene los IDs de los docentes ya asignados a este curso
            'asignados' => array_column($cursoDocenteModel->where('curso_id', $curso_id)->findAll(), 'docente_id')
        ];
        
        return view('cursos/asignar', $data);
    }

    public function guardarAsignacion($curso_id)
    {
        if (session('rol_nombre') !== 'superadmin') {
            return redirect()->to('cursos');
        }

        $cursoDocenteModel = new CursoDocenteModel();
        
        // Obtenemos los IDs de los docentes seleccionados en el formulario
        $docentesSeleccionados = $this->request->getPost('docentes') ?? [];

        // Borramos las asignaciones anteriores para este curso
        $cursoDocenteModel->where('curso_id', $curso_id)->delete();

        // Creamos un array para insertar las nuevas asignaciones
        $nuevasAsignaciones = [];
        foreach ($docentesSeleccionados as $docente_id) {
            $nuevasAsignaciones[] = [
                'curso_id' => $curso_id,
                'docente_id' => $docente_id
            ];
        }

        // Si hay nuevas asignaciones, las insertamos en lote
        if (!empty($nuevasAsignaciones)) {
            $cursoDocenteModel->insertBatch($nuevasAsignaciones);
        }

        return redirect()->to('cursos')->with('success', 'Asignaciones guardadas correctamente.');
    }

    // Muestra un curso específico
    public function show($id)
    {
        $cursoModel = new CursoModel();
        $data['curso'] = $cursoModel->find($id);
        // Carga la vista para el detalle del curso.
    }

    // Muestra el formulario para crear un nuevo curso
    public function new()
    {
        // Carga la vista con el formulario de creación.
    }

    // Procesa la creación de un nuevo curso
    public function create()
    {
        // PASO 1: Simplificamos las reglas. Quitamos la comparación de fechas de aquí.
        $rules = [
            'nombre' => 'required|min_length[3]|max_length[100]|is_unique[cursos.nombre]',
            'descripcion' => 'required|min_length[10]',
            'fecha_inicio' => 'required|valid_date',
            'fecha_fin' => 'required|valid_date', // <-- Regla simplificada
            'estado' => 'required|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', aethis->validator->getErrors());
        }

        // PASO 2: Verificación manual de las fechas
        $fechaInicio = $this->request->getPost('fecha_inicio');
        $fechaFin = $this->request->getPost('fecha_fin');

        // Usamos strtotime para convertir las fechas en números y poder compararlas fácilmente
        if (strtotime($fechaFin) < strtotime($fechaInicio)) {
            // Si la fecha de fin es menor, mandamos un error personalizado y volvemos atrás.
            return redirect()->back()->withInput()->with('errors', [
                'fecha_fin' => 'La fecha de fin no puede ser anterior a la fecha de inicio.'
            ]);
        }

        // PASO 3: Si todo está bien, guardamos los datos (esto no cambia)
        $cursoModel = new CursoModel();
        $cursoModel->save([
            'nombre' => $this->request->getPost('nombre'),
            'descripcion' => $this->request->getPost('descripcion'),
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'estado' => $this->request->getPost('estado'),
            'created_by' => session('user_id'),
        ]);

        return redirect()->to('/cursos')->with('success', '¡Curso creado exitosamente!');
    }

    public function update($id)
    {
        // Hacemos la misma simplificación aquí
        $rules = [
            'descripcion' => 'required|min_length[10]',
            'fecha_inicio' => 'required|valid_date',
            'fecha_fin' => 'required|valid_date', // <-- Regla simplificada
            'estado' => 'required|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Verificación manual de las fechas también en la actualización
        $fechaInicio = $this->request->getPost('fecha_inicio');
        $fechaFin = $this->request->getPost('fecha_fin');

        if (strtotime($fechaFin) < strtotime($fechaInicio)) {
            return redirect()->back()->withInput()->with('errors', [
                'fecha_fin' => 'La fecha de fin no puede ser anterior a la fecha de inicio.'
            ]);
        }

        // Si todo está bien, actualizamos (esto no cambia)
        $cursoModel = new CursoModel();
        $data = [
            'descripcion' => $this->request->getPost('descripcion'),
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'estado' => $this->request->getPost('estado'),
        ];
        $cursoModel->update($id, $data);

        return redirect()->to('/cursos')->with('success', '¡Curso actualizado exitosamente!');
    }

    public function delete($id)
    {
        // PASO 1: Pedir la conexión a la base de datos.
        $db = \Config\Database::connect();

        $cursoModel = new CursoModel();
        $moduloModel = new ModuloModel();

        $curso = $cursoModel->find($id);
        if ($curso === null) {
            return redirect()->to('/cursos')->with('error', 'El curso que intentas eliminar no existe.');
        }

        // PASO 2: Usar la variable $db para la transacción.
        $db->transStart();

        // Borra todos los módulos que pertenecen a este curso
        $moduloModel->where('curso_id', $id)->delete();
        
        // Ahora, borra el curso en sí
        $cursoModel->delete($id);

        // PASO 3: Completar la transacción usando $db.
        $db->transComplete();

        // Verifica si la transacción fue exitosa
        if ($db->transStatus() === false) {
            return redirect()->to('/cursos')->with('error', 'No se pudo eliminar el curso debido a un error en la base de datos.');
        }

        return redirect()->to('/cursos')->with('success', 'Curso y sus módulos han sido eliminados exitosamente.');
    }

    public function inscribir($curso_id)
    {
        // Solo roles administrativos pueden inscribir participantes
        if (session('rol_nombre') !== 'superadmin' && session('rol_nombre') !== 'admin') {
            return redirect()->to('cursos')->with('error', 'No tienes permiso para realizar esta acción.');
        }

        $cursoModel = new CursoModel();
        $userModel = new UserModel();
        $cursoParticipanteModel = new CursoParticipanteModel();

        // Verificar que el curso existe
        $curso = $cursoModel->find($curso_id);
        if (!$curso) {
            return redirect()->to('cursos')->with('error', 'El curso no existe.');
        }

        // Para admin (docentes), verificar que están asignados al curso
        if (session('rol_nombre') === 'admin') {
            $cursoDocenteModel = new CursoDocenteModel();
            $asignacion = $cursoDocenteModel
                ->where('curso_id', $curso_id)
                ->where('docente_id', session('user_id'))
                ->first();
            
            if (!$asignacion) {
                return redirect()->to('cursos')->with('error', 'No tienes permiso para gestionar este curso.');
            }
        }

        $data = [
            'curso' => $curso,
            // Obtiene solo los usuarios activos que son 'usuario' (participantes)
            'participantes' => $userModel
                ->select('users.id, users.nombres, users.apellidos, users.dni, users.correo')
                ->join('roles', 'roles.id = users.rol_id')
                ->where('roles.nombre', 'usuario')
                ->where('users.estado', 1) // Solo usuarios activos
                ->orderBy('users.nombres', 'ASC')
                ->findAll(),
            // Obtiene los IDs de los participantes ya inscritos en este curso
            'inscritos' => array_column(
                $cursoParticipanteModel->where('curso_id', $curso_id)->findAll(), 
                'participante_id'
            )
        ];
        
        return view('cursos/inscribir', $data);
    }

    /**
     * Guarda la lista de participantes inscritos en un curso
     */
    public function guardarInscripcion($curso_id)
    {
        // Verificar permisos
        if (session('rol_nombre') !== 'superadmin' && session('rol_nombre') !== 'admin') {
            return redirect()->to('cursos')->with('error', 'No tienes permiso para realizar esta acción.');
        }

        // Validar que el curso existe
        $cursoModel = new CursoModel();
        $curso = $cursoModel->find($curso_id);
        if (!$curso) {
            return redirect()->to('cursos')->with('error', 'El curso no existe.');
        }

        // Para admin (docentes), verificar que están asignados al curso
        if (session('rol_nombre') === 'admin') {
            $cursoDocenteModel = new CursoDocenteModel();
            $asignacion = $cursoDocenteModel
                ->where('curso_id', $curso_id)
                ->where('docente_id', session('user_id'))
                ->first();
            
            if (!$asignacion) {
                return redirect()->to('cursos')->with('error', 'No tienes permiso para gestionar este curso.');
            }
        }

        // Obtener participantes seleccionados
        $participantesSeleccionados = $this->request->getPost('participantes') ?? [];
        
        // Validar que todos los IDs sean numéricos válidos
        $participantesValidos = [];
        foreach ($participantesSeleccionados as $pid) {
            $pid = (int) $pid;
            if ($pid > 0) {
                $participantesValidos[] = $pid;
            }
        }

        // Si hay participantes seleccionados, verificar que existen y son usuarios válidos
        if (!empty($participantesValidos)) {
            $userModel = new UserModel();
            $participantesExistentes = $userModel
                ->select('users.id')
                ->join('roles', 'roles.id = users.rol_id')
                ->where('roles.nombre', 'usuario')
                ->where('users.estado', 1)
                ->whereIn('users.id', $participantesValidos)
                ->findAll();
            
            $idsExistentes = array_column($participantesExistentes, 'id');
            $participantesValidos = array_intersect($participantesValidos, $idsExistentes);
        }

        $db = Database::connect();
        $cursoParticipanteModel = new CursoParticipanteModel();

        // Iniciar transacción
        $db->transStart();

        try {
            // Eliminar todas las inscripciones actuales del curso
            $cursoParticipanteModel->where('curso_id', $curso_id)->delete();

            // Insertar las nuevas inscripciones si hay participantes válidos
            if (!empty($participantesValidos)) {
                $insertData = [];
                foreach ($participantesValidos as $participante_id) {
                    $insertData[] = [
                        'curso_id' => (int) $curso_id,
                        'participante_id' => $participante_id
                    ];
                }
                $cursoParticipanteModel->insertBatch($insertData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Error en la transacción de base de datos');
            }

            $total = count($participantesValidos);
            if ($total > 0) {
                $mensaje = "Se inscribieron {$total} participante(s) al curso exitosamente.";
            } else {
                $mensaje = "Se eliminaron todos los participantes del curso.";
            }

            return redirect()->to("cursos/inscribir/{$curso_id}")
                           ->with('success', $mensaje);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error en guardarInscripcion: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('errors', ['Error al guardar las inscripciones. Intente nuevamente.']);
        }
    }

    /**
     * Quita un participante específico de un curso
     */
    public function quitarInscripcion($curso_id, $participante_id)
    {
        // Verificar permisos
        if (session('rol_nombre') !== 'superadmin' && session('rol_nombre') !== 'admin') {
            return redirect()->to('cursos')->with('error', 'No tienes permiso para realizar esta acción.');
        }

        // Validar parámetros
        $curso_id = (int) $curso_id;
        $participante_id = (int) $participante_id;
        
        if ($curso_id <= 0 || $participante_id <= 0) {
            return redirect()->back()->with('error', 'Parámetros inválidos.');
        }

        // Verificar que el curso existe
        $cursoModel = new CursoModel();
        $curso = $cursoModel->find($curso_id);
        if (!$curso) {
            return redirect()->to('cursos')->with('error', 'El curso no existe.');
        }

        // Para admin (docentes), verificar que están asignados al curso
        if (session('rol_nombre') === 'admin') {
            $cursoDocenteModel = new CursoDocenteModel();
            $asignacion = $cursoDocenteModel
                ->where('curso_id', $curso_id)
                ->where('docente_id', session('user_id'))
                ->first();
            
            if (!$asignacion) {
                return redirect()->to('cursos')->with('error', 'No tienes permiso para gestionar este curso.');
            }
        }

        try {
            $cursoParticipanteModel = new CursoParticipanteModel();
            
            // Verificar que la inscripción existe
            $inscripcion = $cursoParticipanteModel
                ->where('curso_id', $curso_id)
                ->where('participante_id', $participante_id)
                ->first();
            
            if (!$inscripcion) {
                return redirect()->to("cursos/inscribir/{$curso_id}")
                               ->with('error', 'El participante no está inscrito en este curso.');
            }

            // Eliminar la inscripción
            $eliminado = $cursoParticipanteModel
                ->where('curso_id', $curso_id)
                ->where('participante_id', $participante_id)
                ->delete();

            if ($eliminado) {
                return redirect()->to("cursos/inscribir/{$curso_id}")
                               ->with('success', 'Participante removido del curso exitosamente.');
            } else {
                return redirect()->to("cursos/inscribir/{$curso_id}")
                               ->with('error', 'No se pudo remover al participante del curso.');
            }

        } catch (\Exception $e) {
            log_message('error', 'Error en quitarInscripcion: ' . $e->getMessage());
            return redirect()->to("cursos/inscribir/{$curso_id}")
                           ->with('error', 'Error al remover al participante. Intente nuevamente.');
        }
    }

    /**
     * Quita múltiples participantes de un curso
     */
    public function quitarInscripcionesMultiples($curso_id)
    {
        // Verificar permisos
        if (session('rol_nombre') !== 'superadmin' && session('rol_nombre') !== 'admin') {
            return redirect()->to('cursos')->with('error', 'No tienes permiso para realizar esta acción.');
        }

        // Validar parámetros
        $curso_id = (int) $curso_id;
        if ($curso_id <= 0) {
            return redirect()->back()->with('error', 'ID de curso inválido.');
        }

        // Verificar que el curso existe
        $cursoModel = new CursoModel();
        $curso = $cursoModel->find($curso_id);
        if (!$curso) {
            return redirect()->to('cursos')->with('error', 'El curso no existe.');
        }

        // Para admin (docentes), verificar que están asignados al curso
        if (session('rol_nombre') === 'admin') {
            $cursoDocenteModel = new CursoDocenteModel();
            $asignacion = $cursoDocenteModel
                ->where('curso_id', $curso_id)
                ->where('docente_id', session('user_id'))
                ->first();
            
            if (!$asignacion) {
                return redirect()->to('cursos')->with('error', 'No tienes permiso para gestionar este curso.');
            }
        }

        // Obtener participantes a quitar
        $participantesQuitar = $this->request->getPost('participantes_quitar') ?? [];
        
        if (empty($participantesQuitar)) {
            return redirect()->to("cursos/inscribir/{$curso_id}")
                           ->with('error', 'No se seleccionaron participantes para quitar.');
        }

        // Validar que todos los IDs sean numéricos válidos
        $participantesValidos = [];
        foreach ($participantesQuitar as $pid) {
            $pid = (int) $pid;
            if ($pid > 0) {
                $participantesValidos[] = $pid;
            }
        }

        if (empty($participantesValidos)) {
            return redirect()->to("cursos/inscribir/{$curso_id}")
                           ->with('error', 'IDs de participantes inválidos.');
        }

        try {
            $cursoParticipanteModel = new CursoParticipanteModel();
            $db = Database::connect();
            
            // Iniciar transacción
            $db->transStart();

            // Eliminar las inscripciones seleccionadas
            $eliminados = $cursoParticipanteModel
                ->where('curso_id', $curso_id)
                ->whereIn('participante_id', $participantesValidos)
                ->delete();

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Error en la transacción de base de datos');
            }

            $total = count($participantesValidos);
            $mensaje = $total === 1 
                ? "Se quitó 1 participante del curso exitosamente."
                : "Se quitaron {$total} participantes del curso exitosamente.";

            return redirect()->to("cursos/inscribir/{$curso_id}")
                           ->with('success', $mensaje);

        } catch (\Exception $e) {
            log_message('error', 'Error en quitarInscripcionesMultiples: ' . $e->getMessage());
            return redirect()->to("cursos/inscribir/{$curso_id}")
                           ->with('error', 'Error al quitar participantes. Intente nuevamente.');
        }
    }

    /**
     * Obtiene la lista de participantes inscritos en un curso (para AJAX o API)
     */
    public function obtenerParticipantes($curso_id)
    {
        // Verificar permisos
        if (session('rol_nombre') !== 'superadmin' && session('rol_nombre') !== 'admin') {
            return $this->response->setJSON(['error' => 'Sin permisos'])->setStatusCode(403);
        }

        $curso_id = (int) $curso_id;
        if ($curso_id <= 0) {
            return $this->response->setJSON(['error' => 'ID de curso inválido'])->setStatusCode(400);
        }

        try {
            $userModel = new UserModel();
            $participantes = $userModel
                ->select('users.id, users.nombres, users.apellidos, users.dni, users.correo')
                ->join('curso_participante', 'curso_participante.participante_id = users.id')
                ->join('roles', 'roles.id = users.rol_id')
                ->where('curso_participante.curso_id', $curso_id)
                ->where('roles.nombre', 'usuario')
                ->orderBy('users.nombres', 'ASC')
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'participantes' => $participantes,
                'total' => count($participantes)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en obtenerParticipantes: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Error interno del servidor'])->setStatusCode(500);
        }
    }

}