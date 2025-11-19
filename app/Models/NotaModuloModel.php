<?php

namespace App\Models;

use CodeIgniter\Model;

class NotaModuloModel extends Model
{
    protected $table            = 'notas_modulo';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'modulo_id', 
        'participante_id', 
        'nota', 
        'observaciones', 
        'fecha_registro',
        'registrado_por'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'modulo_id'       => 'required|integer|is_not_unique[modulos.id]',
        'participante_id' => 'required|integer|is_not_unique[users.id]',
        'nota'            => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[20]',
    ];

    protected $validationMessages = [
        'modulo_id' => [
            'required'      => 'El ID del módulo es obligatorio.',
            'integer'       => 'El ID del módulo debe ser un número entero.',
            'is_not_unique' => 'El módulo especificado no existe.'
        ],
        'participante_id' => [
            'required'      => 'El ID del participante es obligatorio.',
            'integer'       => 'El ID del participante debe ser un número entero.',
            'is_not_unique' => 'El participante especificado no existe.'
        ],
        'nota' => [
            'decimal'                  => 'La nota debe ser un número decimal.',
            'greater_than_equal_to'    => 'La nota debe ser mayor o igual a 0.',
            'less_than_equal_to'       => 'La nota debe ser menor o igual a 20.'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Obtiene las notas de un participante en todos los módulos de un curso
     */
    public function getNotasParticipanteByCurso($participante_id, $curso_id)
    {
        return $this->select('notas_modulo.*, modulos.nombre as modulo_nombre, modulos.orden')
                    ->join('modulos', 'modulos.id = notas_modulo.modulo_id')
                    ->where('notas_modulo.participante_id', $participante_id)
                    ->where('modulos.curso_id', $curso_id)
                    ->orderBy('modulos.orden', 'ASC')
                    ->findAll();
    }

    /**
     * Obtiene las notas de un participante en un módulo específico
     */
    public function getNotaParticipanteByModulo($participante_id, $modulo_id)
    {
        return $this->where('participante_id', $participante_id)
                    ->where('modulo_id', $modulo_id)
                    ->first();
    }

    /**
     * Obtiene todas las notas de un módulo con información de participantes
     */
    public function getNotasByModulo($modulo_id)
    {
        return $this->select('notas_modulo.*, users.nombres, users.apellidos, users.dni, users.correo')
                    ->join('users', 'users.id = notas_modulo.participante_id')
                    ->where('notas_modulo.modulo_id', $modulo_id)
                    ->orderBy('users.apellidos', 'ASC')
                    ->orderBy('users.nombres', 'ASC')
                    ->findAll();
    }

    /**
     * Registra o actualiza la nota de un participante en un módulo
     */
    public function registrarNota($modulo_id, $participante_id, $nota, $observaciones = null, $docente_id = null)
    {
        $existente = $this->where('modulo_id', $modulo_id)
                          ->where('participante_id', $participante_id)
                          ->first();

        $data = [
            'modulo_id'       => $modulo_id,
            'participante_id' => $participante_id,
            'nota'            => $nota,
            'observaciones'   => $observaciones,
            'fecha_registro'  => date('Y-m-d H:i:s'),
            'registrado_por'  => $docente_id
        ];

        if ($existente) {
            return $this->update($existente['id'], $data);
        } else {
            return $this->insert($data);
        }
    }

    /**
     * Calcula el promedio de notas de un participante en un curso
     */
    public function getPromedioParticipanteByCurso($participante_id, $curso_id)
    {
        $result = $this->select('AVG(notas_modulo.nota) as promedio')
                       ->join('modulos', 'modulos.id = notas_modulo.modulo_id')
                       ->where('notas_modulo.participante_id', $participante_id)
                       ->where('modulos.curso_id', $curso_id)
                       ->where('notas_modulo.nota IS NOT NULL')
                       ->first();

        return $result ? round($result['promedio'], 2) : null;
    }

    /**
     * Obtiene el reporte completo de notas de un curso
     */
    public function getReporteNotasByCurso($curso_id)
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('curso_participante cp');
        $builder->select('
            u.id as participante_id,
            u.nombres,
            u.apellidos,
            u.dni,
            m.id as modulo_id,
            m.nombre as modulo_nombre,
            m.orden,
            nm.nota,
            nm.observaciones,
            nm.fecha_registro
        ');
        $builder->join('users u', 'u.id = cp.participante_id');
        $builder->join('modulos m', 'm.curso_id = cp.curso_id');
        $builder->join('notas_modulo nm', 'nm.modulo_id = m.id AND nm.participante_id = u.id', 'left');
        $builder->where('cp.curso_id', $curso_id);
        $builder->orderBy('u.apellidos', 'ASC');
        $builder->orderBy('u.nombres', 'ASC');
        $builder->orderBy('m.orden', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Obtiene estadísticas de notas de un módulo
     */
    public function getEstadisticasModulo($modulo_id)
    {
        $db = \Config\Database::connect();
        
        $stats = $this->select('
            COUNT(*) as total_notas,
            AVG(nota) as promedio,
            MIN(nota) as nota_minima,
            MAX(nota) as nota_maxima,
            SUM(CASE WHEN nota >= 11 THEN 1 ELSE 0 END) as aprobados,
            SUM(CASE WHEN nota < 11 THEN 1 ELSE 0 END) as desaprobados
        ')
        ->where('modulo_id', $modulo_id)
        ->where('nota IS NOT NULL')
        ->first();

        if ($stats) {
            $stats['promedio'] = $stats['promedio'] ? round($stats['promedio'], 2) : null;
            $stats['nota_minima'] = $stats['nota_minima'] ? round($stats['nota_minima'], 2) : null;
            $stats['nota_maxima'] = $stats['nota_maxima'] ? round($stats['nota_maxima'], 2) : null;
        }

        return $stats;
    }

    /**
     * Verifica si un participante tiene todas las notas de un curso
     */
    public function tieneNotasCompletas($participante_id, $curso_id)
    {
        $db = \Config\Database::connect();
        
        $totalModulos = $db->table('modulos')
                           ->where('curso_id', $curso_id)
                           ->countAllResults();
        
        $notasRegistradas = $this->join('modulos', 'modulos.id = notas_modulo.modulo_id')
                                 ->where('notas_modulo.participante_id', $participante_id)
                                 ->where('modulos.curso_id', $curso_id)
                                 ->where('notas_modulo.nota IS NOT NULL')
                                 ->countAllResults();
        
        return $totalModulos > 0 && $totalModulos === $notasRegistradas;
    }

    /**
     * Elimina todas las notas de un módulo
     */
    public function eliminarNotasByModulo($modulo_id)
    {
        return $this->where('modulo_id', $modulo_id)->delete();
    }

    /**
     * Elimina todas las notas de un participante
     */
    public function eliminarNotasByParticipante($participante_id)
    {
        return $this->where('participante_id', $participante_id)->delete();
    }
}