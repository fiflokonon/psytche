<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'last_name' => '',
                'first_name' => '',
                'call_id' => 'GB529',
                'sex' => 'M',
                'country' => 'Bénin',
                'voice_hidden' => false,
                'balance' => 0,
                'status' => true,
                'last_connexion' => now()
            ]
        ]);
    }
}
