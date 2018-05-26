<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Subcategory extends BaseModel
{
	use SoftDeletes;
	protected $fillable = ['name', 'category_id'];
    public $timestamps = false;

	public function products(){
		return $this->hasMany('App\Product');
	}


	public function category(){
		return $this->belongsTo('App\Category');
	}


	public function discounts(){
		return $this->hasMany('App\Discount');
	}


	public function groups(){
		return $this->belongsToMany('App\Groups');
	}
}