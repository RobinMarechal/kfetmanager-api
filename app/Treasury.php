<?php
namespace App;

use Illuminate\Database\Eloquent\Model;


class Treasury extends Model
{
    protected $table = 'treasury';
	protected $fillable = ['movement_type', 'movement_id', 'movement_operation', 'balance'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

}