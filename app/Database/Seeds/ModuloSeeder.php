<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ModuloSeeder extends Seeder
{
    public function run()
    {
        $cursoPhp = $this->db->table('cursos')->where('nombre', 'Curso de PHP')->get()->getRow();
        $cursoMysql = $this->db->table('cursos')->where('nombre', 'Curso de MySQL')->get()->getRow();

        $modulos = [];
        if ($cursoPhp) {
            $modulos[] = ['curso_id' => $cursoPhp->id, 'nombre' => 'Introducción a PHP', 'orden' => 1];
            $modulos[] = ['curso_id' => $cursoPhp->id, 'nombre' => 'POO en PHP', 'orden' => 2];
        }
        if ($cursoMysql) {
            $modulos[] = ['curso_id' => $cursoMysql->id, 'nombre' => 'Consultas Básicas', 'orden' => 1];
            $modulos[] = ['curso_id' => $cursoMysql->id, 'nombre' => 'Consultas Avanzadas', 'orden' => 2];
        }

        $table = $this->db->table('modulos');
        foreach ($modulos as $m) {
            $exists = $this->db->table('modulos')->where('curso_id', $m['curso_id'])->where('nombre', $m['nombre'])->countAllResults(false);
            if ($exists == 0) {
                $table->insert($m);
            }
        }
    }
}
