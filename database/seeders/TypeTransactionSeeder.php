<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('type_transactions')->insert([
            [
                'title' => 'Achat de forfait',
                'code' => 'package',
                'status' => true
            ],
            [
                'title' => 'Retrait de revenus',
                'code' => 'withdraw',
                'status' => true
            ]
        ]);
    }
}
