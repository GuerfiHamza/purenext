<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\RecipePackaging;

class ProductionSimulatorService
{
    public function simulate(int $recipeId, int $packagingId, float $inputKg): array
    {
        $recipe    = Recipe::with('ingredients.rawMaterial')->findOrFail($recipeId);
        $packaging = RecipePackaging::findOrFail($packagingId);

        // 1. Calcul perte traitement
        $netKg = $inputKg * (1 - $recipe->loss_percentage / 100);

        // 2. Nombre de packets estimés
        $packetsEstimated = (int) floor(($netKg * 1000) / $packaging->packet_size_g);

        // 3. Consommation MP par ingrédient
        $ingredientsConsumption = $recipe->ingredients->map(function ($ingredient) use ($inputKg) {
            $consumed  = $ingredient->quantity * $inputKg;
            $available = $ingredient->rawMaterial->quantity_in_stock;

            return [
                'raw_material_id'    => $ingredient->raw_material_id,
                'name'               => $ingredient->rawMaterial->name,
                'unit'               => $ingredient->unit,
                'quantity_needed'    => round($consumed, 4),
                'quantity_available' => $available,
                'is_sufficient'      => $available >= $consumed,
            ];
        });

        // 4. Temps estimé en minutes
        $durationMinutes = null;
        if ($packaging->machine_capacity_per_hour > 0) {
            $durationMinutes = round(
                ($packetsEstimated / $packaging->machine_capacity_per_hour) * 60, 1
            );
        }

        // 5. Consommation film estimée en m²
        $filmM2 = null;
        if ($packaging->film_width_mm && $packaging->film_length_mm) {
            $filmM2 = round(
                $packetsEstimated
                * ($packaging->film_width_mm / 1000)
                * ($packaging->film_length_mm / 1000),
                2
            );
        }

        // 6. Alerte stock insuffisant
        $stockAlert = $ingredientsConsumption->contains('is_sufficient', false);

        return [
            'recipe'                  => $recipe->only(['id', 'name', 'version', 'loss_percentage']),
            'packaging'               => $packaging->only(['id', 'packet_size_g', 'packet_label']),
            'input_qty_kg'            => $inputKg,
            'net_qty_kg'              => round($netKg, 3),
            'packets_estimated'       => $packetsEstimated,
            'duration_minutes'        => $durationMinutes,
            'film_m2_estimated'       => $filmM2,
            'ingredients_consumption' => $ingredientsConsumption,
            'stock_alert'             => $stockAlert,
        ];
    }
}