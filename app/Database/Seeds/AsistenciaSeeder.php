<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AsistenciaSeeder extends Seeder
{
    public function run()
    {
        $user = $this->db->table('users')->where('correo', 'luis@demo.com')->get()->getRow();
        $ses1 = $this->db->table('sesiones')->where('titulo', 'Instalación y configuración de PHP')->get()->getRow();
        $ses2 = $this->db->table('sesiones')->where('titulo', 'SELECT y WHERE')->get()->getRow();

        $table = $this->db->table('asistencias');
        $now = date('Y-m-d H:i:s');

        if ($user && $ses1) {
            $exists = $this->db->table('asistencias')
                ->where('user_id', $user->id)
                ->where('sesion_id', $ses1->id)
                ->countAllResults(false);

            if ($exists == 0) {
                $table->insert([
                    'user_id' => $user->id,
                    'sesion_id' => $ses1->id,
                    'fecha_asistencia' => '2025-08-12 10:05:00',
                    'estado' => 'presente',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        if ($user && $ses2) {
            $exists = $this->db->table('asistencias')
                ->where('user_id', $user->id)
                ->where('sesion_id', $ses2->id)
                ->countAllResults(false);

            if ($exists == 0) {
                $table->insert([
                    'user_id' => $user->id,
                    'sesion_id' => $ses2->id,
                    'fecha_asistencia' => '2025-08-13 15:02:00',
                    'estado' => 'tarde',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
