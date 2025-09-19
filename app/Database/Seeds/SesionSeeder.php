<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SesionSeeder extends Seeder
{
    public function run()
    {
        $m1 = $this->db->table('modulos')->where('nombre', 'Introducción a PHP')->get()->getRow();
        $m2 = $this->db->table('modulos')->where('nombre', 'POO en PHP')->get()->getRow();
        $m3 = $this->db->table('modulos')->where('nombre', 'Consultas Básicas')->get()->getRow();
        $m4 = $this->db->table('modulos')->where('nombre', 'Consultas Avanzadas')->get()->getRow();

        $sesiones = [];
        if ($m1) {
            $sesiones[] = ['modulo_id' => $m1->id, 'titulo' => 'Instalación y configuración de PHP', 'descripcion' => null, 'fecha' => '2025-08-12', 'hora_inicio' => '10:00:00', 'hora_fin' => '11:30:00', 'asistencia_habilitada' => 1];
            $sesiones[] = ['modulo_id' => $m1->id, 'titulo' => 'Sintaxis básica', 'descripcion' => null, 'fecha' => '2025-08-14', 'hora_inicio' => '10:00:00', 'hora_fin' => '11:30:00', 'asistencia_habilitada' => 1];
        }
        if ($m3) {
            $sesiones[] = ['modulo_id' => $m3->id, 'titulo' => 'SELECT y WHERE', 'descripcion' => null, 'fecha' => '2025-08-13', 'hora_inicio' => '15:00:00', 'hora_fin' => '16:30:00', 'asistencia_habilitada' => 1];
        }
        if ($m4) {
            $sesiones[] = ['modulo_id' => $m4->id, 'titulo' => 'JOIN y subconsultas', 'descripcion' => null, 'fecha' => '2025-08-15', 'hora_inicio' => '15:00:00', 'hora_fin' => '16:30:00', 'asistencia_habilitada' => 1];
        }

        $table = $this->db->table('sesiones');
        foreach ($sesiones as $s) {
            $exists = $this->db->table('sesiones')->where('modulo_id', $s['modulo_id'])->where('titulo', $s['titulo'])->countAllResults(false);
            if ($exists == 0) {
                $table->insert($s);
            }
        }
    }
}
