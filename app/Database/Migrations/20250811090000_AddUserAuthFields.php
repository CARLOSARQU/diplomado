<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserAuthFields extends Migration
{
    public function up()
    {
        $fields = [
            'last_login_at' => ['type' => 'DATETIME', 'null' => true],
            'reset_token' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'reset_expires_at' => ['type' => 'DATETIME', 'null' => true],
        ];

        // Si ya existen created_at/updated_at puedes omitirlos; la migration es segura si no hay duplicados.
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['last_login_at', 'reset_token', 'reset_expires_at']);
    }
}
