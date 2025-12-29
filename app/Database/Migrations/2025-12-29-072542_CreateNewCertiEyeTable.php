<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNewCertiEyeTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'pecahan' => [
                'type' => 'INT',
            ],
            'jual' => [
                'type' => 'INT',
            ],
            'buyback' => [
                'type' => 'INT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('new-certieye');
    }

    public function down()
    {
        $this->forge->dropTable('new-certieye');
    }
}
