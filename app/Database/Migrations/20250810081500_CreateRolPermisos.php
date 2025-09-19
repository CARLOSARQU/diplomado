<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRolPermisos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'rol_id' => [ 'type'=>'INT','constraint'=>11,'unsigned'=>true ],
            'permiso_id' => [ 'type'=>'INT','constraint'=>11,'unsigned'=>true ],
            'created_at' => [ 'type'=>'DATETIME', 'null'=>true ],
        ]);

        // Composite primary key (rol_id + permiso_id)
        $this->forge->addKey(['rol_id','permiso_id'], true);
        $this->forge->addForeignKey('rol_id', 'roles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('permiso_id', 'permisos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rol_permisos');
    }

    public function down()
    {
        $this->forge->dropTable('rol_permisos');
    }
}