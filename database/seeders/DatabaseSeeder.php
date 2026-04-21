<?php

namespace Database\Seeders;

use App\Models\User;
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
        // Usuario administrador por defecto
        User::updateOrCreate(
            ['email' => 'admin@lostroncos.com'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('password'),
            ]
        );

        $this->call([
            TableSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
