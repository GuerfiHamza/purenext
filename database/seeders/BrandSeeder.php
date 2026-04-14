<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'name'        => 'FRUITAYA',
                'slug'        => 'fruitaya',
                'color_hex'   => '#E8523A',
                'slogan'      => 'Le meilleur des fruits algériens',
                'description' => 'Fruits secs premium, 9 variétés sous vide.',
            ],
            [
                'name'        => 'FRUITAYA PRESTIGE',
                'slug'        => 'fruitaya-prestige',
                'color_hex'   => '#8B1A1A',
                'slogan'      => 'L\'excellence chocolatée',
                'description' => 'Fruits enrobés chocolat, coffrets S/M/L/XL.',
            ],
            [
                'name'        => 'LUXMIEL',
                'slug'        => 'luxmiel',
                'color_hex'   => '#F5A623',
                'slogan'      => 'Le miel d\'exception',
                'description' => 'Miel Sidre, Jerjire, Orange en sticks et barquettes.',
            ],
            [
                'name'        => 'CAFIZIO',
                'slug'        => 'cafizio',
                'color_hex'   => '#3B1F0A',
                'slogan'      => 'Le café algérien en capsule',
                'description' => '14 références café en capsules aluminium compatibles Nespresso.',
            ],
            [
                'name'        => 'INFUZIO',
                'slug'        => 'infuzio',
                'color_hex'   => '#4CAF50',
                'slogan'      => 'Les tisanes naturelles en capsule',
                'description' => '7 tisanes en capsules, extraction 70-100°C.',
            ],
            [
                'name'        => 'EPICO',
                'slug'        => 'epico',
                'color_hex'   => '#FF6F00',
                'slogan'      => 'Les épices authentiques d\'Algérie',
                'description' => '9 épices et mélanges en doypacks et pots.',
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}