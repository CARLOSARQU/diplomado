<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermisoSeeder extends Seeder
{
    public function run()
    {
        $permisos = [
            ['nombre' => 'crear_curso', 'descripcion' => 'Permite crear cursos'],
            ['nombre' => 'editar_curso', 'descripcion' => 'Permite editar cursos'],
            ['nombre' => 'eliminar_curso', 'descripcion' => 'Permite eliminar cursos'],
            ['nombre' => 'gestionar_asistencias', 'descripcion' => 'Permite gestionar asistencias'],
        ];

        $table = $this->db->table('permisos');
        foreach ($permisos as $perm) {
            $exists = $this->db->table('permisos')->where('nombre', $perm['nombre'])->countAllResults(false);
            if ($exists == 0) {
                $table->insert($perm);
            }
        }
    }
}
