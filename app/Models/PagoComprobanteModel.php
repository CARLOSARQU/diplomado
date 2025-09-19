<?php

namespace App\Models;

use CodeIgniter\Model;

class PagoComprobanteModel extends Model
{
    protected $table            = 'pagos_comprobantes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'participante_id', 'modulo_id', 'monto', 'identificador_pago', 
        'metodo_pago', 'archivo_comprobante', 'fecha_pago', 'observaciones',
        'estado', 'observaciones_admin', 'revisado_por', 'fecha_revision'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'participante_id' => 'required|integer|is_not_unique[users.id]',
        'modulo_id' => 'required|integer|is_not_unique[modulos.id]',
        'monto' => 'required|decimal|greater_than[0]',
        //'identificador_pago' => 'required|min_length[5]|max_length[10]|alpha_numeric',
        //'metodo_pago' => 'required|in_list[banco_nacion,pagalo_pe,caja]',
        'archivo_comprobante' => 'required',
        'fecha_pago' => 'required|valid_date'
    ];

    protected $validationMessages = [
        'participante_id' => [
            'required' => 'El participante es obligatorio.',
            'is_not_unique' => 'El participante no existe.'
        ],
        'modulo_id' => [
            'required' => 'El módulo es obligatorio.',
            'is_not_unique' => 'El módulo no existe.'
        ],
        'monto' => [
            'required' => 'El monto es obligatorio.',
            'greater_than' => 'El monto debe ser mayor a 0.'
        ],
        'identificador_pago' => [
            'required' => 'El identificador de pago es obligatorio.',
            'min_length' => 'El identificador debe tener al menos 5 caracteres.',
            'max_length' => 'El identificador no puede tener más de 10 caracteres.',
            'alpha_numeric' => 'El identificador solo puede contener letras y números.'
        ]
    ];

    /**
     * Obtiene pagos de un participante con información de módulos y cursos
     */
    public function getPagosByParticipante($participante_id)
    {
        return $this->select('
                pagos_comprobantes.*, 
                modulos.nombre as modulo_nombre,
                cursos.nombre as curso_nombre,
                users.nombres as revisor_nombres,
                users.apellidos as revisor_apellidos
            ')
            ->join('modulos', 'modulos.id = pagos_comprobantes.modulo_id')
            ->join('cursos', 'cursos.id = modulos.curso_id')
            ->join('users', 'users.id = pagos_comprobantes.revisado_por', 'LEFT')
            ->where('pagos_comprobantes.participante_id', $participante_id)
            ->orderBy('pagos_comprobantes.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Obtiene módulos sin pago de un participante
     */
    public function getModulosSinPago($participante_id)
    {
        $db = \Config\Database::connect();
        
        return $db->query("
            SELECT DISTINCT m.id, m.nombre as modulo_nombre, c.nombre as curso_nombre, c.id as curso_id
            FROM modulos m
            INNER JOIN cursos c ON c.id = m.curso_id
            INNER JOIN curso_participante cp ON cp.curso_id = c.id
            LEFT JOIN pagos_comprobantes pc ON pc.modulo_id = m.id AND pc.participante_id = ?
            WHERE cp.participante_id = ? 
            AND c.estado = 1
            AND pc.id IS NULL
            ORDER BY c.nombre, m.orden
        ", [$participante_id, $participante_id])->getResultArray();
    }

    /**
     * Verifica si ya existe un comprobante para el participante y módulo
     */
    public function yaExistePago($participante_id, $modulo_id)
    {
        return $this->where('participante_id', $participante_id)
                   ->where('modulo_id', $modulo_id)
                   ->countAllResults() > 0;
    }

    /**
     * Verifica si el identificador de pago ya existe
     */
    public function identificadorExiste($identificador)
    {
        return $this->where('identificador_pago', $identificador)
                   ->countAllResults() > 0;
    }

    /**
     * Obtiene pagos pendientes para administrador
     */
    public function getPagosPendientes()
    {
        return $this->select('
                pagos_comprobantes.*, 
                modulos.nombre as modulo_nombre,
                cursos.nombre as curso_nombre,
                users.nombres as participante_nombres,
                users.apellidos as participante_apellidos,
                users.dni as participante_dni
            ')
            ->join('modulos', 'modulos.id = pagos_comprobantes.modulo_id')
            ->join('cursos', 'cursos.id = modulos.curso_id')
            ->join('users', 'users.id = pagos_comprobantes.participante_id')
            ->where('pagos_comprobantes.estado', 'en_revision')
            ->orderBy('pagos_comprobantes.created_at', 'ASC')
            ->findAll();
    }

    /**
     * Obtiene todos los pagos con filtros para administrador
     */
    public function getPagosAdmin($filtros = [])
    {
        $query = $this->select('
                pagos_comprobantes.*, 
                modulos.nombre as modulo_nombre,
                cursos.nombre as curso_nombre,
                users.nombres as participante_nombres,
                users.apellidos as participante_apellidos,
                users.dni as participante_dni
            ')
            ->join('modulos', 'modulos.id = pagos_comprobantes.modulo_id')
            ->join('cursos', 'cursos.id = modulos.curso_id')
            ->join('users', 'users.id = pagos_comprobantes.participante_id');

        if (!empty($filtros['estado'])) {
            $query->where('pagos_comprobantes.estado', $filtros['estado']);
        }
        if (!empty($filtros['curso_id'])) {
            $query->where('cursos.id', $filtros['curso_id']);
        }
        if (!empty($filtros['metodo_pago'])) {
            $query->where('pagos_comprobantes.metodo_pago', $filtros['metodo_pago']);
        }

        return $query->orderBy('pagos_comprobantes.created_at', 'DESC')->findAll();
    }

    /**
     * Aprueba un pago
     */
    public function aprobarPago($pago_id, $admin_id, $observaciones = null)
    {
        return $this->update($pago_id, [
            'estado' => 'aprobado',
            'revisado_por' => $admin_id,
            'fecha_revision' => date('Y-m-d H:i:s'),
            'observaciones_admin' => $observaciones
        ]);
    }

    /**
     * Rechaza un pago
     */
    public function rechazarPago($pago_id, $admin_id, $observaciones)
    {
        return $this->update($pago_id, [
            'estado' => 'rechazado',
            'revisado_por' => $admin_id,
            'fecha_revision' => date('Y-m-d H:i:s'),
            'observaciones_admin' => $observaciones
        ]);
    }

    public function editarDatosPago($pago_id, $datos)
    {
        // Validar que el identificador no exista para otro pago
        if (isset($datos['identificador_pago'])) {
            if ($this->identificadorExiste($datos['identificador_pago'], $pago_id)) {
                return false; // El identificador ya existe
            }
        }

        return $this->update($pago_id, $datos);
    }

    /**
     * Obtiene los métodos de pago disponibles
     */
    public function getMetodosPago()
    {
        return [
            'banco_nacion' => 'Banco de la Nación',
            'pagalo_pe' => 'Págalo.pe',
            'caja' => 'Caja'
        ];
    }

        /**
     * Ingresos totales (sum) por período (solo pagos aprobados)
     * $fechaInicio, $fechaFin: 'YYYY-MM-DD' (opcionales)
     */
    public function ingresosPorPeriodo($fechaInicio = null, $fechaFin = null)
    {
        $qb = $this->select('SUM(monto) as total_ingresos')
                   ->where('estado', 'aprobado');

        if ($fechaInicio) {
            $qb->where('DATE(fecha_revision) >=', $fechaInicio);
        }
        if ($fechaFin) {
            $qb->where('DATE(fecha_revision) <=', $fechaFin);
        }

        return $qb->first()['total_ingresos'] ?? 0;
    }

    /**
     * Conteo de estados por curso y módulo
     */
    public function estadoPorCursoModulo()
    {
        return $this->select('
                cursos.nombre AS curso,
                modulos.nombre AS modulo,
                pagos_comprobantes.estado AS estado,
                COUNT(pagos_comprobantes.id) AS total
            ')
            ->join('modulos', 'modulos.id = pagos_comprobantes.modulo_id')
            ->join('cursos', 'cursos.id = modulos.curso_id')
            ->groupBy('cursos.id, modulos.id, pagos_comprobantes.estado')
            ->orderBy('cursos.nombre, modulos.orden')
            ->findAll();
    }

    /**
     * Participantes con pagos pendientes/en revisión
     */
    public function participantesConPendientes()
    {
        return $this->select('users.id, users.nombres, users.apellidos, users.dni, COUNT(pagos_comprobantes.id) as pendientes')
            ->join('users', 'users.id = pagos_comprobantes.participante_id')
            ->whereIn('pagos_comprobantes.estado', ['pendiente', 'en_revision'])
            ->groupBy('users.id')
            ->orderBy('pendientes', 'DESC')
            ->findAll();
    }

    public function getPagoDetalle($id)
    {
        return $this->select('
                pagos_comprobantes.*,
                modulos.nombre AS modulo_nombre,
                cursos.nombre AS curso_nombre,
                users.nombres AS participante_nombres,
                users.apellidos AS participante_apellidos,
                users.dni AS participante_dni,
                revisores.nombres AS revisor_nombres,
                revisores.apellidos AS revisor_apellidos
            ')
            ->join('modulos', 'modulos.id = pagos_comprobantes.modulo_id')
            ->join('cursos', 'cursos.id = modulos.curso_id')
            ->join('users', 'users.id = pagos_comprobantes.participante_id')
            ->join('users AS revisores', 'revisores.id = pagos_comprobantes.revisado_por', 'LEFT')
            ->where('pagos_comprobantes.id', $id)
            ->first();
    }
}