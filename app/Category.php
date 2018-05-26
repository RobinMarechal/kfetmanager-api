<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Category extends BaseModel
{
	use SoftDeletes;
	protected $fillable = ['name'];
    public $timestamps = false;

	public function subcategories(){
		return $this->hasMany('App\Subcategory');
	}

	public function menus(){
		return $this->belongsToMany('App\Menu');
	}

	public function products(){
	    return $this->hasManyThrough('App\Product', 'App\Subcategory');
    }
}