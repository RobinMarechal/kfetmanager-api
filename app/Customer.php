<?php
namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string email
 * @property string name
 * @property float balance
 * @property string year
 * @property string department
 * @property Collection groups
 * @property Collection orders
 */
class Customer extends BaseModel
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