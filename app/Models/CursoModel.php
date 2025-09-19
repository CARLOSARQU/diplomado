<?php

namespace App\Models;
use CodeIgniter\Model;

class CursoModel extends Model
{
    protected $table      = 'cursos';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['nombre','descripcion','fecha_inicio','fecha_fin','estado','created_by','created_at','updated_at'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Devuelve un curso con módulos y (opcionalmente) sesiones
     */
    
}
