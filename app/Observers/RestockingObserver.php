<?php

namespace App\Observers;

use App\TreasuryUpdater;

class RestockingObserver extends TreasuryUpdaterObserver
{

    public function deleted(TreasuryUpdater $instance)
    {
        parent::deleted($instance);

        $products = $instance->products;
        foreach ($products as $p) {
            $quantity = $p->pivot->quantity;
            $p->quantity += $quantity;
        }
    }
}