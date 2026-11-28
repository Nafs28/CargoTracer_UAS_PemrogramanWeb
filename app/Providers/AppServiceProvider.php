<?php

namespace App\Providers;

use App\Models\Shipment;
use App\Policies\ShipmentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Shipment::class, ShipmentPolicy::class);

        if ($this->app->environment('production') || request()->header('x-forwarded-proto') === 'https') {
            URL::forceScheme('https');
        }
    }
}
