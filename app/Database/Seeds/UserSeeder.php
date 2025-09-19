<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // obtener roles (si existen)
        $roles = $this->db->table('roles')->get()->getResultArray();
        $roleIndex = [];
        foreach ($roles as $r) {
            $roleIndex[$r['nombre']] = $r['id'];
        }

        $users = [
            [
                'nombres' => 'Carlos',
                'apellidos' => 'Pérez',
                'correo' => 'carlos@demo.com',
                'dni' => '12345678',
                'escuela_profesional' => 'Ingeniería de Sistemas',
                'password_hash' => password_hash('123456', PASSWORD_DEFAULT),
                'rol_id' => $roleIndex['superadmin'] ?? 1,
                'estado' => 1,
            ],
            [
                'nombres' => 'Ana',
                'apellidos' => 'Torres',
                'correo' => 'ana@demo.com',
                'dni' => '87654321',
                'escuela_profesional' => 'Ingeniería Industrial',
                'password_hash' => password_hash('123456', PASSWORD_DEFAULT),
                'rol_id' => $roleIndex['admin'] ?? 2,
                'estado' => 1,
            ],
            [
                'nombres' => 'Luis',
                'apellidos' => 'Ramírez',
                'correo' => 'luis@demo.com',
                'dni' => '56781234',
                'escuela_profesional' => 'Administración',
                'password_hash' => password_hash('123456', PASSWORD_DEFAULT),
                'rol_id' => $roleIndex['usuario'] ?? 3,
                'estado' => 1,
            ],
        ];

        $table = $this->db->table('users');
        foreach ($users as $user) {
            $exists = $this->db->table('users')->where('correo', $user['correo'])->orWhere('dni', $user['dni'])->countAllResults(false);
            if ($exists == 0) {
                $table->insert($user);
            }
        }
    }
}
