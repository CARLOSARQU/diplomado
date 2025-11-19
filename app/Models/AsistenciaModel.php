<?php

namespace App\Models;

use CodeIgniter\Model;

class AsistenciaModel extends Model
{
    protected $table = 'asistencias';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['sesion_id', 'participante_id', 'hora_registro', 'observaciones'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'sesion_id' => 'required|integer|is_not_unique[sesiones.id]',
        'participante_id' => 'required|integer|is_not_unique[users.id]',
        'hora_registro' => 'permit_empty|valid_date[Y-m-d H:i:s]'
    ];

    protected $validationMessages = [
        'sesion_id' => [
            'required' => 'El ID de la sesión es obligatorio.',
            'integer' => 'El ID de la sesión debe ser un número entero.',
            'is_not_unique' => 'La sesión especificada no existe.'
        ],
        'participante_id' => [
            'required' => 'El ID del participante es obligatorio.',
            'integer' => 'El ID del participante debe ser un número entero.',
            'is_not_unique' => 'El participante especificado no existe.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['setHoraRegistro'];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Establece la hora de registro automáticamente si no se proporciona
     */
    protected function setHoraRegistro(array $data)
    {
        if (!isset($data['data']['hora_registro']) || empty($data['data']['hora_registro'])) {
            $data['data']['hora_registro'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Verifica si un participante ya registró asistencia para una sesión
     */
    public function yaRegistroAsistencia($sesion_id, $participante_id)
    {
        return $this->where('sesion_id', $sesion_id)
            ->where('participante_id', $participante_id)
            ->countAllResults() > 0;
    }

    /**
     * Obtiene todas las asistencias de una sesión
     */
    public function getAsistenciasBySesion($sesion_id)
    {
        return $this->select('asistencias.*, users.nombres, users.apellidos, users.dni')
            ->join('users', 'users.id = asistencias.participante_id')
            ->where('asistencias.sesion_id', $sesion_id)
            ->orderBy('asistencias.created_at', 'ASC')
            ->findAll();
    }

    /**
     * Obtiene todas las asistencias de un participante
     */
    public function getAsistenciasByParticipante($participante_id)
    {
        $db = \Config\Database::connect();

        $query = $db->query("
        SELECT 
            a.id,
            a.sesion_id,
            a.participante_id,
            a.hora_registro,
            a.observaciones,
            a.created_at,
            a.updated_at,
            s.titulo,
            s.fecha,
            s.hora_inicio,
            s.hora_fin,
            c.nombre as curso_nombre,
            m.nombre as modulo_nombre
        FROM asistencias a
        INNER JOIN sesiones s ON s.id = a.sesion_id
        INNER JOIN modulos m ON m.id = s.modulo_id  
        INNER JOIN cursos c ON c.id = m.curso_id
        INNER JOIN curso_participante cp ON cp.curso_id = c.id AND cp.participante_id = a.participante_id
        WHERE a.participante_id = ?
        ORDER BY s.fecha DESC, s.hora_inicio DESC
    ", [$participante_id]);

        $result = $query->getResultArray();

        log_message('debug', 'getAsistenciasByParticipante - Participante: ' . $participante_id . ' - Resultados: ' . count($result));

        return $result;
    }


    /**
     * Obtiene estadísticas de asistencia de un curso
     */
    public function getEstadisticasCurso($curso_id)
    {
        $db = \Config\Database::connect();

        // Total de sesiones del curso
        $totalSesiones = $db->table('sesiones')
            ->join('modulos', 'modulos.id = sesiones.modulo_id')
            ->where('modulos.curso_id', $curso_id)
            ->where('sesiones.fecha <=', date('Y-m-d'))
            ->countAllResults();

        // Total de asistencias registradas
        $totalAsistencias = $this->select('asistencias.*')
            ->join('sesiones', 'sesiones.id = asistencias.sesion_id')
            ->join('modulos', 'modulos.id = sesiones.modulo_id')
            ->where('modulos.curso_id', $curso_id)
            ->countAllResults();

        // Participantes únicos que han asistido
        $participantesUnicos = $this->select('DISTINCT asistencias.participante_id')
            ->join('sesiones', 'sesiones.id = asistencias.sesion_id')
            ->join('modulos', 'modulos.id = sesiones.modulo_id')
            ->where('modulos.curso_id', $curso_id)
            ->countAllResults();

        return [
            'total_sesiones' => $totalSesiones,
            'total_asistencias' => $totalAsistencias,
            'participantes_unicos' => $participantesUnicos,
            'promedio_asistencia' => $totalSesiones > 0 ? round($totalAsistencias / $totalSesiones, 2) : 0
        ];
    }

    /**
     * Obtiene estadísticas de asistencia de un participante en un curso específico
     */
    public function getEstadisticasParticipanteCurso($participante_id, $curso_id)
    {
        $db = \Config\Database::connect();

        // Total de sesiones del curso (hasta la fecha actual)
        $totalSesiones = $db->table('sesiones')
            ->join('modulos', 'modulos.id = sesiones.modulo_id')
            ->where('modulos.curso_id', $curso_id)
            ->where('sesiones.fecha <=', date('Y-m-d'))
            ->countAllResults();

        // Asistencias del participante en este curso
        $asistenciasParticipante = $this->select('asistencias.*')
            ->join('sesiones', 'sesiones.id = asistencias.sesion_id')
            ->join('modulos', 'modulos.id = sesiones.modulo_id')
            ->where('modulos.curso_id', $curso_id)
            ->where('asistencias.participante_id', $participante_id)
            ->countAllResults();

        $porcentaje = $totalSesiones > 0 ? round(($asistenciasParticipante / $totalSesiones) * 100, 1) : 0;

        return [
            'total_sesiones' => $totalSesiones,
            'asistencias' => $asistenciasParticipante,
            'porcentaje' => $porcentaje
        ];
    }

    /**
     * Registra una nueva asistencia con validaciones
     */
    public function registrarAsistencia($sesion_id, $participante_id, $observaciones = null)
    {
        // Verificar que no existe ya
        if ($this->yaRegistroAsistencia($sesion_id, $participante_id)) {
            return [
                'success' => false,
                'message' => 'Ya has registrado asistencia para esta sesión.'
            ];
        }

        $data = [
            'sesion_id' => $sesion_id,
            'participante_id' => $participante_id,
            'hora_registro' => date('Y-m-d H:i:s'),
            'observaciones' => $observaciones
        ];

        if ($this->insert($data)) {
            return [
                'success' => true,
                'message' => 'Asistencia registrada exitosamente.',
                'id' => $this->getInsertID()
            ];
        }

        return [
            'success' => false,
            'message' => 'Error al registrar la asistencia.'
        ];
    }

    /**
     * Obtiene el reporte de asistencias por fecha
     */
    public function getReportePorFecha($fecha_inicio, $fecha_fin = null)
    {
        if (!$fecha_fin) {
            $fecha_fin = $fecha_inicio;
        }

        return $this->select('asistencias.*, users.nombres, users.apellidos, users.dni,
                         sesiones.titulo, sesiones.fecha, sesiones.hora_inicio, sesiones.hora_fin,
                         cursos.id as curso_id, cursos.nombre as curso_nombre, 
                         modulos.nombre as modulo_nombre')
            ->join('users', 'users.id = asistencias.participante_id')
            ->join('sesiones', 'sesiones.id = asistencias.sesion_id')
            ->join('modulos', 'modulos.id = sesiones.modulo_id')
            ->join('cursos', 'cursos.id = modulos.curso_id')
            ->where('sesiones.fecha >=', $fecha_inicio)
            ->where('sesiones.fecha <=', $fecha_fin)
            ->orderBy('sesiones.fecha', 'ASC')
            ->orderBy('sesiones.hora_inicio', 'ASC')
            ->orderBy('users.apellidos', 'ASC')
            ->findAll();
    }

    /**
     * Elimina todas las asistencias de una sesión
     */
    public function eliminarAsistenciasBySesion($sesion_id)
    {
        return $this->where('sesion_id', $sesion_id)->delete();
    }

    /**
     * Elimina todas las asistencias de un participante
     */
    public function eliminarAsistenciasByParticipante($participante_id)
    {
        return $this->where('participante_id', $participante_id)->delete();
    }

    /**
     * Obtiene las últimas asistencias registradas
     */
    public function getUltimasAsistencias($limite = 10)
    {
        return $this->select('asistencias.*, users.nombres, users.apellidos,
                             sesiones.titulo, sesiones.fecha, cursos.nombre as curso_nombre')
            ->join('users', 'users.id = asistencias.participante_id')
            ->join('sesiones', 'sesiones.id = asistencias.sesion_id')
            ->join('modulos', 'modulos.id = sesiones.modulo_id')
            ->join('cursos', 'cursos.id = modulos.curso_id')
            ->orderBy('asistencias.created_at', 'DESC')
            ->limit($limite)
            ->findAll();
    }
}