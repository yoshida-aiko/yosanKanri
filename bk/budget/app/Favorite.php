<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Favorite extends Model
{
    //items hasOne
    public function item() {
        return $this->hasOne('App\Item','id','ItemId');
    }

    //users hasOne
    public function user() {
        return $this->hasOne('App\User','id','UserId');
    }

    //論理削除
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
