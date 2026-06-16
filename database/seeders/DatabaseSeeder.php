<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Driver;
use App\Models\Package;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Administrador inicial com senha provisória e troca obrigatória no 1º acesso (RF06).
        User::firstOrCreate(
            ['email' => 'admin@coinpel.local'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'must_change_password' => true,
            ],
        );

        $vehicles = Vehicle::factory(5)->create();
        $drivers = Driver::factory(5)->create();

        // Reaproveita os veículos/motoristas já criados em vez de gerar novos.
        Trip::factory(8)
            ->recycle([$vehicles, $drivers])
            ->create();

        $packages = Package::factory(6)->create();
        $clients = Client::factory(8)->create();

        // Contratos de exemplo já vinculados a clientes e pacotes existentes.
        Contract::factory(5)
            ->recycle([$packages, $clients])
            ->create();
    }
}
