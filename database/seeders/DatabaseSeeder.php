<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
    $this->call([
    UserSeeder::class,
    BrandSeeder::class,
    SupplierSeeder::class,
    RawMaterialSeeder::class,
    RawMaterialCompleteSeeder::class,
    RecipeSeeder::class,
    RecipeCompleteSeeder::class,
    FixRecipeRatiosSeeder::class,
    SettingsSeeder::class, // 👈 ajouter
]);
    }
}