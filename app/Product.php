<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property string name
 * @property int subcategory_id
 * @property float purchase_price
 * @property float price
 * @property int stock
 * @property Collection restockings
 * @property Collection orders
 * @property Subcategory subcategory
 */
class Product extends BaseModel
{
	use SoftDeletes;
	protected $fillable = ['name', 'subcategory_id', 'purchase_price', 'price', 'stock'];
    public $timestamps = false;

	public function restockings(){
		return $this->belongsToMany('App\Restocking')->withPivot(['quantity']);
	}


	public function orders(){
		return $this->belongsToMany('App\Order');
	}


	public function subcategory(){
		return $this->belongsTo('App\Subcategory');
	}


    //---

    protected static function boot()
    {
        parent::boot();

        static::pivotAttached(function ($model, $relationName, $pivotIds, $pivotIdsAttributes) {
            if ($relationName === 'orders') {
                $model->stock -= count($pivotIds);
                $model->save();
            }
        });

        static::pivotDetached(function ($model, $relationName, $pivotIds) {
            if ($relationName === 'orders') {
                $model->stock += count($pivotIds);
                $model->save();
            }
        });
    }
}