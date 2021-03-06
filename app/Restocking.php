<?php

namespace App;

use App\Observers\RestockingObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Restocking
 * @property string     comment
 * @property float      total_cost
 * @property int        id
 * @property Carbon     created_at
 * @property Carbon     updated_at
 * @property Carbon     deleted_at
 * @property Collection products
 * @property Treasury   treasury
 * @package App
 */
class Restocking extends BaseModel implements TreasuryUpdater
{
    use SoftDeletes;

    protected $fillable = ['comment', 'total_cost'];


    public function products()
    {
        return $this->belongsToMany('App\Product')->withPivot(['quantity']);
    }


    public function treasury()
    {
        return $this->hasOne('App\Treasury', 'movement_id')
                    ->where('movement_type', 'RESTOCKING')
                    ->where('movement_operation', 'INSERT');
    }


    public function getId(): int
    {
        return $this->id;
    }


    public function getValue(): float
    {
        return $this->total_cost;
    }


    public function getRelativeValue(): float
    {
        // the kfet loses money
        return $this->total_cost * -1;
    }


    //---

    protected static function boot()
    {
        parent::boot();

        static::pivotAttaching(function ($model, $relationName, $pivotIds, $pivotIdsAttributes) {
            RestockingObserver::dispatchAttachment($model, $relationName, $pivotIds, $pivotIdsAttributes);
        });

        static::pivotDetaching(function ($model, $relationName, $pivotIds) {
            RestockingObserver::dispatchDetachment($model, $relationName, $pivotIds);
        });
    }
}