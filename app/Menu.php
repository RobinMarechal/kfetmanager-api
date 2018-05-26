<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Menu extends BaseModel
{
	use SoftDeletes;
	protected $fillable = ['name', 'price'];
    public $timestamps = false;

	public function categories(){
		return $this->belongsToMany('App\Category');
	}


	public function orders(){
		return $this->hasMany('App\Order');
	}
}