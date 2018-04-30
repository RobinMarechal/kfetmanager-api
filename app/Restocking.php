<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Restocking extends Model
{
	use SoftDeletes;
	public $fillables = ['comment', 'total_cost'];

	public function treasury(){
		return $this->hasOne('App\Treasury');
	}


	public function products(){
		return $this->belongsToMany('App\Product');
	}
}