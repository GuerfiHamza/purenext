<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Gérant PURENEXT',
            'email'    => 'gerant@purenext.dz',
            'password' => Hash::make('purenext2026'),
            'role'     => 'gerant',
        ]);

        User::create([
            'name'     => 'Opérateur Production',
            'email'    => 'operateur@purenext.dz',
            'password' => Hash::make('purenext2026'),
            'role'     => 'operateur',
        ]);

        User::create([
            'name'     => 'Commercial',
            'email'    => 'commercial@purenext.dz',
            'password' => Hash::make('purenext2026'),
            'role'     => 'commercial',
        ]);
    }
}