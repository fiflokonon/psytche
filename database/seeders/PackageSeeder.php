<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('packages')->insert([
            [
                'title' => 'Welcome Pack',
                'code' => 'welcome',
                'price' => 0,
                'validity' => 30,
                'type_package_id' => 1,
                'duration' => '00:10:00',
                'sex' => false,
                'voice_hidden' => true,
                'language' => false,
                'status' => false,
            ],
            [
                'title' => 'Welcome Hi',
                'code' => 'welcome_hi',
                'price' => 100,
                'validity' => 30,
                'type_package_id' => 1,
                'duration' => '00:10:00',
                'sex' => false,
                'voice_hidden' => true,
                'language' => false,
                'status' => true,
            ]
        ]);
    }
}
