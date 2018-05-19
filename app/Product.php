<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Product extends Model
{
	use SoftDeletes;
	protected $fillable = ['name', 'sucategory_id', 'purchase_price', 'price', 'stock'];
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
}