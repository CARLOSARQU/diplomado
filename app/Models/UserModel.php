<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
            'nombres', 'apellidos', 'correo', 'dni', 
            'escuela_profesional', 'password_hash', 
            'rol_id', 'estado', 'last_login_at', 
            'reset_token', 'reset_expires_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getUsersWithRoles()
    {
        return $this->select('users.*, roles.nombre as rol_nombre')
                    ->join('roles', 'roles.id = users.rol_id')
                    ->findAll();
    }
}
