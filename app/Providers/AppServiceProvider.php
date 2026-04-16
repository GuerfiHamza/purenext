<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
        'raw_material'   => \App\Models\RawMaterial::class,
        'production_run' => \App\Models\ProductionRun::class,
        'finished_good'  => \App\Models\FinishedGood::class,
        'supplier'       => \App\Models\Supplier::class,
    ]);
    }
}
