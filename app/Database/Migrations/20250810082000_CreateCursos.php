<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCursos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [ 'type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true ],
            'nombre' => [ 'type'=>'VARCHAR','constraint'=>150 ],
            'descripcion' => [ 'type'=>'TEXT', 'null'=>true ],
            'fecha_inicio' => [ 'type'=>'DATE', 'null'=>true ],
            'fecha_fin' => [ 'type'=>'DATE', 'null'=>true ],
            'estado' => [ 'type'=>'TINYINT', 'constraint'=>1, 'default'=>1 ],
            'created_by' => [ 'type'=>'INT','constraint'=>11,'unsigned'=>true,'null'=>true ],
            'created_at' => [ 'type'=>'DATETIME', 'null'=>true ],
            'updated_at' => [ 'type'=>'DATETIME', 'null'=>true ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('cursos');
    }

    public function down()
    {
        $this->forge->dropTable('cursos');
    }
}