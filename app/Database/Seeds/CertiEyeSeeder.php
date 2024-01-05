<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class CertiEyeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => Uuid::uuid4(),
                'pecahan' => '0,5',
                'jual' => '611500',
                'buyback' => '513000',
            ],
            [
                'id' => Uuid::uuid4(),
                'pecahan' => '1',
                'jual' => 1123000,
                'buyback' => 1026000,
            ],
            [
                'id' => Uuid::uuid4(),
                'pecahan' => '2',
                'jual' => 2186000,
                'buyback' => 2052000,
            ],
            [
                'id' => Uuid::uuid4(),
                'pecahan' => '3',
                'jual' => 3254000,
                'buyback' => 3078000,
            ],
            [
                'id' => Uuid::uuid4(),
                'pecahan' => '5',
                'jual' => 5390000,
                'buyback' => 5130000,
            ],
            [
                'id' => Uuid::uuid4(),
                'pecahan' => '10',
                'jual' => 10725000,
                'buyback' => 10260000,
            ],
        ];

        $this->db->table('certieye')->insertBatch($data);
    }
}
