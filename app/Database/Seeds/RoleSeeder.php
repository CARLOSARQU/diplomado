<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['nombre' => 'superadmin', 'descripcion' => 'Super administrador del sistema'],
            ['nombre' => 'admin', 'descripcion' => 'Administrador (crea cursos/modulos/sesiones)'],
            ['nombre' => 'usuario', 'descripcion' => 'Usuario/estudiante'],
        ];

        $table = $this->db->table('roles');
        foreach ($roles as $rol) {
            $exists = $this->db->table('roles')->where('nombre', $rol['nombre'])->countAllResults(false);
            if ($exists == 0) {
                $table->insert($rol);
            }
        }
    }
}
