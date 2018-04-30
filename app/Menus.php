<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Menus extends Model
{
	use SoftDeletes;
	public $fillables = ['name', 'price'];

	public function categories(){
		return $this->belongsToMany('App\Category');
	}


	public function orders(){
		return $this->hasMany('App\Order');
	}
}