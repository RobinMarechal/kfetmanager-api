<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Restocking extends Model
{
	use SoftDeletes;
	protected $fillable = ['comment', 'total_cost'];

	public function products(){
		return $this->belongsToMany('App\Product');
	}

    public function treasury(){
        return $this->hasOne('App\Treasury', 'movement_id')
                    ->where('movement_type', 'RESTOCKING')
                    ->orderBy('id', 'desc')
                    ->limit(1);
    }
}