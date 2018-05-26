<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property float      amount
 * @property int        id
 * @property Carbon     created_at
 * @property Carbon     updated_at
 * @property Carbon     deleted_at
 * @property Collection products
 * @property Treasury   treasury
 */
class CashFlow extends BaseModel implements TreasuryUpdater
{
    use SoftDeletes;

    protected $fillable = ['amount', 'description'];


    public function treasury()
    {
        return $this->hasOne('App\Treasury', 'movement_id')
                    ->where('movement_type', 'CASH_FLOW')
                    ->where('movement_operation', 'INSERT');
    }


    public function getId(): int
    {
        return $this->id;
    }


    public function getValue(): float
    {
        return $this->amount;
    }


    public function getRelativeValue(): float
    {
        // the kfet earn money (which can be negative in case of an expense)
        return $this->amount;
    }

}