<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maker extends Model
{
    //makers hasOne
    public function supplier() {
        return $this->belongsTo('App\Supplier','MainSupplierId','id');
    }
    
    //論理削除
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
