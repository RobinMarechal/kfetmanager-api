<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CashFlow extends Model
{
	use SoftDeletes;
	protected $fillable = ['amount', 'description'];

    public function treasury(){
        return $this->hasOne('App\Treasury', 'movement_id')
                    ->where('movement_type', 'CASH_FLOW')
                    ->where('movement_operation', 'INSERT');
    }
}