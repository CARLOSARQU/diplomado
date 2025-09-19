<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CursoSeeder extends Seeder
{
    public function run()
    {
        $cursos = [
            ['nombre' => 'Curso de PHP', 'descripcion' => 'Programación en PHP con CodeIgniter 4'],
            ['nombre' => 'Curso de MySQL', 'descripcion' => 'Bases de datos relacionales y consultas avanzadas'],
        ];

        $table = $this->db->table('cursos');
        foreach ($cursos as $curso) {
            $exists = $this->db->table('cursos')->where('nombre', $curso['nombre'])->countAllResults(false);
            if ($exists == 0) {
                $table->insert($curso);
            }
        }
    }
}
