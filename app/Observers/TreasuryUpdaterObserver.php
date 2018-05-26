<?php

namespace App\Observers;

use App\CashFlow;
use App\Order;
use App\Restocking;
use App\Treasury;
use App\TreasuryUpdater;

class TreasuryUpdaterObserver
{

    public function created(TreasuryUpdater $instance)
    {
        $operation = Treasury::MOVEMENT_OPERATION_INSERT;
        $value = $instance->getRelativeValue();
        $this->insertTreasuryRow($operation, $value, $instance);
    }


    public function deleted(TreasuryUpdater $instance)
    {
        $operation = Treasury::MOVEMENT_OPERATION_INSERT;
        $value = $instance->getRelativeValue();
        // Cancel an update by adding (resp. deducted) the same amount that has been deducted (resp. added)
        $this->insertTreasuryRow($operation, -$value, $instance);
    }


    public function updating(TreasuryUpdater $instance)
    {
        $operation = Treasury::MOVEMENT_OPERATION_INSERT;

        $class = get_class($instance);

        /** @var TreasuryUpdater $oldInstance */
        $oldInstance = $class::find($instance->getId());

        $oldValue = $oldInstance->getRelativeValue();
        $newValue = $instance->getRelativeValue();

        // Updating is restoring the balance's state then incrementing/reducing it by the new value
        $this->insertTreasuryRow($operation, -$oldValue + $newValue, $instance);
    }


    // ----

    private function insertTreasuryRow($operation, $toAdd, $instance)
    {
        $type = $this->guessMovementType($instance);
        $newBalance = Treasury::currentBalance() + $toAdd;
        
        Treasury::create([
            'movement_operation' => $operation,
            'movement_type' => $type,
            'movement_id' => $instance->getId(),
            'balance' => $newBalance,
        ]);
    }


    private function guessMovementType(TreasuryUpdater $instance): ?string
    {
        if ($instance instanceof Order) {
            return Treasury::MOVEMENT_TYPE_ORDER;
        }
        else if ($instance instanceof Restocking) {
            return Treasury::MOVEMENT_TYPE_RESTOCKING;
        }
        else if ($instance instanceof CashFlow) {
            return Treasury::MOVEMENT_TYPE_CASH_FLOW;
        }

        throw new \Exception("Error while trying to guess the movement type to create the treasury.");
    }
}