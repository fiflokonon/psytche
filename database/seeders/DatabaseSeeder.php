<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(TypePackageSeeder::class);
        $this->call(PackageSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(TypeTransactionSeeder::class);
        $this->call(ParameterSeeder::class);
    }
}
