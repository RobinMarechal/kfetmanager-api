<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Treasury extends BaseModel
{
    const MOVEMENT_TYPE_ORDER = "ORDER";
    const MOVEMENT_TYPE_RESTOCKING = "RESTOCKING";
    const MOVEMENT_TYPE_CASH_FLOW = "CASH_FLOW";

    const MOVEMENT_OPERATION_INSERT = 'INSERT';
    const MOVEMENT_OPERATION_UPDATE = 'UPDATE';
    const MOVEMENT_OPERATION_DELETE = 'DELETE';

    protected $table = 'treasury';

    protected $fillable = ['movement_type', 'movement_id', 'movement_operation', 'balance'];


    public static function current()
    {
        return self::orderBy('id', 'desc')->select()->first();
    }


    public static function currentBalance()
    {
        return self::current()->balance;
    }
}