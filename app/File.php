<?php
namespace App;

use Illuminate\Database\Eloquent\Model;


class File extends Model
{
    public $temporalField = 'from';
	public $dates = ['from', 'to'];
	public $fillables = ['type', 'path', 'comment', 'from', 'to'];

}