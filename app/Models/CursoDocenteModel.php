<?php

namespace App\Models;

use CodeIgniter\Model;

class CursoDocenteModel extends Model
{
    protected $table            = 'curso_docente';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['curso_id', 'docente_id'];

    // No usamos timestamps para esta tabla pivote
    protected $useTimestamps = false;
}