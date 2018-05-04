<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;
	public $fillables = ['name'];

	public function customers(){
		return $this->belongsToMany('App\Customer');
	}


	public function discounts(){
		return $this->hasMany('App\Discount');
	}


	public function subcategories(){
		return $this->belongsToMany('App\Subcategory')->withPivot(['value', 'percentage']);
	}
}