<?php
namespace App;

use Illuminate\Database\Eloquent\Model;


class Treasury extends Model
{
    protected $table = 'treasury';
	public $fillables = ['movement_type', 'movement_id', 'movement_operation', 'balance'];

}