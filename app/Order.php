<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property float      final_price
 * @property int        customer_id
 * @property int        menu_id
 * @property int        id
 * @property Carbon     created_at
 * @property Carbon     updated_at
 * @property Carbon     deleted_at
 * @property Collection products
 * @property Treasury   treasury
 * @property Customer   customer
 */
class Order extends BaseModel implements TreasuryUpdater
{
    use SoftDeletes;

    protected $fillable = ['customer_id', 'menu_id', 'final_price'];


    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }


    public function menu()
    {
        return $this->belongsTo('App\Menu');
    }


    public function products()
    {
        return $this->belongsToMany('App\Product');
    }


    public function treasury()
    {
        return $this->hasOne('App\Treasury', 'movement_id')
                    ->where('movement_type', 'ORDER')
                    ->where('movement_operation', 'INSERT');
    }


    public function getId(): int
    {
        return $this->id;
    }


    public function getValue(): float
    {
        return $this->final_price;
    }


    public function getRelativeValue(): float
    {
        // the kfet earns money
        return $this->final_price;
    }


    //---
    protected static function boot()
    {
        parent::boot();

        static::pivotAttached(function ($model, $relationName, $pivotIds, $pivotIdsAttributes) {
            if(!is_null($model->deleted_at)){
                return ;
            }

            if ($relationName === 'products') {
                foreach ($pivotIds as $id) {
                    $p = Product::find($id);
                    $p->stock--;
                    $p->save();
                }
            }
        });

        static::pivotDetached(function ($model, $relationName, $pivotIds) {
            if(!is_null($model->deleted_at)){
                return ;
            }

            if ($relationName === 'products') {
                foreach ($pivotIds as $id) {
                    $p = Product::find($id);
                    $p->stock++;
                    $p->save();
                }
            }
        });
    }
}