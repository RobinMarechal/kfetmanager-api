<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
	protected $fillable = ['email', 'name', 'balance', 'year', 'department'];
	public $timestamps = false;

	public function groups(){
		return $this->belongsToMany('App\Group');
	}


	public function orders(){
		return $this->hasMany('App\Order');
	}
}