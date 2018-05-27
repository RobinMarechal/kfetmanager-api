<?php

namespace App;

use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property string      name
 * @property int         subcategory_id
 * @property float       purchase_price
 * @property float       price
 * @property int         stock
 * @property Collection  restockings
 * @property Collection  orders
 * @property Subcategory subcategory
 */
class Product extends BaseModel
{
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = ['name', 'subcategory_id', 'purchase_price', 'price', 'stock'];


    public function restockings()
    {
        return $this->belongsToMany('App\Restocking')->withPivot(['quantity']);
    }


    public function orders()
    {
        return $this->belongsToMany('App\Order');
    }


    public function subcategory()
    {
        return $this->belongsTo('App\Subcategory');
    }


    //---

    protected static function boot()
    {
        parent::boot();

        static::pivotAttaching(function ($model, $relationName, $pivotIds, $pivotIdsAttributes) {
            ProductObserver::dispatchAttachment($model, $relationName, $pivotIds, $pivotIdsAttributes);
        });

        static::pivotDetaching(function ($model, $relationName, $pivotIds) {
            ProductObserver::dispatchDetachment($model, $relationName, $pivotIds);
        });
    }
}