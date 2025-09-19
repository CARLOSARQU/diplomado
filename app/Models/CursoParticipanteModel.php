<?php

namespace App\Models;

use CodeIgniter\Model;

class CursoParticipanteModel extends Model
{
    protected $table = 'curso_participante';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['curso_id', 'participante_id'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'curso_id' => 'required|integer|is_not_unique[cursos.id]',
        'participante_id' => 'required|integer|is_not_unique[users.id]'
    ];

    protected $validationMessages = [
        'curso_id' => [
            'required' => 'El ID del curso es obligatorio.',
            'integer' => 'El ID del curso debe ser un número entero.',
            'is_not_unique' => 'El curso especificado no existe.'
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
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Obtiene todos los participantes de un curso específico
     */
    public function getParticipantesByCurso($curso_id)
    {
        return $this->select('curso_participante.*, users.nombres, users.apellidos, users.dni, users.correo')
            ->join('users', 'users.id = curso_participante.participante_id')
            ->where('curso_participante.curso_id', $curso_id)
            ->orderBy('users.nombres', 'ASC')
            ->findAll();
    }

    /**
     * Obtiene todos los cursos de un participante específico
     */
    public function getCursosByParticipante($participante_id)
    {
        return $this->select('curso_participante.*, cursos.nombre, cursos.descripcion, cursos.fecha_inicio, cursos.fecha_fin, cursos.estado')
            ->join('cursos', 'cursos.id = curso_participante.curso_id')
            ->where('curso_participante.participante_id', $participante_id)
            ->orderBy('cursos.fecha_inicio', 'DESC')
            ->findAll();
    }

    /**
     * Verifica si un participante está inscrito en un curso
     */
    public function isParticipanteInscrito($curso_id, $participante_id)
    {
        return $this->where('curso_id', $curso_id)
            ->where('participante_id', $participante_id)
            ->countAllResults() > 0;
    }

    /**
     * Cuenta el número total de participantes en un curso
     */
    public function countParticipantesByCurso($curso_id)
    {
        return $this->where('curso_id', $curso_id)->countAllResults();
    }

    /**
     * Cuenta el número total de cursos de un participante
     */
    public function countCursosByParticipante($participante_id)
    {
        return $this->where('participante_id', $participante_id)->countAllResults();
    }

    /**
     * Elimina todas las inscripciones de un curso
     */
    public function eliminarInscripcionesByCurso($curso_id)
    {
        return $this->where('curso_id', $curso_id)->delete();
    }

    /**
     * Elimina todas las inscripciones de un participante
     */
    public function eliminarInscripcionesByParticipante($participante_id)
    {
        return $this->where('participante_id', $participante_id)->delete();
    }

    /**
     * Inscribe múltiples participantes a un curso
     */
    public function inscribirParticipantes($curso_id, $participantes_ids)
    {
        if (empty($participantes_ids)) {
            return true;
        }

        $data = [];
        foreach ($participantes_ids as $participante_id) {
            // Verificar que no esté ya inscrito
            if (!$this->isParticipanteInscrito($curso_id, $participante_id)) {
                $data[] = [
                    'curso_id' => $curso_id,
                    'participante_id' => $participante_id
                ];
            }
        }

        if (empty($data)) {
            return true;
        }

        return $this->insertBatch($data);
    }

    /**
     * Obtiene estadísticas de inscripciones
     */
    public function getEstadisticasInscripciones()
    {
        $db = \Config\Database::connect();

        return [
            'total_inscripciones' => $this->countAllResults(),
            'cursos_con_participantes' => $db->table($this->table)
                ->select('curso_id')
                ->distinct()
                ->countAllResults(),
            'participantes_activos' => $db->table($this->table)
                ->select('participante_id')
                ->distinct()
                ->countAllResults()
        ];
    }
}