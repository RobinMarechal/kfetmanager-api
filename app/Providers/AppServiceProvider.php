<?php

namespace App\Providers;

use App\CashFlow;
use App\Observers\CashFlowObserver;
use App\Observers\OrderObserver;
use App\Observers\OrderProductObserver;
use App\Observers\RestockingObserver;
use App\Order;
use App\OrderProduct;
use App\Restocking;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Order::observe(OrderObserver::class);
        Restocking::observe(RestockingObserver::class);
        CashFlow::observe(CashFlowObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
