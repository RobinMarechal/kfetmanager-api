<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CashFlow extends Model
{
	use SoftDeletes;
	public $fillables = ['amount', 'description'];

	public function treasury(){
		return $this->hasOne('App\Treasury');
	}
}