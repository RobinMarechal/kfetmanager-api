<?php

namespace App\Observers;

use App\Product;
use App\Restocking;

class ProductObserver
{
    public static function dispatchAttachment(Product $product, $relationName, $pivotIds, $pivotIdsAttributes)
    {
        if ($relationName === 'orders') {
            static::attachingToOrder($product, $pivotIdsAttributes);
        }
        else if($relationName === 'restockings'){
            static::attachingToRestocking($product, $pivotIdsAttributes);
        }
    }


    public static function dispatchDetachment(Product $model, $relationName, $pivotIds)
    {
        if ($relationName === 'orders') {
            static::detachingFromOrder($model, $pivotIds);
        }
        else if($relationName === 'restockings'){
            static::detachingRestocking($model, $pivotIds);
        }
    }


    public static function attachingToOrder(Product $product, $pivotIdsAttributes)
    {
        $product->stock -= count($pivotIdsAttributes);
        $product->save();
    }


    public static function detachingFromOrder(Product $product, $pivotIdsAttributes)
    {
        $product->stock += count($pivotIdsAttributes);
        $product->save();
    }


    public static function attachingToRestocking(Product $product, $pivotIdsAttributes)
    {
        $restockings = Restocking::whereIn('id', array_keys($pivotIdsAttributes))->get();

        foreach ($restockings as $r) {
            $quantity = $pivotIdsAttributes[$r->id]['quantity'];
            $product->stock += $quantity;
        }

        $product->save();
    }


    public static function detachingRestocking(Product $product, $pivotIds)
    {
        $product->load(['restockings' => function($query) use ($pivotIds){
            $query->whereIn('id', $pivotIds);
        }]);

        foreach ($product->restockings as $r) {
            $quantity = $r->pivot->quantity;
            $product->stock -= $quantity;
        }

        $product->save();
    }
}