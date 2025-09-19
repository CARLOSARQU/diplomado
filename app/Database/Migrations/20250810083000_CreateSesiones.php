<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSesiones extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [ 'type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true ],
            'modulo_id' => [ 'type'=>'INT','constraint'=>11,'unsigned'=>true ],
            'titulo' => [ 'type'=>'VARCHAR','constraint'=>150 ],
            'descripcion' => [ 'type'=>'TEXT', 'null'=>true ],
            'fecha' => [ 'type'=>'DATE', 'null'=>true ],
            'hora_inicio' => [ 'type'=>'TIME', 'null'=>true ],
            'hora_fin' => [ 'type'=>'TIME', 'null'=>true ],
            'asistencia_habilitada' => [ 'type'=>'TINYINT', 'constraint'=>1, 'default'=>0 ],
            'created_at' => [ 'type'=>'DATETIME', 'null'=>true ],
            'updated_at' => [ 'type'=>'DATETIME', 'null'=>true ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('modulo_id', 'modulos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sesiones');
    }

    public function down()
    {
        $this->forge->dropTable('sesiones');
    }
}