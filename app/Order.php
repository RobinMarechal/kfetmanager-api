<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Order extends Model
{
    const MOVEMENT_TYPE_ORDER = "ORDER";
    const MOVEMENT_TYPE_RESTOCKING = "RESTOCKING";
    const MOVEMENT_TYPE_CASH_FLOW = "CASH_FLOW";

    const MOVEMENT_OPERATION_INSERT = 'INSERT';
    const MOVEMENT_OPERATION_UPDATE = 'UPDATE';
    const MOVEMENT_OPERATION_DELETE = 'DELETE';

	use SoftDeletes;
	protected $fillable = ['customer_id', 'menu_id', 'final_price'];

	public function customer(){
		return $this->belongsTo('App\Customer');
	}


	public function menu(){
		return $this->belongsTo('App\Menu');
	}


	public function products(){
		return $this->belongsToMany('App\Product');
	}

	public function treasury(){
	    return $this->hasOne('App\Treasury', 'movement_id')
                    ->where('movement_type', 'ORDER')
                    ->where('movement_operation', 'INSERT')

    }
}