<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BulletinBoard extends Model
{
    //ユーザーマスタbelongTo
    public function user(){
        return $this->hasOne('App\User','id','RegistUserId');
    }

    //論理削除
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
