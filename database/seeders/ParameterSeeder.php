<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParameterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('parameters')->insert([
            [
                'code' => 'benefit',
                'title' => 'Pourcentage pour 60s',
                'value' => 5,
                'status' => true
            ],
            [
                'code' => 'minute_price',
                'title' => 'Prix de la minute',
                'value' => 100,
                'status' => true
            ]
        ]);
    }
}
