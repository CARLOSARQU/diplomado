<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAsistencias extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'sesion_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'fecha_asistencia' => ['type' => 'DATETIME', 'null' => false],
            'estado'      => ['type' => 'ENUM("presente","ausente","tarde")', 'default' => 'presente'],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('sesion_id', 'sesiones', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('asistencias');
    }

    public function down()
    {
        $this->forge->dropTable('asistencias');
    }
}
