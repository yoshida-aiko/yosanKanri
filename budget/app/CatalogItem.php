<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogItem extends Model
{
    use Sortable;
    public $sortable =['ItemClass','AmountUnit','ItemNameJp','ItemNameEn','Standard','CatalogCode','MakerNameJp','UnitPrice'];

    //makers hasOne
    public function maker() {
        return $this->hasOne('App\Maker','id','MakerId');
    }
    //論理削除
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
