<?php

namespace App\Observers;

use App\Customer;
use App\Order;
use App\Product;
use App\TreasuryUpdater;

class OrderObserver extends TreasuryUpdaterObserver
{
    public function created(TreasuryUpdater $instance)
    {
        parent::created($instance);

        /** @var Order $instance */
        $instance = $instance;
        $this->updateCustomersBalance($instance->customer, $instance->final_price);
    }


    public function deleted(TreasuryUpdater $instance)
    {
        parent::deleted($instance);

        /** @var Order $instance */
        $instance = $instance;
        $this->updateCustomersBalance($instance->customer, -$instance->final_price);
        $this->updateProductsStocks($instance, -1);
    }


    public function updating(TreasuryUpdater $instance)
    {
        parent::updating($instance);

        /** @var Order $instance */
        $instance = $instance;

        $old = Order::with('customer')->find($instance->id);
        $oldCustomer = $old->customer;
        $newCustomer = $instance->customer;

        $this->updateCustomersBalance($oldCustomer, -$old->final_price);
        $this->updateCustomersBalance($newCustomer, $instance->final_price);
    }


    // ----

    private function updateCustomersBalance(?Customer $customer, $toDeduct)
    {
        if(!$customer){
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
}