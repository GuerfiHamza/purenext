<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\RawMaterial;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class RawMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $luxmiel        = Brand::where('slug', 'luxmiel')->first();
        $cafizio        = Brand::where('slug', 'cafizio')->first();
        $infuzio        = Brand::where('slug', 'infuzio')->first();
        $epico          = Brand::where('slug', 'epico')->first();
        $fruitaya       = Brand::where('slug', 'fruitaya')->first();

        $miellerie      = Supplier::where('name', 'Miellerie Atlas')->first();
        $torrefaction   = Supplier::where('name', 'Torréfaction Kahwa')->first();
        $fruitsec       = Supplier::where('name', 'FruitSec Kabylie')->first();
        $packaging      = Supplier::where('name', 'Packaging Pro Algérie')->first();
        $epices         = Supplier::where('name', 'Épices du Sud')->first();

        $materials = [
            // ── LUXMIEL ──────────────────────────────────────────
            [
                'name'              => 'Miel Sidre brut',
                'sku'               => 'MP-MIEL-SIDRE',
                'brand_id'          => $luxmiel->id,
                'supplier_id'       => $miellerie->id,
                'unit'              => 'kg',
                'quantity_in_stock' => 500,
                'min_stock_alert'   => 50,
                'cost_per_unit'     => 1800,
            ],
            [
                'name'              => 'Miel Jerjire brut',
                'sku'               => 'MP-MIEL-JERJ',
                'brand_id'          => $luxmiel->id,
                'supplier_id'       => $miellerie->id,
                'unit'              => 'kg',
                'quantity_in_stock' => 300,
                'min_stock_alert'   => 30,
                'cost_per_unit'     => 2200,
            ],
            [
                'name'              => 'Miel Orange brut',
                'sku'               => 'MP-MIEL-ORAN',
                'brand_id'          => $luxmiel->id,
                'supplier_id'       => $miellerie->id,
                'unit'              => 'kg',
                'quantity_in_stock' => 200,
                'min_stock_alert'   => 20,
                'cost_per_unit'     => 1600,
            ],

            // ── CAFIZIO ──────────────────────────────────────────
            [
                'name'              => 'Café Arabica vert',
                'sku'               => 'MP-CAFE-ARA',
                'brand_id'          => $cafizio->id,
                'supplier_id'       => $torrefaction->id,
                'unit'              => 'kg',
                'quantity_in_stock' => 800,
                'min_stock_alert'   => 100,
                'cost_per_unit'     => 950,
            ],
            [
                'name'              => 'Café Robusta vert',
                'sku'               => 'MP-CAFE-ROB',
                'brand_id'          => $cafizio->id,
                'supplier_id'       => $torrefaction->id,
                'unit'              => 'kg',
                'quantity_in_stock' => 400,
                'min_stock_alert'   => 50,
                'cost_per_unit'     => 700,
            ],
            [
                'name'              => 'Capsules aluminium Nespresso',
                'sku'               => 'MP-CAPS-ALU',
                'brand_id'          => null, // partagé CAFIZIO + INFUZIO
                'supplier_id'       => $packaging->id,
                'unit'              => 'piece',
                'quantity_in_stock' => 50000,
                'min_stock_alert'   => 5000,
                'cost_per_unit'     => 4.5,
            ],
            [
                'name'              => 'Azote N2 99.9%',
                'sku'               => 'MP-N2-GAZ',
                'brand_id'          => $cafizio->id,
                'supplier_id'       => null,
                'unit'              => 'l',
                'quantity_in_stock' => 1000,
                'min_stock_alert'   => 100,
                'cost_per_unit'     => 12,
            ],

            // ── INFUZIO ──────────────────────────────────────────
            [
                'name'              => 'Verveine séchée',
                'sku'               => 'MP-TIS-VERV',
                'brand_id'          => $infuzio->id,
                'supplier_id'       => $epices->id,
                'unit'              => 'kg',
                'quantity_in_stock' => 50,
                'min_stock_alert'   => 10,
                'cost_per_unit'     => 850,
            ],
            [
                'name'              => 'Camomille séchée',
                'sku'               => 'MP-TIS-CAMO',
                'brand_id'          => $infuzio->id,
                'supplier_id'       => $epices->id,
                'unit'              => 'kg',
                'quantity_in_stock' => 40,
                'min_stock_alert'   => 10,
                'cost_per_unit'     => 900,
            ],

            // ── EPICO ────────────────────────────────────────────
            [
                'name'              => 'Cumin brut',
                'sku'               => 'MP-EPI-CUMIN',
                'brand_id'          => $epico->id,
                'supplier_id'       => $epices->id,
                'unit'              => 'kg',
                'quantity_in_stock' => 200,
                'min_stock_alert'   => 20,
                'cost_per_unit'     => 420,
            ],
            [
                'name'              => 'Coriandre brute',
                'sku'               => 'MP-EPI-CORI',
                'brand_id'          => $epico->id,
                'supplier_id'       => $epices->id,
                'unit'              => 'kg',
                'quantity_in_stock' => 150,
                'min_stock_alert'   => 15,
                'cost_per_unit'     => 380,
            ],
            [
                'name'              => 'Piment fort brut',
                'sku'               => 'MP-EPI-PIMENT',
                'brand_id'          => $epico->id,
                'supplier_id'       => $epices->id,
                'unit'              => 'kg',
                'quantity_in_stock' => 100,
                'min_stock_alert'   => 10,
                'cost_per_unit'     => 550,
            ],

            // ── FRUITAYA ─────────────────────────────────────────
            [
                'name'              => 'Figues fraîches',
                'sku'               => 'MP-FRT-FIGUE',
                'brand_id'          => $fruitaya->id,
                'supplier_id'       => $fruitsec->id,
                'unit'              => 'kg',
                'quantity_in_stock' => 300,
                'min_stock_alert'   => 30,
                'cost_per_unit'     => 280,
            ],
            [
                'name'              => 'Abricots frais',
                'sku'               => 'MP-FRT-ABRIC',
                'brand_id'          => $fruitaya->id,
                'supplier_id'       => $fruitsec->id,
                'unit'              => 'kg',
                'quantity_in_stock' => 250,
                'min_stock_alert'   => 25,
                'cost_per_unit'     => 220,
            ],

            // ── PACKAGING COMMUN ─────────────────────────────────
            [
                'name'              => 'Film PET/ALU/PE sticks',
                'sku'               => 'MP-FILM-STICK',
                'brand_id'          => null,
                'supplier_id'       => $packaging->id,
                'unit'              => 'm2',
                'quantity_in_stock' => 500,
                'min_stock_alert'   => 50,
                'cost_per_unit'     => 85,
            ],
            [
                'name'              => 'Doypacks 50g',
                'sku'               => 'MP-DOYPACK-50',
                'brand_id'          => null,
                'supplier_id'       => $packaging->id,
                'unit'              => 'piece',
                'quantity_in_stock' => 10000,
                'min_stock_alert'   => 1000,
                'cost_per_unit'     => 8,
            ],
            [
                'name'              => 'Sachets sous vide 100g',
                'sku'               => 'MP-SACHET-100',
                'brand_id'          => null,
                'supplier_id'       => $packaging->id,
                'unit'              => 'piece',
                'quantity_in_stock' => 8000,
                'min_stock_alert'   => 500,
                'cost_per_unit'     => 6.5,
            ],
        ];

        foreach ($materials as $material) {
            RawMaterial::create($material);
        }
    }
}