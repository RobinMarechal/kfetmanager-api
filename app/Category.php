<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Category extends Model
{
	use SoftDeletes;
	public $fillables = ['name'];

	public function subcategories(){
		return $this->hasMany('App\Subcategory');
	}


	public function menus(){
		return $this->belongsToMany('App\Menu');
	}
}