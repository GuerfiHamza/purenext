<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\RawMaterial;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\RecipePackaging;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        // ── LUXMIEL Miel Sidre ───────────────────────────────
        $luxmiel = Brand::where('slug', 'luxmiel')->first();
        $mielSidre = RawMaterial::where('sku', 'MP-MIEL-SIDRE')->first();
        $filmStick = RawMaterial::where('sku', 'MP-FILM-STICK')->first();

        $recipe = Recipe::create([
            'name'             => 'LUXMIEL Miel Sidre Sticks',
            'brand_id'         => $luxmiel->id,
            'version'          => '1.0',
            'yield_unit'       => 'packet',
            'yield_qty'        => 1,
            'loss_percentage'  => 5,
            'notes'            => 'Filtrage + chauffage max 40°C. Humidité cible < 18%.',
            'technical_params' => [
                'temperature_max_c' => 40,
                'humidity_target'   => 18,
                'filtration'        => true,
            ],
        ]);

        RecipeIngredient::create([
            'recipe_id'       => $recipe->id,
            'raw_material_id' => $mielSidre->id,
            'quantity'        => 1,   // 1 kg de miel par kg input
            'unit'            => 'kg',
            'order'           => 1,
        ]);

        RecipeIngredient::create([
            'recipe_id'       => $recipe->id,
            'raw_material_id' => $filmStick->id,
            'quantity'        => 0.0033, // ~3300 cm² par kg (150mm × 22mm × nb packets)
            'unit'            => 'm2',
            'order'           => 2,
        ]);

        // Options packaging LUXMIEL (5g / 10g / 20g / 30g)
        $packagingOptions = [
            ['size' => 5,  'label' => '5g Mini - Restauration',  'default' => false, 'capacity' => 3000],
            ['size' => 10, 'label' => '10g Petite dose',          'default' => false, 'capacity' => 2500],
            ['size' => 20, 'label' => '20g Standard - Hôtels',   'default' => true,  'capacity' => 2000],
            ['size' => 30, 'label' => '30g Premium',              'default' => false, 'capacity' => 1500],
        ];

        foreach ($packagingOptions as $opt) {
            RecipePackaging::create([
                'recipe_id'                 => $recipe->id,
                'packet_size_g'             => $opt['size'],
                'packet_label'              => $opt['label'],
                'film_type'                 => 'PET/ALU/PE',
                'film_width_mm'             => 22,
                'film_length_mm'            => 150,
                'machine_capacity_per_hour' => $opt['capacity'],
                'is_default'                => $opt['default'],
            ]);
        }

        // ── CAFIZIO Arabica ──────────────────────────────────
        $cafizio   = Brand::where('slug', 'cafizio')->first();
        $arabica   = RawMaterial::where('sku', 'MP-CAFE-ARA')->first();
        $capsules  = RawMaterial::where('sku', 'MP-CAPS-ALU')->first();
        $n2        = RawMaterial::where('sku', 'MP-N2-GAZ')->first();

        $recipeCafe = Recipe::create([
            'name'             => 'CAFIZIO Arabica Capsule',
            'brand_id'         => $cafizio->id,
            'version'          => '1.0',
            'yield_unit'       => 'packet',
            'yield_qty'        => 1,
            'loss_percentage'  => 2,
            'notes'            => 'Mouture 250-350 microns. Flush N2 avant scellage.',
            'technical_params' => [
                'grind_microns_min' => 250,
                'grind_microns_max' => 350,
                'capsule_diameter'  => 37,
                'n2_flush'          => true,
            ],
        ]);

        RecipeIngredient::create([
            'recipe_id'       => $recipeCafe->id,
            'raw_material_id' => $arabica->id,
            'quantity'        => 1,       // 1 kg café → ~166 capsules à 6g
            'unit'            => 'kg',
            'order'           => 1,
        ]);

        RecipeIngredient::create([
            'recipe_id'       => $recipeCafe->id,
            'raw_material_id' => $capsules->id,
            'quantity'        => 166,     // 1000g ÷ 6g/capsule
            'unit'            => 'piece',
            'order'           => 2,
        ]);

        RecipeIngredient::create([
            'recipe_id'       => $recipeCafe->id,
            'raw_material_id' => $n2->id,
            'quantity'        => 0.5,     // 0.5L N2 par kg café
            'unit'            => 'l',
            'order'           => 3,
        ]);

        RecipePackaging::create([
            'recipe_id'                 => $recipeCafe->id,
            'packet_size_g'             => 6,
            'packet_label'              => '6g Capsule x1',
            'film_type'                 => 'Aluminium',
            'film_width_mm'             => 38,
            'film_length_mm'            => 38,
            'machine_capacity_per_hour' => 1800,
            'is_default'                => true,
        ]);

        // ── EPICO Cumin ──────────────────────────────────────
        $epico   = Brand::where('slug', 'epico')->first();
        $cumin   = RawMaterial::where('sku', 'MP-EPI-CUMIN')->first();
        $doypack = RawMaterial::where('sku', 'MP-DOYPACK-50')->first();

        $recipeEpico = Recipe::create([
            'name'             => 'EPICO Cumin moulu Doypack',
            'brand_id'         => $epico->id,
            'version'          => '1.0',
            'yield_unit'       => 'packet',
            'yield_qty'        => 1,
            'loss_percentage'  => 3,
            'notes'            => 'Mesh 80-100. Broyage avant conditionnement.',
            'technical_params' => [
                'mesh_min'    => 80,
                'mesh_max'    => 100,
                'grind_order' => ['cumin'],
            ],
        ]);

        RecipeIngredient::create([
            'recipe_id'       => $recipeEpico->id,
            'raw_material_id' => $cumin->id,
            'quantity'        => 1,
            'unit'            => 'kg',
            'order'           => 1,
        ]);

        RecipeIngredient::create([
            'recipe_id'       => $recipeEpico->id,
            'raw_material_id' => $doypack->id,
            'quantity'        => 20,   // 1kg → 20 doypacks de 50g
            'unit'            => 'piece',
            'order'           => 2,
        ]);

        RecipePackaging::create([
            'recipe_id'                 => $recipeEpico->id,
            'packet_size_g'             => 50,
            'packet_label'              => '50g Doypack',
            'film_type'                 => 'Doypack kraft/alu',
            'machine_capacity_per_hour' => 800,
            'is_default'                => true,
        ]);
    }
}