<?php

namespace App;

use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model{
    use PivotEventTrait;
}