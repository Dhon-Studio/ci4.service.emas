<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePrices extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'group' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'owner' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'size' => [
                'type' => 'INT',
            ],
            'year' => [
                'type' => 'INT',
            ],
            'redmark' => [
                'type' => 'INT',
                'constraint' => 1,
            ],
            'price' => [
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
        $this->forge->createTable('prices');
    }

    public function down()
    {
        $this->forge->dropTable('prices');
    }
}
