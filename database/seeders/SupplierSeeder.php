<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name'           => 'Miellerie Atlas',
                'contact_name'   => 'Mohammed Benali',
                'email'          => 'contact@miellerie-atlas.dz',
                'phone'          => '+213 550 11 22 33',
                'country'        => 'DZ',
                'lead_time_days' => 5,
                'notes'          => 'Fournisseur principal miel Sidre et Jerjire.',
            ],
            [
                'name'           => 'Torréfaction Kahwa',
                'contact_name'   => 'Karim Meziani',
                'email'          => 'kahwa@torrefaction.dz',
                'phone'          => '+213 661 44 55 66',
                'country'        => 'DZ',
                'lead_time_days' => 7,
                'notes'          => 'Café vert Arabica et Robusta, livraison Alger.',
            ],
            [
                'name'           => 'FruitSec Kabylie',
                'contact_name'   => 'Lynda Aït Ouali',
                'email'          => 'fruits@kabylie-sec.dz',
                'phone'          => '+213 772 88 99 00',
                'country'        => 'DZ',
                'lead_time_days' => 3,
                'notes'          => 'Fruits frais et fruits secs, toutes variétés.',
            ],
            [
                'name'           => 'Packaging Pro Algérie',
                'contact_name'   => 'Sofiane Djalab',
                'email'          => 'sofiane@packpro.dz',
                'phone'          => '+213 555 12 34 56',
                'country'        => 'DZ',
                'lead_time_days' => 10,
                'notes'          => 'Capsules alu, film PET/ALU/PE, doypacks, sticks.',
            ],
            [
                'name'           => 'Épices du Sud',
                'contact_name'   => 'Rachid Hammoudi',
                'email'          => 'epices@sud-dz.com',
                'phone'          => '+213 699 22 33 44',
                'country'        => 'DZ',
                'lead_time_days' => 7,
                'notes'          => 'Épices brutes : cumin, coriandre, piment, ras el hanout.',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}