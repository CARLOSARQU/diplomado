<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('App\\Database\\Seeds\\RoleSeeder');
        $this->call('App\\Database\\Seeds\\PermisoSeeder');
        $this->call('App\\Database\\Seeds\\UserSeeder');
        $this->call('App\\Database\\Seeds\\RolPermisoSeeder');
        $this->call('App\\Database\\Seeds\\CursoSeeder');
        $this->call('App\\Database\\Seeds\\ModuloSeeder');
        $this->call('App\\Database\\Seeds\\SesionSeeder');
        $this->call('App\\Database\\Seeds\\AsistenciaSeeder');
    }
}
