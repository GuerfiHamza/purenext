<?php

namespace App\Services;

use App\Models\FinishedGood;
use App\Models\RawMaterial;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;
class StockService
{
    public function adjustRawMaterial(
        RawMaterial $material,
        string $type,
        float $quantity,
        string $reason,
        ?int $userId = null,
        ?string $sourceType = null,
        ?int $sourceId = null
    ): RawMaterial {
        return DB::transaction(function () use ($material, $type, $quantity, $reason, $userId, $sourceType, $sourceId) {
            $stockBefore = $material->quantity_in_stock;

            $stockAfter = match($type) {
                'in'         => $stockBefore + $quantity,
                'out'        => max(0, $stockBefore - $quantity),
                'adjustment' => $quantity,
            };

            $material->update(['quantity_in_stock' => $stockAfter]);
            NotificationService::checkLowStock();
            StockMovement::create([
                'movable_type' => 'raw_material',
                'movable_id'   => $material->id,
                'type'         => $type,
                'quantity'     => $quantity,
                'unit'         => $material->unit,
                'stock_before' => $stockBefore,
                'stock_after'  => $stockAfter,
                'reason'       => $reason,
                'source_type'  => $sourceType,
                'source_id'    => $sourceId,
                'user_id'      => $userId ?? auth()->id(),
            ]);

            return $material->fresh();
        });
    }

    public function adjustFinishedGood(
        FinishedGood $good,
        string $type,
        int $quantity,
        string $reason,
        ?int $userId = null,
        ?string $sourceType = null,
        ?int $sourceId = null
    ): FinishedGood {
        return DB::transaction(function () use ($good, $type, $quantity, $reason, $userId, $sourceType, $sourceId) {
            $stockBefore = $good->quantity_in_stock;

            $stockAfter = match($type) {
                'in'         => $stockBefore + $quantity,
                'out'        => max(0, $stockBefore - $quantity),
                'adjustment' => $quantity,
            };

            $good->update(['quantity_in_stock' => $stockAfter]);
            NotificationService::checkLowStock();

            StockMovement::create([
                'movable_type' => 'finished_good',
                'movable_id'   => $good->id,
                'type'         => $type,
                'quantity'     => $quantity,
                'unit'         => 'piece',
                'stock_before' => $stockBefore,
                'stock_after'  => $stockAfter,
                'reason'       => $reason,
                'source_type'  => $sourceType,
                'source_id'    => $sourceId,
                'user_id'      => $userId ?? auth()->id(),
            ]);

            return $good->fresh();
        });
    }

    public function getLowStockAlerts(): array
    {
        $rawMaterials = RawMaterial::where('is_active', true)
            ->whereColumn('quantity_in_stock', '<=', 'min_stock_alert')
            ->with('supplier')
            ->get();

        $finishedGoods = FinishedGood::whereColumn('quantity_in_stock', '<=', 'min_stock_alert')
            ->with('brand')
            ->get();

        return [
            'raw_materials'  => $rawMaterials,
            'finished_goods' => $finishedGoods,
            'total'          => $rawMaterials->count() + $finishedGoods->count(),
        ];
    }
}