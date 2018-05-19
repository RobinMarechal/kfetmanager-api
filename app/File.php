<?php
namespace App;

use Illuminate\Database\Eloquent\Model;


class File extends Model
{
    public $temporalField = 'from';
	public $dates = ['from', 'to'];
	protected $fillable = ['type', 'path', 'comment', 'from', 'to'];
    public $timestamps = false;

}