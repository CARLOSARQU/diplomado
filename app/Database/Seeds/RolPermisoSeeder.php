<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolPermisoSeeder extends Seeder
{
    public function run()
    {
        $roles = $this->db->table('roles')->get()->getResult();
        $permisos = $this->db->table('permisos')->get()->getResult();

        if (empty($roles) || empty($permisos)) {
            return;
        }

        // Asignación simple: superadmin -> todos; admin -> la mayoría; usuario -> ninguno
        $permIds = array_map(fn($p) => $p->id, $permisos);

        $table = $this->db->table('rol_permisos');
        foreach ($roles as $role) {
            if ($role->nombre === 'superadmin') {
                foreach ($permIds as $pid) {
                    $exists = $this->db->table('rol_permisos')->where('rol_id', $role->id)->where('permiso_id', $pid)->countAllResults(false);
                    if ($exists == 0) {
                        $table->insert(['rol_id' => $role->id, 'permiso_id' => $pid]);
                    }
                }
            } elseif ($role->nombre === 'admin') {
                // dar todos excepto permisos de administración crítica (ejemplo: ninguno extra aquí)
                foreach ($permIds as $pid) {
                    $exists = $this->db->table('rol_permisos')->where('rol_id', $role->id)->where('permiso_id', $pid)->countAllResults(false);
                    if ($exists == 0) {
                        $table->insert(['rol_id' => $role->id, 'permiso_id' => $pid]);
                    }
                }
            }
            // usuarios normales no reciben permisos por defecto aquí
        }
    }
}
