<?php

namespace App\Observers;

use App\Product;
use App\Restocking;
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


    public static function dispatchAttachment(Restocking $product, $relationName, $pivotIds, $pivotIdsAttributes)
    {
        if ($relationName === 'products') {
            static::attachingToProduct($pivotIdsAttributes);
        }
    }


    public static function dispatchDetachment(Restocking $model, $relationName, $pivotIds)
    {
        if ($relationName === 'products') {
            static::detachingProduct($model, $pivotIds);
        }
    }

    public static function attachingToProduct($pivotIdsAttributes)
    {
        $products = Product::whereIn('id', array_keys($pivotIdsAttributes))->get();

        foreach ($products as $p) {
            $quantity = $pivotIdsAttributes[$p->id]['quantity'];
            $p->stock += $quantity;
            $p->save();
        }
    }


    public static function detachingProduct(Restocking $restocking, $pivotIds)
    {
        $restocking->load(['products' => function($query) use ($pivotIds){
            $query->whereIn('id', $pivotIds);
        }]);

        foreach ($restocking->products as $p) {
            $quantity = $p->pivot->quantity;
            $p->stock -= $quantity;
            $p->save();
        }
    }
}