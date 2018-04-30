<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Order extends Model
{
	use SoftDeletes;
	public $fillables = ['customer_id', 'menu_id', 'final_price'];

	public function customer(){
		return $this->belongsTo('App\Customer');
	}


	public function menu(){
		return $this->belongsTo('App\Menu');
	}


	public function products(){
		return $this->belongsToMany('App\Product')->withPivot(['quantity']);
	}
}