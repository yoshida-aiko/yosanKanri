<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    //ユーザーマスタhasOne
    public function user(){
        return $this->hasOne('App\User','id','UserId');
    }

    //論理削除
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
