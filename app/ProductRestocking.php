<?php

namespace App;

class ProductRestocking extends BaseModel
{
    public $timestamps = false;

    protected $fillable = ['quantity'];


    public function product()
    {
        return $this->belongsTo('App\Product');
    }


    public function restocking()
    {
        return $this->belongsTo('App\Restocking');
    }
}
