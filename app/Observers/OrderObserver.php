<?php

namespace App\Observers;

use App\Customer;
use App\Order;
use App\Product;
use App\TreasuryUpdater;

class OrderObserver extends TreasuryUpdaterObserver
{

    /** @noinspection PhpDocSignatureInspection */
    /**
     * @param Order $instance
     */
    public function created(TreasuryUpdater $instance)
    {
        parent::created($instance);

        $this->updateCustomersBalance($instance->customer, $instance->final_price);
    }



    /** @noinspection PhpDocSignatureInspection */
    /**
     * @param Order $instance
     */
    public function deleted(TreasuryUpdater $instance)
    {
        parent::deleted($instance);

        $this->updateCustomersBalance($instance->customer, -$instance->final_price);
        $this->updateProductsStocks($instance, -1);
    }

    /** @noinspection PhpDocSignatureInspection */
    /**
     * @param Order $instance
     */
    public function updating(TreasuryUpdater $instance)
    {
        parent::updating($instance);

        $old = Order::with('customer')->find($instance->id);
        $oldCustomer = $old->customer;
        $newCustomer = $instance->customer;

        if ($oldCustomer !== $newCustomer || $old->final_price !== $instance->final_price) {
            $this->updateCustomersBalance($oldCustomer, -$old->final_price);
            $this->updateCustomersBalance($newCustomer, $instance->final_price);
        }
    }


    // ----

    private function updateCustomersBalance(?Customer $customer, $toDeduct)
    {
        if (!$customer) {
            return;
        }

        $customer->balance -= $toDeduct;
        $customer->save();
    }


    private function updateProductsStocks(Order $order, $toDeduct = 1)
    {
        $products = $order->products;

        /** @var Product $p */
        foreach ($products as $p) {
            $p->stock -= $toDeduct;
            $p->save();
        }
    }


    public static function dispatchAttachment(Order $order, $relationName, $pivotIds, $pivotIdsAttributes)
    {
        if ($relationName === 'products') {
            static::attachingToProduct($pivotIdsAttributes);
        }
    }


    public static function dispatchDetachment(Order $order, $relationName, $pivotIds)
    {
        if ($relationName === 'products') {
            static::detachingProduct($order, $pivotIds);
        }
    }


    public static function attachingToProduct($pivotIdsAttributes)
    {
        $products = Product::whereIn('id', array_keys($pivotIdsAttributes))->get();

        foreach ($products as $p) {
            $p->stock--;
            $p->save();
        }
    }


    public static function detachingProduct(Order $order, $pivotIds)
    {
        $order->load(['products' => function ($query) use ($pivotIds) {
            $query->whereIn('id', $pivotIds);
        }]);

        foreach ($order->products as $p) {
            $p->stock++;
            $p->save();
        }
    }
}