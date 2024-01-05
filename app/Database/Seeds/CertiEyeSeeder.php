<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CertiEyeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => '',
            ],
        ];

        $this->db->table('certieye')->insertBatch($data);
    }
}
