<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Condition extends Model
{
    //論理削除
    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
