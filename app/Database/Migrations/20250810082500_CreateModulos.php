<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateModulos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [ 'type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true ],
            'curso_id' => [ 'type'=>'INT','constraint'=>11,'unsigned'=>true ],
            'nombre' => [ 'type'=>'VARCHAR','constraint'=>150 ],
            'descripcion' => [ 'type'=>'TEXT', 'null'=>true ],
            'orden' => [ 'type'=>'INT', 'constraint'=>11, 'default'=>0 ],
            'created_at' => [ 'type'=>'DATETIME', 'null'=>true ],
            'updated_at' => [ 'type'=>'DATETIME', 'null'=>true ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('curso_id', 'cursos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('modulos');
    }

    public function down()
    {
        $this->forge->dropTable('modulos');
    }
}
