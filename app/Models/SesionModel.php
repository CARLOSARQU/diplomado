<?php

namespace App\Models;

use CodeIgniter\Model;

class SesionModel extends Model
{
    protected $table      = 'sesiones';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'modulo_id','titulo','descripcion','fecha','hora_inicio','hora_fin','asistencia_habilitada','created_at','updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

}
