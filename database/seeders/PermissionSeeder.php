<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions')->insert([
            [
                'title' => 'Voir son forfait actif',
                'code' => 'get-active-package',
                'status' => true
            ],
            [
                'title' => 'Voir ses informations',
                'code' => 'get-user-infos',
                'status' => true
            ],
            [
                'title' => "Récupérer un id aléatoire",
                'code' => 'get-user-call-id',
                'status' => true
            ],
            [
                'title' => "Faire un appel",
                'code' => 'make-call',
                'status' => true
            ],
            [
                'title' => "Voir son historique d'appel",
                'code' => 'get-call-history',
                'status' => true
            ],
            [
                'title' => "Voir l'historique des forfaits",
                'code' => 'get-package-history',
                'status' => true
            ],
            [
                'title' => "Voir la liste des forfaits disponibles",
                'code' => 'get-packages',
                'status' => true
            ],
            [
                'title' => "Acheter du forfait",
                'code' => 'init-package',
                'status' => true
            ],
            [
                'title' => "Voir l'historique de ses transactions",
                'code' => 'get-transaction-history',
                'status' => true
            ],
            [
                'title' => "Gagner de l'argent",
                'code' => 'make-money',
                'status' => true
            ],
            [
                'title' => "Accéder au dashboard",
                'code' => 'get-dashboard',
                'status' => true
            ],
        ]);
    }
}
