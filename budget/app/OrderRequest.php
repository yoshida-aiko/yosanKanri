<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderRequest extends Model
{
    use Sortable;
    public $sortable =['ItemClass','OrderDate','RequestDate','ItemNameJp','AmountUnit','Standard','UserNameJp','CatalogCode','MakerNameJp','UnitPrice','RequestNumber','RequestProgress'];

    /*発注日+依頼日を結合してソート*/
    /*public function unionOrderRequestDateSortable($query, $direction) {
        return $query->orderBy('CONCAT(RequestDate,OrderDate) as RequestDate',$direction);
    }*/

    //suppliers hasOne
    public function supplier() {
        return $this->hasOne('App\Supplier','id','SupplierId');
    }

    //items hasOne
    public function item() {
        return $this->hasOne('App\Item','id','ItemId');
    }

    //users hasOne
    public function user() {
        return $this->hasOne('App\User','id','RequestUserId');
    }
    //論理削除
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
