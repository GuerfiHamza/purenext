<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\RawMaterial;
use Illuminate\Database\Seeder;

class FixRecipeRatiosSeeder extends Seeder
{
    public function run(): void
    {
        // ── Raw Materials ────────────────────────────────────────────
        $arabica      = RawMaterial::where('sku', 'MP-CAFE-ARA')->first();
        $robusta      = RawMaterial::where('sku', 'MP-CAFE-ROB')->first();
        $decaf        = RawMaterial::where('sku', 'MP-CAFE-DECAF')->first();
        $capsules     = RawMaterial::where('sku', 'MP-CAPS-ALU')->first();
        $n2           = RawMaterial::where('sku', 'MP-N2-GAZ')->first();
        $aroCannelle  = RawMaterial::where('sku', 'MP-ARO-CANNELLE')->first();
        $aroVanille   = RawMaterial::where('sku', 'MP-ARO-VANILLE')->first();
        $aroCacao     = RawMaterial::where('sku', 'MP-ARO-CACAO')->first();
        $aroCaramel   = RawMaterial::where('sku', 'MP-ARO-CARAMEL')->first();
        $aroNoisette  = RawMaterial::where('sku', 'MP-ARO-NOISETTE')->first();
        $cardamomeCafe= RawMaterial::where('sku', 'MP-ARO-CARDAMOME')->first();
        $aroMenthe    = RawMaterial::where('sku', 'MP-ARO-MENTHE')->first();

        $theVert      = RawMaterial::where('sku', 'MP-TIS-THE-VERT')->first();
        $mentheTis    = RawMaterial::where('sku', 'MP-TIS-MENTHE')->first();
        $rooibos      = RawMaterial::where('sku', 'MP-TIS-ROOIBOS')->first();
        $matcha       = RawMaterial::where('sku', 'MP-TIS-MATCHA')->first();
        $fenouil      = RawMaterial::where('sku', 'MP-TIS-FENOUIL')->first();
        $anis         = RawMaterial::where('sku', 'MP-TIS-ANIS')->first();
        $gingembre    = RawMaterial::where('sku', 'MP-TIS-GINGEMBRE')->first();
        $citron       = RawMaterial::where('sku', 'MP-TIS-CITRON')->first();
        $camomille    = RawMaterial::where('sku', 'MP-TIS-CAMO')->first();

        $cumin        = RawMaterial::where('sku', 'MP-EPI-CUMIN')->first();
        $coriandre    = RawMaterial::where('sku', 'MP-EPI-CORI')->first();
        $piment       = RawMaterial::where('sku', 'MP-EPI-PIMENT')->first();
        $curcuma      = RawMaterial::where('sku', 'MP-EPI-CURCUMA')->first();
        $cannelleEpi  = RawMaterial::where('sku', 'MP-EPI-CANNELLE')->first();
        $poivre       = RawMaterial::where('sku', 'MP-EPI-POIVRE')->first();
        $cardamomeEpi = RawMaterial::where('sku', 'MP-EPI-CARDAMOME')->first();
        $gingEmbreEpi = RawMaterial::where('sku', 'MP-EPI-GINGEMBRE')->first();
        $ail          = RawMaterial::where('sku', 'MP-EPI-AIL')->first();
        $oignon       = RawMaterial::where('sku', 'MP-EPI-OIGNON')->first();
        $muscade      = RawMaterial::where('sku', 'MP-EPI-MUSCADE')->first();
        $girofle      = RawMaterial::where('sku', 'MP-EPI-GIROFLE')->first();
        $doypack50    = RawMaterial::where('sku', 'MP-DOYPACK-50')->first();
        $doypack100   = RawMaterial::where('sku', 'MP-DOYPACK-100')->first();
        $sachet       = RawMaterial::where('sku', 'MP-SACHET-100')->first();

        // ════════════════════════════════════════════════════════════
        // CAFIZIO — Ratios par kg de café input
        // 1 kg café → 1000g ÷ 6g = round(1000 / 6, 4) capsules
        // Capsules: round(1000 / 6, 4) / kg input
        // N2: 0.5L / kg input
        // ════════════════════════════════════════════════════════════

        // 7 Intensités
        $intensites = [
            ['name' => 'CAFIZIO DOUX Intensité 6',     'ara' => 1.0,  'rob' => 0],
            ['name' => 'CAFIZIO EQUILIBRE Intensité 7', 'ara' => 0.8,  'rob' => 0.2],
            ['name' => 'CAFIZIO INTENSE Intensité 8',   'ara' => 0.7,  'rob' => 0.3],
            ['name' => 'CAFIZIO BOLD Intensité 9',      'ara' => 0.6,  'rob' => 0.4],
            ['name' => 'CAFIZIO FORTE Intensité 10',    'ara' => 0.5,  'rob' => 0.5],
            ['name' => 'CAFIZIO SUPREMO Intensité 11',  'ara' => 0.3,  'rob' => 0.7],
            ['name' => 'CAFIZIO NERO Intensité 12',     'ara' => 0,    'rob' => 1.0],
        ];

        foreach ($intensites as $data) {
            $recipe = Recipe::where('name', $data['name'])->first();
            if (!$recipe) continue;
            RecipeIngredient::where('recipe_id', $recipe->id)->delete();
            $o = 1;
            if ($data['ara'] > 0) RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $arabica->id, 'quantity' => $data['ara'], 'unit' => 'kg', 'order' => $o++]);
            if ($data['rob'] > 0) RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $robusta->id, 'quantity' => $data['rob'], 'unit' => 'kg', 'order' => $o++]);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $capsules->id, 'quantity' => round(1000 / 6, 4), 'unit' => 'piece', 'order' => $o++]);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $n2->id, 'quantity' => 0.5, 'unit' => 'l', 'order' => $o]);
        }

        // 7 Arômes — ratios par kg de mélange input
        // Ex CANNELLE: 96% café + 4% cannelle = 0.96kg café + 0.04kg cannelle par kg input
        $aromes = [
            ['name' => 'CAFIZIO CANNELLE',  'ara' => 0.96, 'arome_mp' => $aroCannelle,  'arome_ratio' => 0.04],
            ['name' => 'CAFIZIO VANILLE',   'ara' => 0.95, 'arome_mp' => $aroVanille,   'arome_ratio' => 0.05],
            ['name' => 'CAFIZIO CHOCOLAT',  'ara' => 0.92, 'arome_mp' => $aroCacao,     'arome_ratio' => 0.08],
            ['name' => 'CAFIZIO CARAMEL',   'ara' => 0.95, 'arome_mp' => $aroCaramel,   'arome_ratio' => 0.04],
            ['name' => 'CAFIZIO NOISETTE',  'ara' => 0.96, 'arome_mp' => $aroNoisette,  'arome_ratio' => 0.04],
            ['name' => 'CAFIZIO CARDAMOME', 'ara' => 0.94, 'arome_mp' => $cardamomeCafe,'arome_ratio' => 0.06],
            ['name' => 'CAFIZIO MENTHE',    'ara' => 0.96, 'arome_mp' => $aroMenthe,    'arome_ratio' => 0.04],
        ];

        foreach ($aromes as $data) {
            $recipe = Recipe::where('name', $data['name'])->first();
            if (!$recipe) continue;
            RecipeIngredient::where('recipe_id', $recipe->id)->delete();
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $arabica->id, 'quantity' => $data['ara'], 'unit' => 'kg', 'order' => 1]);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $data['arome_mp']->id, 'quantity' => $data['arome_ratio'], 'unit' => 'kg', 'order' => 2]);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $capsules->id, 'quantity' => round(1000 / 6, 4), 'unit' => 'piece', 'order' => 3]);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $n2->id, 'quantity' => 0.5, 'unit' => 'l', 'order' => 4]);
        }

        // 4 Décaféinés
        $decafs = [
            ['name' => 'CAFIZIO DECAF NATURE',   'arome_mp' => null,        'arome_ratio' => 0],
            ['name' => 'CAFIZIO DECAF VANILLE',   'arome_mp' => $aroVanille, 'arome_ratio' => 0.05],
            ['name' => 'CAFIZIO DECAF CHOCOLAT',  'arome_mp' => $aroCacao,   'arome_ratio' => 0.08],
            ['name' => 'CAFIZIO DECAF NOISETTE',  'arome_mp' => $aroNoisette,'arome_ratio' => 0.04],
        ];

        foreach ($decafs as $data) {
            $recipe = Recipe::where('name', $data['name'])->first();
            if (!$recipe) continue;
            RecipeIngredient::where('recipe_id', $recipe->id)->delete();
            $o = 1;
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $decaf->id, 'quantity' => $data['arome_ratio'] > 0 ? 1 - $data['arome_ratio'] : 1.0, 'unit' => 'kg', 'order' => $o++]);
            if ($data['arome_mp']) {
                RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $data['arome_mp']->id, 'quantity' => $data['arome_ratio'], 'unit' => 'kg', 'order' => $o++]);
            }
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $capsules->id, 'quantity' => round(1000 / 6, 4), 'unit' => 'piece', 'order' => $o++]);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $n2->id, 'quantity' => 0.5, 'unit' => 'l', 'order' => $o]);
        }

        // ════════════════════════════════════════════════════════════
        // INFUZIO — Ratios par kg de plante input
        // 1 kg plante → 1000g ÷ taille capsule = nb capsules
        // Ex THE VERT 4g: 1000 ÷ 4 = 250 capsules / kg
        // ════════════════════════════════════════════════════════════

        $tisanes = [
            ['name' => 'INFUZIO THE VERT',         'mp' => $theVert,   'ratio' => 1.0,                              'caps' => 250,  'n2' => 0.5],
            ['name' => 'INFUZIO MENTHE',            'mp' => $mentheTis, 'ratio' => 1.0,                              'caps' => 333,  'n2' => 0.5],
            ['name' => 'INFUZIO ROOIBOS',           'mp' => $rooibos,   'ratio' => 1.0,                              'caps' => 250,  'n2' => 0.5],
            ['name' => 'INFUZIO MATCHA',            'mp' => $matcha,    'ratio' => 1.0,                              'caps' => 250,  'n2' => 0.5],
            ['name' => 'INFUZIO GINGEMBRE CITRON',  'mp' => $gingembre, 'ratio' => 0.75,                             'caps' => 250,  'n2' => 0.5],
            ['name' => 'INFUZIO CAMOMILLE',         'mp' => $camomille, 'ratio' => 1.0,                              'caps' => 286,  'n2' => 0.5],
        ];

        foreach ($tisanes as $data) {
            $recipe = Recipe::where('name', $data['name'])->first();
            if (!$recipe) continue;
            RecipeIngredient::where('recipe_id', $recipe->id)->delete();
            $o = 1;
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $data['mp']->id, 'quantity' => $data['ratio'], 'unit' => 'kg', 'order' => $o++]);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $capsules->id, 'quantity' => $data['caps'], 'unit' => 'piece', 'order' => $o++]);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $n2->id, 'quantity' => $data['n2'], 'unit' => 'l', 'order' => $o]);
        }

        // INFUZIO DIGESTIF — mélange 4 plantes par kg input total
        $recipe = Recipe::where('name', 'INFUZIO DIGESTIF')->first();
        if ($recipe) {
            RecipeIngredient::where('recipe_id', $recipe->id)->delete();
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $fenouil->id,   'quantity' => 0.375, 'unit' => 'kg', 'order' => 1]); // 37.5%
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $anis->id,      'quantity' => 0.25,  'unit' => 'kg', 'order' => 2]); // 25%
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $mentheTis->id, 'quantity' => 0.2,   'unit' => 'kg', 'order' => 3]); // 20%
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $camomille->id, 'quantity' => 0.175, 'unit' => 'kg', 'order' => 4]); // 17.5%
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $capsules->id,  'quantity' => 250,   'unit' => 'piece', 'order' => 5]);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $n2->id,        'quantity' => 0.5,   'unit' => 'l',     'order' => 6]);
        }

        // INFUZIO GINGEMBRE CITRON — 75% gingembre + 25% citron
        $recipe = Recipe::where('name', 'INFUZIO GINGEMBRE CITRON')->first();
        if ($recipe) {
            RecipeIngredient::where('recipe_id', $recipe->id)->delete();
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $gingembre->id, 'quantity' => 0.75, 'unit' => 'kg', 'order' => 1]);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $citron->id,    'quantity' => 0.25, 'unit' => 'kg', 'order' => 2]);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $capsules->id,  'quantity' => 250,  'unit' => 'piece', 'order' => 3]);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $n2->id,        'quantity' => 0.5,  'unit' => 'l',     'order' => 4]);
        }

        // ════════════════════════════════════════════════════════════
        // EPICO — Ratios par kg d'épice(s) input
        // 1 kg épice → 20 doypacks 50g ou 10 doypacks 100g
        // ════════════════════════════════════════════════════════════

        // Épices simples
        $epicesSimples = [
            ['name' => 'EPICO CUMIN',      'mp' => $cumin],
            ['name' => 'EPICO PAPRIKA',    'mp' => $piment],
            ['name' => 'EPICO PIMENT',     'mp' => $piment],
            ['name' => 'EPICO CURCUMA',    'mp' => $curcuma],
            ['name' => 'EPICO CORIANDRE',  'mp' => $coriandre],
        ];

        foreach ($epicesSimples as $data) {
            $recipe = Recipe::where('name', $data['name'])->first();
            if (!$recipe) continue;
            RecipeIngredient::where('recipe_id', $recipe->id)->delete();
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $data['mp']->id,  'quantity' => 1.0,  'unit' => 'kg',    'order' => 1]);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $doypack50->id,   'quantity' => 20,   'unit' => 'piece', 'order' => 2]);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $doypack100->id,  'quantity' => 10,   'unit' => 'piece', 'order' => 3]);
        }

        // Ras el Hanout — par kg de mélange total
        $recipe = Recipe::where('name', 'EPICO RAS EL HANOUT')->first();
        if ($recipe) {
            RecipeIngredient::where('recipe_id', $recipe->id)->delete();
            $ingredients = [
                [$cumin,        0.25, 1],
                [$coriandre,    0.20, 2],
                [$cannelleEpi,  0.10, 3],
                [$gingEmbreEpi, 0.10, 4],
                [$piment,       0.10, 5],
                [$curcuma,      0.08, 6],
                [$poivre,       0.07, 7],
                [$cardamomeEpi, 0.05, 8],
                [$muscade,      0.03, 9],
                [$girofle,      0.02, 10],
            ];
            foreach ($ingredients as [$mp, $ratio, $order]) {
                RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $mp->id, 'quantity' => $ratio, 'unit' => 'kg', 'order' => $order]);
            }
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $doypack50->id,  'quantity' => 20, 'unit' => 'piece', 'order' => 11]);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $doypack100->id, 'quantity' => 10, 'unit' => 'piece', 'order' => 12]);
        }

        // BBQ Algérien
        $recipe = Recipe::where('name', 'EPICO BBQ ALGERIEN')->first();
        if ($recipe) {
            RecipeIngredient::where('recipe_id', $recipe->id)->delete();
            $ingredients = [
                [$piment,   0.30, 1],
                [$cumin,    0.20, 2],
                [$ail,      0.15, 3],
                [$oignon,   0.10, 4],
                [$poivre,   0.08, 5],
                [$piment,   0.07, 6],
            ];
            foreach ($ingredients as [$mp, $ratio, $order]) {
                RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $mp->id, 'quantity' => $ratio, 'unit' => 'kg', 'order' => $order]);
            }
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $doypack50->id,  'quantity' => 20, 'unit' => 'piece', 'order' => 7]);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $doypack100->id, 'quantity' => 10, 'unit' => 'piece', 'order' => 8]);
        }

        // Tandoori
        $recipe = Recipe::where('name', 'EPICO TANDOORI')->first();
        if ($recipe) {
            RecipeIngredient::where('recipe_id', $recipe->id)->delete();
            $ingredients = [
                [$piment,       0.25, 1],
                [$cumin,        0.15, 2],
                [$coriandre,    0.15, 3],
                [$curcuma,      0.10, 4],
                [$gingEmbreEpi, 0.10, 5],
                [$ail,          0.08, 6],
                [$piment,       0.07, 7],
                [$cardamomeEpi, 0.05, 8],
                [$cannelleEpi,  0.03, 9],
                [$poivre,       0.02, 10],
            ];
            foreach ($ingredients as [$mp, $ratio, $order]) {
                RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $mp->id, 'quantity' => $ratio, 'unit' => 'kg', 'order' => $order]);
            }
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $doypack50->id,  'quantity' => 20, 'unit' => 'piece', 'order' => 11]);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $doypack100->id, 'quantity' => 10, 'unit' => 'piece', 'order' => 12]);
        }

        // ════════════════════════════════════════════════════════════
        // FRUITAYA — déjà correct (ratio 1:1 par kg de fruit frais)
        // Juste corriger les sachets
        // 1 kg fruit → après perte → x sachets 100g
        // Ex banane: 1kg - 70% perte = 300g → 3 sachets 100g
        // ════════════════════════════════════════════════════════════

        $fruits = [
            ['name' => 'FRUITAYA BANANE',  'sku' => 'MP-FRT-BANANE',  'loss' => 70],
            ['name' => 'FRUITAYA FRAISE',  'sku' => 'MP-FRT-FRAISE',  'loss' => 85],
            ['name' => 'FRUITAYA MANGUE',  'sku' => 'MP-FRT-MANGUE',  'loss' => 75],
            ['name' => 'FRUITAYA POMME',   'sku' => 'MP-FRT-POMME',   'loss' => 75],
            ['name' => 'FRUITAYA ABRICOT', 'sku' => 'MP-FRT-ABRIC',   'loss' => 75],
            ['name' => 'FRUITAYA FIGUE',   'sku' => 'MP-FRT-FIGUE',   'loss' => 70],
            ['name' => 'FRUITAYA CERISE',  'sku' => 'MP-FRT-CERISE',  'loss' => 75],
            ['name' => 'FRUITAYA KIWI',    'sku' => 'MP-FRT-KIWI',    'loss' => 80],
            ['name' => 'FRUITAYA ANANAS',  'sku' => 'MP-FRT-ANANAS',  'loss' => 80],
        ];

        foreach ($fruits as $data) {
            $recipe = Recipe::where('name', $data['name'])->first();
            if (!$recipe) continue;
            $mp = RawMaterial::where('sku', $data['sku'])->first();
            if (!$mp) continue;

            // Sachets par kg input = (1000g × (1 - perte%)) ÷ 100g
            $sachetsParKg = (1000 * (1 - $data['loss'] / 100)) / 100;

            RecipeIngredient::where('recipe_id', $recipe->id)->delete();
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $mp->id,     'quantity' => 1.0,            'unit' => 'kg',    'order' => 1]);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'raw_material_id' => $sachet->id, 'quantity' => $sachetsParKg,  'unit' => 'piece', 'order' => 2]);
        }

        $this->command->info('✅ Ratios recettes corrigés !');
        $this->command->info('CAFIZIO: ratios par kg input (café + arôme + capsules + N2)');
        $this->command->info('INFUZIO: ratios par kg de plante input');
        $this->command->info('EPICO: ratios par kg d\'épice input');
        $this->command->info('FRUITAYA: sachets calculés selon % perte');
    }
}