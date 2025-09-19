<?php

namespace App\Models;

use CodeIgniter\Model;

class ModuloModel extends Model
{
    protected $table      = 'modulos';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['curso_id','nombre','descripcion','orden','created_at','updated_at'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

}
