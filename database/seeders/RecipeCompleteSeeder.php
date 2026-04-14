<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\RawMaterial;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\RecipePackaging;
use Illuminate\Database\Seeder;

class RecipeCompleteSeeder extends Seeder
{
    public function run(): void
    {
        // ── Brands ───────────────────────────────────────────────────
        $luxmiel          = Brand::where('slug', 'luxmiel')->first();
        $cafizio          = Brand::where('slug', 'cafizio')->first();
        $infuzio          = Brand::where('slug', 'infuzio')->first();
        $epico            = Brand::where('slug', 'epico')->first();
        $fruitaya         = Brand::where('slug', 'fruitaya')->first();

        // ── Raw Materials ────────────────────────────────────────────
        $mielJerjire  = RawMaterial::where('sku', 'MP-MIEL-JERJ')->first();
        $mielOrange   = RawMaterial::where('sku', 'MP-MIEL-ORAN')->first();
        $arabica      = RawMaterial::where('sku', 'MP-CAFE-ARA')->first();
        $robusta      = RawMaterial::where('sku', 'MP-CAFE-ROB')->first();
        $decaf        = RawMaterial::where('sku', 'MP-CAFE-DECAF')->first();
        $capsules     = RawMaterial::where('sku', 'MP-CAPS-ALU')->first();
        $n2           = RawMaterial::where('sku', 'MP-N2-GAZ')->first();
        $filmStick    = RawMaterial::where('sku', 'MP-FILM-STICK')->first();
        $doypack50    = RawMaterial::where('sku', 'MP-DOYPACK-50')->first();
        $doypack100   = RawMaterial::where('sku', 'MP-DOYPACK-100')->first();
        $sachet       = RawMaterial::where('sku', 'MP-SACHET-100')->first();

        // Arômes CAFIZIO
        $aroCannelle  = RawMaterial::where('sku', 'MP-ARO-CANNELLE')->first();
        $aroVanille   = RawMaterial::where('sku', 'MP-ARO-VANILLE')->first();
        $aroCacao     = RawMaterial::where('sku', 'MP-ARO-CACAO')->first();
        $aroCaramel   = RawMaterial::where('sku', 'MP-ARO-CARAMEL')->first();
        $aroNoisette  = RawMaterial::where('sku', 'MP-ARO-NOISETTE')->first();
        $cardamomeCafe= RawMaterial::where('sku', 'MP-ARO-CARDAMOME')->first();
        $aroMenthe    = RawMaterial::where('sku', 'MP-ARO-MENTHE')->first();

        // Tisanes INFUZIO
        $theVert      = RawMaterial::where('sku', 'MP-TIS-THE-VERT')->first();
        $mentheTis    = RawMaterial::where('sku', 'MP-TIS-MENTHE')->first();
        $rooibos      = RawMaterial::where('sku', 'MP-TIS-ROOIBOS')->first();
        $matcha       = RawMaterial::where('sku', 'MP-TIS-MATCHA')->first();
        $fenouil      = RawMaterial::where('sku', 'MP-TIS-FENOUIL')->first();
        $anis         = RawMaterial::where('sku', 'MP-TIS-ANIS')->first();
        $gingembre    = RawMaterial::where('sku', 'MP-TIS-GINGEMBRE')->first();
        $citron       = RawMaterial::where('sku', 'MP-TIS-CITRON')->first();
        $camomille    = RawMaterial::where('sku', 'MP-TIS-CAMO')->first();

        // Épices EPICO
        $cumin        = RawMaterial::where('sku', 'MP-EPI-CUMIN')->first();
        $coriandre    = RawMaterial::where('sku', 'MP-EPI-CORI')->first();
        $piment       = RawMaterial::where('sku', 'MP-EPI-PIMENT')->first();
        $cannelleEpi  = RawMaterial::where('sku', 'MP-EPI-CANNELLE')->first();
        $poivre       = RawMaterial::where('sku', 'MP-EPI-POIVRE')->first();
        $cardamomeEpi = RawMaterial::where('sku', 'MP-EPI-CARDAMOME')->first();
        $gingEmbreEpi = RawMaterial::where('sku', 'MP-EPI-GINGEMBRE')->first();
        $curcuma      = RawMaterial::where('sku', 'MP-EPI-CURCUMA')->first();
        $ail          = RawMaterial::where('sku', 'MP-EPI-AIL')->first();
        $oignon       = RawMaterial::where('sku', 'MP-EPI-OIGNON')->first();
        $muscade      = RawMaterial::where('sku', 'MP-EPI-MUSCADE')->first();
        $girofle      = RawMaterial::where('sku', 'MP-EPI-GIROFLE')->first();

        // Fruits FRUITAYA
        $banane       = RawMaterial::where('sku', 'MP-FRT-BANANE')->first();
        $fraise       = RawMaterial::where('sku', 'MP-FRT-FRAISE')->first();
        $mangue       = RawMaterial::where('sku', 'MP-FRT-MANGUE')->first();
        $pomme        = RawMaterial::where('sku', 'MP-FRT-POMME')->first();
        $abricot      = RawMaterial::where('sku', 'MP-FRT-ABRIC')->first();
        $figue        = RawMaterial::where('sku', 'MP-FRT-FIGUE')->first();
        $cerise       = RawMaterial::where('sku', 'MP-FRT-CERISE')->first();
        $kiwi         = RawMaterial::where('sku', 'MP-FRT-KIWI')->first();
        $ananas       = RawMaterial::where('sku', 'MP-FRT-ANANAS')->first();

        // ════════════════════════════════════════════════════════════
        // LUXMIEL — Jerjire & Orange
        // ════════════════════════════════════════════════════════════

        foreach ([
            ['mp' => $mielJerjire, 'name' => 'LUXMIEL Miel Jerjire Sticks', 'temp_min' => 36, 'temp_max' => 40, 'note' => 'Nettoyer machine après lot Jerjire. Humidité < 18%.'],
            ['mp' => $mielOrange,  'name' => 'LUXMIEL Miel Orange Sticks',  'temp_min' => 35, 'temp_max' => 38, 'note' => 'Ne pas dépasser 38°C — arôme volatil. Conditionner rapidement.'],
        ] as $data) {
            $r = Recipe::create([
                'name'             => $data['name'],
                'brand_id'         => $luxmiel->id,
                'version'          => '1.0',
                'yield_unit'       => 'packet',
                'yield_qty'        => 1,
                'loss_percentage'  => 5,
                'notes'            => $data['note'],
                'technical_params' => ['temperature_min_c' => $data['temp_min'], 'temperature_max_c' => $data['temp_max'], 'humidity_target' => 18],
            ]);
            RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $data['mp']->id, 'quantity' => 1, 'unit' => 'kg', 'order' => 1]);
            RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $filmStick->id, 'quantity' => 0.0033, 'unit' => 'm2', 'order' => 2]);
            foreach ([
                [5,  '5g Mini - Restauration',  false, 3000],
                [10, '10g Petite dose',          false, 2500],
                [20, '20g Standard - Hôtels',    true,  2000],
                [30, '30g Premium',              false, 1500],
            ] as [$size, $label, $default, $cap]) {
                RecipePackaging::create([
                    'recipe_id'                 => $r->id,
                    'packet_size_g'             => $size,
                    'packet_label'              => $label,
                    'film_type'                 => 'PET/ALU/PE',
                    'film_width_mm'             => 22,
                    'film_length_mm'            => 150,
                    'machine_capacity_per_hour' => $cap,
                    'is_default'                => $default,
                ]);
            }
        }

        // ════════════════════════════════════════════════════════════
        // CAFIZIO — 7 Niveaux d'intensité
        // ════════════════════════════════════════════════════════════

        $intensites = [
            ['name' => 'CAFIZIO DOUX Intensité 6',     'ara' => 6.0, 'rob' => 0,   'notes' => 'Arabica 100%. Torréfaction légère 195-205°C. Repos 24-48h.', 'params' => ['torrefaction_c' => '195-205', 'repos_h' => 24]],
            ['name' => 'CAFIZIO EQUILIBRE Intensité 7', 'ara' => 4.8, 'rob' => 1.2, 'notes' => 'Arabica 80% + Robusta 20%. Torréfaction séparée.', 'params' => ['torrefaction_arabica_c' => '210-215', 'torrefaction_robusta_c' => '215-220']],
            ['name' => 'CAFIZIO INTENSE Intensité 8',   'ara' => 4.2, 'rob' => 1.8, 'notes' => 'Arabica 70% + Robusta 30%. 220-230°C.', 'params' => ['torrefaction_c' => '220-230', 'repos_h' => 48]],
            ['name' => 'CAFIZIO BOLD Intensité 9',      'ara' => 3.6, 'rob' => 2.4, 'notes' => 'Arabica 60% + Robusta 40%. Dark Roast 225-235°C.', 'params' => ['torrefaction_c' => '225-235', 'repos_h' => 48]],
            ['name' => 'CAFIZIO FORTE Intensité 10',    'ara' => 3.0, 'rob' => 3.0, 'notes' => 'Blend 50/50. Full Dark 230-240°C. Mouture très fine.', 'params' => ['torrefaction_c' => '230-240', 'mouture' => 'tres fine']],
            ['name' => 'CAFIZIO SUPREMO Intensité 11',  'ara' => 1.8, 'rob' => 4.2, 'notes' => 'Robusta 70% + Arabica 30%. 235-245°C. Repos 72h.', 'params' => ['torrefaction_c' => '235-245', 'repos_h' => 72]],
            ['name' => 'CAFIZIO NERO Intensité 12',     'ara' => 0,   'rob' => 6.0, 'notes' => 'Robusta 100% grade A. 240-250°C. Repos 72h. Edition limitée.', 'params' => ['torrefaction_c' => '240-250', 'repos_h' => 72, 'edition' => 'limitee']],
        ];

        foreach ($intensites as $data) {
            $r = Recipe::create([
                'name' => $data['name'], 'brand_id' => $cafizio->id, 'version' => '1.0',
                'yield_unit' => 'packet', 'yield_qty' => 1, 'loss_percentage' => 2,
                'notes' => $data['notes'], 'technical_params' => $data['params'],
            ]);
            $o = 1;
            if ($data['ara'] > 0) RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $arabica->id, 'quantity' => round($data['ara'] / 1000, 5), 'unit' => 'kg', 'order' => $o++]);
            if ($data['rob'] > 0) RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $robusta->id, 'quantity' => round($data['rob'] / 1000, 5), 'unit' => 'kg', 'order' => $o++]);
            RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $capsules->id, 'quantity' => 1, 'unit' => 'piece', 'order' => $o++]);
            RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $n2->id, 'quantity' => 0.003, 'unit' => 'l', 'order' => $o]);
            RecipePackaging::create(['recipe_id' => $r->id, 'packet_size_g' => 6, 'packet_label' => '6g Capsule x1', 'film_type' => 'Aluminium', 'film_width_mm' => 38, 'film_length_mm' => 38, 'machine_capacity_per_hour' => 1800, 'is_default' => true]);
        }

        // ════════════════════════════════════════════════════════════
        // CAFIZIO — 7 Arômes
        // ════════════════════════════════════════════════════════════

        // cafe_g, arome_mp, arome_g, extra_mp, extra_g
        $aromes = [
            ['name' => 'CAFIZIO CANNELLE',  'cafe_g' => 5.76, 'arome_mp' => $aroCannelle,  'arome_g' => 0.24, 'extra_mp' => null,         'extra_g' => 0,    'notes' => '96% café + 4% cannelle. Mélanger 5 min en 3 fois.'],
            ['name' => 'CAFIZIO VANILLE',   'cafe_g' => 5.7,  'arome_mp' => $aroVanille,   'arome_g' => 0.18, 'extra_mp' => null,         'extra_g' => 0.12, 'notes' => '95% café + 3% vanille + 2% arôme. Repos 1h avant remplissage.'],
            ['name' => 'CAFIZIO CHOCOLAT',  'cafe_g' => 5.52, 'arome_mp' => $aroCacao,     'arome_g' => 0.36, 'extra_mp' => null,         'extra_g' => 0.12, 'notes' => '92% café + 6% cacao + 2% arôme. Mélanger 8 min.'],
            ['name' => 'CAFIZIO CARAMEL',   'cafe_g' => 5.7,  'arome_mp' => $aroCaramel,   'arome_g' => 0.24, 'extra_mp' => null,         'extra_g' => 0,    'notes' => '95% café + 4% caramel. Halal certifié.'],
            ['name' => 'CAFIZIO NOISETTE',  'cafe_g' => 5.76, 'arome_mp' => $aroNoisette,  'arome_g' => 0.24, 'extra_mp' => null,         'extra_g' => 0,    'notes' => '96% café + 4% noisette. Mélanger 3 min.'],
            ['name' => 'CAFIZIO CARDAMOME', 'cafe_g' => 5.64, 'arome_mp' => $cardamomeCafe,'arome_g' => 0.36, 'extra_mp' => null,         'extra_g' => 0,    'notes' => '94% café + 6% cardamome verte. Tamiser très finement. Mélanger 8 min.'],
            ['name' => 'CAFIZIO MENTHE',    'cafe_g' => 5.76, 'arome_mp' => $aroMenthe,    'arome_g' => 0.24, 'extra_mp' => null,         'extra_g' => 0,    'notes' => '96% café + 4% menthe poivrée. Max 4%. Mélanger 3 min. Edition limitée été.'],
        ];

        foreach ($aromes as $data) {
            $r = Recipe::create([
                'name' => $data['name'], 'brand_id' => $cafizio->id, 'version' => '1.0',
                'yield_unit' => 'packet', 'yield_qty' => 1, 'loss_percentage' => 2,
                'notes' => $data['notes'], 'technical_params' => ['ratio_arome_pct' => round($data['arome_g'] / 6 * 100, 1)],
            ]);
            RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $arabica->id, 'quantity' => round($data['cafe_g'] / 1000, 5), 'unit' => 'kg', 'order' => 1]);
            RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $data['arome_mp']->id, 'quantity' => round($data['arome_g'] / 1000, 5), 'unit' => 'kg', 'order' => 2]);
            RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $capsules->id, 'quantity' => 1, 'unit' => 'piece', 'order' => 3]);
            RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $n2->id, 'quantity' => 0.003, 'unit' => 'l', 'order' => 4]);
            RecipePackaging::create(['recipe_id' => $r->id, 'packet_size_g' => 6, 'packet_label' => '6g Capsule Aromatisée', 'film_type' => 'Aluminium', 'film_width_mm' => 38, 'film_length_mm' => 38, 'machine_capacity_per_hour' => 1800, 'is_default' => true]);
        }

        // ════════════════════════════════════════════════════════════
        // CAFIZIO — 4 Décaféinés
        // ════════════════════════════════════════════════════════════

        $decafs = [
            ['name' => 'CAFIZIO DECAF NATURE',   'decaf_g' => 6.0,  'arome_mp' => null,         'arome_g' => 0],
            ['name' => 'CAFIZIO DECAF VANILLE',   'decaf_g' => 5.7,  'arome_mp' => $aroVanille,  'arome_g' => 0.3],
            ['name' => 'CAFIZIO DECAF CHOCOLAT',  'decaf_g' => 5.52, 'arome_mp' => $aroCacao,    'arome_g' => 0.48],
            ['name' => 'CAFIZIO DECAF NOISETTE',  'decaf_g' => 5.76, 'arome_mp' => $aroNoisette, 'arome_g' => 0.24],
        ];

        foreach ($decafs as $data) {
            $r = Recipe::create([
                'name' => $data['name'], 'brand_id' => $cafizio->id, 'version' => '1.0',
                'yield_unit' => 'packet', 'yield_qty' => 1, 'loss_percentage' => 2,
                'notes' => 'Swiss Water Process. Caféine < 0.1%. Torréfaction -5°C vs normal.',
                'technical_params' => ['decaf' => true, 'methode' => 'Swiss Water Process', 'cafeine_max_pct' => 0.1],
            ]);
            RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $decaf->id, 'quantity' => round($data['decaf_g'] / 1000, 5), 'unit' => 'kg', 'order' => 1]);
            if ($data['arome_mp']) {
                RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $data['arome_mp']->id, 'quantity' => round($data['arome_g'] / 1000, 5), 'unit' => 'kg', 'order' => 2]);
            }
            RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $capsules->id, 'quantity' => 1, 'unit' => 'piece', 'order' => 3]);
            RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $n2->id, 'quantity' => 0.003, 'unit' => 'l', 'order' => 4]);
            RecipePackaging::create(['recipe_id' => $r->id, 'packet_size_g' => 6, 'packet_label' => '6g Capsule DECAF', 'film_type' => 'Aluminium bleu', 'film_width_mm' => 38, 'film_length_mm' => 38, 'machine_capacity_per_hour' => 1800, 'is_default' => true]);
        }

        // ════════════════════════════════════════════════════════════
        // INFUZIO — 7 Tisanes
        // ════════════════════════════════════════════════════════════

        $tisanes = [
            [
                'name' => 'INFUZIO THE VERT', 'qty_g' => 4,
                'ingredients' => [[$theVert, 0.004]],
                'notes' => 'Feuilles brisées 2-3mm. Sécher si humidité > 8%. Tamiser 20 mesh.',
                'params' => ['finesse_mm' => '2-3', 'temp_extraction_c' => '70-80'],
            ],
            [
                'name' => 'INFUZIO MENTHE', 'qty_g' => 3,
                'ingredients' => [[$mentheTis, 0.003]],
                'notes' => 'Sécher 40°C max 3h. Ne pas dépasser 45°C. Petits lots — arôme fugace.',
                'params' => ['temp_sechage_max_c' => 45, 'dlc_mois' => 6],
            ],
            [
                'name' => 'INFUZIO ROOIBOS', 'qty_g' => 4,
                'ingredients' => [[$rooibos, 0.004]],
                'notes' => 'Aiguilles brisées 2-4mm. Sécher si humidité > 10%.',
                'params' => ['temp_extraction_c' => '90-95', 'volume_ml' => '100-150'],
            ],
            [
                'name' => 'INFUZIO MATCHA', 'qty_g' => 4,
                'ingredients' => [[$matcha, 0.004]],
                'notes' => 'Poudre ultra-fine 100-200 mesh. N2 OBLIGATOIRE. Humidité < 5%.',
                'params' => ['mesh' => '100-200', 'humidite_max_pct' => 5, 'n2_obligatoire' => true],
            ],
            [
                'name' => 'INFUZIO DIGESTIF', 'qty_g' => 4,
                'ingredients' => [[$fenouil, 0.0015], [$anis, 0.001], [$mentheTis, 0.0008], [$camomille, 0.0007]],
                'notes' => 'Fenouil 1.5g + Anis 1g + Menthe 0.8g + Camomille 0.7g. Sécher séparément puis mélanger.',
                'params' => ['composition' => ['fenouil_g' => 1.5, 'anis_g' => 1, 'menthe_g' => 0.8, 'camomille_g' => 0.7]],
            ],
            [
                'name' => 'INFUZIO GINGEMBRE CITRON', 'qty_g' => 4,
                'ingredients' => [[$gingembre, 0.003], [$citron, 0.001]],
                'notes' => 'Gingembre 3g + Citron séché 1g. Ratio 3:1. Broyer 80-100 mesh.',
                'params' => ['temp_extraction_c' => '95-100', 'volume_ml' => '150-200'],
            ],
            [
                'name' => 'INFUZIO CAMOMILLE', 'qty_g' => 3.5,
                'ingredients' => [[$camomille, 0.0035]],
                'notes' => 'Fleurs entières. Ne pas broyer. Sécher 35-40°C 4h max.',
                'params' => ['forme' => 'fleurs entieres', 'temp_sechage_max_c' => 40],
            ],
        ];

        foreach ($tisanes as $data) {
            $r = Recipe::create([
                'name' => $data['name'], 'brand_id' => $infuzio->id, 'version' => '1.0',
                'yield_unit' => 'packet', 'yield_qty' => 1, 'loss_percentage' => 3,
                'notes' => $data['notes'], 'technical_params' => $data['params'],
            ]);
            $o = 1;
            foreach ($data['ingredients'] as [$mp, $qty]) {
                RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $mp->id, 'quantity' => $qty, 'unit' => 'kg', 'order' => $o++]);
            }
            RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $capsules->id, 'quantity' => 1, 'unit' => 'piece', 'order' => $o++]);
            RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $n2->id, 'quantity' => 0.003, 'unit' => 'l', 'order' => $o]);
            RecipePackaging::create(['recipe_id' => $r->id, 'packet_size_g' => $data['qty_g'], 'packet_label' => "{$data['qty_g']}g Capsule Tisane", 'film_type' => 'Aluminium', 'film_width_mm' => 38, 'film_length_mm' => 38, 'machine_capacity_per_hour' => 1200, 'is_default' => true]);
        }

        // ════════════════════════════════════════════════════════════
        // EPICO — 8 Épices & Mélanges
        // ════════════════════════════════════════════════════════════

        $epices = [
            [
                'name' => 'EPICO CUMIN', 'loss' => 10,
                'ingredients' => [[$cumin, 1.0]],
                'notes' => '80-100 mesh. Refroidir 30 min. Ordre broyage: 2ème.',
                'params' => ['mesh' => '80-100', 'ordre_broyage' => 2],
            ],
            [
                'name' => 'EPICO PAPRIKA', 'loss' => 10,
                'ingredients' => [[$piment, 1.0]],
                'notes' => 'EPI OBLIGATOIRE: masque FFP2 + lunettes. 80 mesh. Décanter 1h.',
                'params' => ['mesh' => 80, 'epi_obligatoire' => true, 'ordre_broyage' => 4],
            ],
            [
                'name' => 'EPICO PIMENT', 'loss' => 10,
                'ingredients' => [[$piment, 1.0]],
                'notes' => 'PRÉCAUTION: masque FFP2 + lunettes + gants nitrile. Toujours en dernier. Décanter 2h.',
                'params' => ['mesh' => 80, 'epi_obligatoire' => true, 'ordre_broyage' => 5],
            ],
            [
                'name' => 'EPICO CURCUMA', 'loss' => 10,
                'ingredients' => [[$curcuma, 1.0]],
                'notes' => 'Tache définitivement. Gants + tablier. Nettoyage acide citrique. Ordre broyage: 3ème.',
                'params' => ['mesh' => '80-100', 'ordre_broyage' => 3],
            ],
            [
                'name' => 'EPICO CORIANDRE', 'loss' => 10,
                'ingredients' => [[$coriandre, 1.0]],
                'notes' => '80 mesh. Torréfaction légère optionnelle 150°C 5min. Ordre broyage: 1er.',
                'params' => ['mesh' => 80, 'ordre_broyage' => 1],
            ],
            [
                'name' => 'EPICO RAS EL HANOUT', 'loss' => 5,
                'ingredients' => [
                    [$cumin,        0.25],
                    [$coriandre,    0.20],
                    [$cannelleEpi,  0.10],
                    [$gingEmbreEpi, 0.10],
                    [$piment,       0.10],
                    [$curcuma,      0.08],
                    [$poivre,       0.07],
                    [$cardamomeEpi, 0.05],
                    [$muscade,      0.03],
                    [$girofle,      0.02],
                ],
                'notes' => 'Mélange 10 épices. Peser chaque séparément. Mélanger 5 min. Tamiser 80 mesh. Recette confidentielle.',
                'params' => ['mesh' => 80, 'ordre_broyage' => 6, 'recette_confidentielle' => true],
            ],
            [
                'name' => 'EPICO BBQ ALGERIEN', 'loss' => 5,
                'ingredients' => [
                    [$piment,  0.30],
                    [$cumin,   0.20],
                    [$ail,     0.15],
                    [$oignon,  0.10],
                    [$poivre,  0.08],
                    [$piment,  0.07],
                ],
                'notes' => 'Tamiser 60 mesh (plus grossier). Ail et oignon achetés prêts.',
                'params' => ['mesh' => 60, 'ordre_broyage' => 6],
            ],
            [
                'name' => 'EPICO TANDOORI', 'loss' => 5,
                'ingredients' => [
                    [$piment,       0.25],
                    [$cumin,        0.15],
                    [$coriandre,    0.15],
                    [$curcuma,      0.10],
                    [$gingEmbreEpi, 0.10],
                    [$ail,          0.08],
                    [$piment,       0.07],
                    [$cardamomeEpi, 0.05],
                    [$cannelleEpi,  0.03],
                    [$poivre,       0.02],
                ],
                'notes' => 'Même process que Ras el Hanout. Couleur rouge-orange caractéristique.',
                'params' => ['mesh' => 80, 'ordre_broyage' => 6],
            ],
        ];

        foreach ($epices as $data) {
            $r = Recipe::create([
                'name' => $data['name'], 'brand_id' => $epico->id, 'version' => '1.0',
                'yield_unit' => 'packet', 'yield_qty' => 1, 'loss_percentage' => $data['loss'],
                'notes' => $data['notes'], 'technical_params' => $data['params'],
            ]);
            $o = 1;
            foreach ($data['ingredients'] as [$mp, $qty]) {
                RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $mp->id, 'quantity' => $qty, 'unit' => 'kg', 'order' => $o++]);
            }
            RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $doypack50->id, 'quantity' => 20, 'unit' => 'piece', 'order' => $o++]);
            RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $doypack100->id, 'quantity' => 10, 'unit' => 'piece', 'order' => $o]);
            RecipePackaging::create(['recipe_id' => $r->id, 'packet_size_g' => 50, 'packet_label' => '50g Doypack', 'film_type' => 'Doypack kraft/alu', 'machine_capacity_per_hour' => 800, 'is_default' => true]);
            RecipePackaging::create(['recipe_id' => $r->id, 'packet_size_g' => 100, 'packet_label' => '100g Doypack', 'film_type' => 'Doypack kraft/alu', 'machine_capacity_per_hour' => 600, 'is_default' => false]);
        }

        // ════════════════════════════════════════════════════════════
        // FRUITAYA — 9 Fruits secs
        // ════════════════════════════════════════════════════════════

        $fruits = [
            ['name' => 'FRUITAYA BANANE',  'mp' => $banane,  'loss' => 70, 'temp' => 57, 'duree' => '10-12h', 'notes' => 'Rondelles 4-5mm. Bain citron optionnel. Retourner à mi-parcours.'],
            ['name' => 'FRUITAYA FRAISE',  'mp' => $fraise,  'loss' => 85, 'temp' => 57, 'duree' => '8-12h',  'notes' => 'Tranches 5-6mm face coupée vers haut. Vérifier toutes les 2h.'],
            ['name' => 'FRUITAYA MANGUE',  'mp' => $mangue,  'loss' => 75, 'temp' => 57, 'duree' => '10-14h', 'notes' => 'Tranches 5-6mm ou lanières. Bain citron vert 1min.'],
            ['name' => 'FRUITAYA POMME',   'mp' => $pomme,   'loss' => 75, 'temp' => 57, 'duree' => '8-12h',  'notes' => 'Rondelles 4-5mm. Bain citron OBLIGATOIRE. Option cannelle.'],
            ['name' => 'FRUITAYA ABRICOT', 'mp' => $abricot, 'loss' => 75, 'temp' => 57, 'duree' => '20-28h', 'notes' => 'Couper en deux. Ne pas éplucher. Blanchiment optionnel 30sec. Planifier sur 2 cycles.'],
            ['name' => 'FRUITAYA FIGUE',   'mp' => $figue,   'loss' => 70, 'temp' => 60, 'duree' => '20-24h', 'notes' => 'Entières ou en deux. Incision en croix. Bloom sucre = normal.'],
            ['name' => 'FRUITAYA CERISE',  'mp' => $cerise,  'loss' => 75, 'temp' => 57, 'duree' => '24-36h', 'notes' => 'Dénoyauter + couper en deux. Le plus long à sécher. Produit premium.'],
            ['name' => 'FRUITAYA KIWI',    'mp' => $kiwi,    'loss' => 80, 'temp' => 57, 'duree' => '10-14h', 'notes' => 'Rondelles 5-6mm. Éplucher obligatoirement. Bain citron pour couleur verte.'],
            ['name' => 'FRUITAYA ANANAS',  'mp' => $ananas,  'loss' => 80, 'temp' => 57, 'duree' => '12-16h', 'notes' => 'Rondelles 5-8mm ou triangles. Retirer cœur fibreux. Surveiller fin — riche en sucre.'],
        ];

        foreach ($fruits as $data) {
            $r = Recipe::create([
                'name' => $data['name'], 'brand_id' => $fruitaya->id, 'version' => '1.0',
                'yield_unit' => 'packet', 'yield_qty' => 1, 'loss_percentage' => $data['loss'],
                'notes' => $data['notes'],
                'technical_params' => ['temp_c' => $data['temp'], 'duree' => $data['duree'], 'humidite_residuelle_pct' => 15],
            ]);
            RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $data['mp']->id, 'quantity' => 1, 'unit' => 'kg', 'order' => 1]);
            RecipeIngredient::create(['recipe_id' => $r->id, 'raw_material_id' => $sachet->id, 'quantity' => 10, 'unit' => 'piece', 'order' => 2]);
            RecipePackaging::create(['recipe_id' => $r->id, 'packet_size_g' => 100, 'packet_label' => '100g Sachet sous vide', 'film_type' => 'Alu/PE multicouche sous vide', 'machine_capacity_per_hour' => 600, 'is_default' => true]);
        }
    }
}