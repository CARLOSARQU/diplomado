<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [ 'type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true ],
            'nombres' => [ 'type'=>'VARCHAR','constraint'=>100 ],
            'apellidos' => [ 'type'=>'VARCHAR','constraint'=>100 ],
            'correo' => [ 'type'=>'VARCHAR','constraint'=>150 ],
            'dni' => [ 'type'=>'VARCHAR','constraint'=>20 ],
            'escuela_profesional' => [ 'type'=>'VARCHAR','constraint'=>150, 'null'=>true ],
            'password_hash' => [ 'type'=>'VARCHAR','constraint'=>255 ],
            'rol_id' => [ 'type'=>'INT','constraint'=>11,'unsigned'=>true ],
            'estado' => [ 'type'=>'TINYINT','constraint'=>1, 'default'=>1 ],
            'created_at' => [ 'type'=>'DATETIME', 'null'=>true ],
            'updated_at' => [ 'type'=>'DATETIME', 'null'=>true ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('correo');
        $this->forge->addUniqueKey('dni');
        $this->forge->addForeignKey('rol_id', 'roles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}