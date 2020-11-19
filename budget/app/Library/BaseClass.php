<?php


namespace App\Library;

use Auth;
use App;
use Illuminate\Support\Facades\DB;
use App\CatalogItem;
use App\Cart;
use App\Favorite;
use App\Item;
use App\Condition;
use Carbon\Carbon;

class BaseClass
{
    public static function getFavoriteTree($isShared) {

        /*お気に入りTreeを取得*/
        $favoriteUserId = Auth::id();
        $nodata = App::getLocale()=='en' ? 'Thre is no data.' : 'データがありません';
        $ParentFavorite = Favorite::where('ParentId','=', -1)
            ->where(function($ParentFavorite) use ($isShared,$favoriteUserId) {
                if ($isShared!='true'){//共用の場合ユーザーID指定なし
                    $ParentFavorite->where('UserId','=', $favoriteUserId);
                }
                if ($isShared!='true'){
                    $ParentFavorite->where('IsShared','<>',1);
                }
                else {
                    $ParentFavorite->where('IsShared','=',1);
                }
            })->orderBy('id','asc')->get();
        $jsonFavoriteTreeReagent = array();
        $jsonFavoriteTreeArticle = array();
        $parentTree = array();
        $childTree = array();
        $jtreeicon = 'jstree-folder';
        $jtreename = '';
        foreach($ParentFavorite as $parent) {

            if ($parent->FolderName !== NULL && $parent->FolderName !== '') {
                $jtreeicon = 'jstree-folder';
                $jtreename = str_replace(' ','',$parent->FolderName);
                $attr = '';
            }
            else{
                $jtreeicon = 'jstree-file';
                $itemnm = App::getLocale()=='en' ? $parent->item->ItemNameEn : $parent->item->ItemNameJp;
                $jtreename = str_replace(' ','',$itemnm);
                $tooltip = $itemnm;
                $tooltip .= '<br>'.__('screenwords.capacity').'：'.$parent->item->AmountUnit;
                $tooltip .= '<br>'.__('screenwords.standard').'：'.$parent->item->Standard;
                $tooltip .= '<br>'.__('screenwords.maker').'：'.(App::getLocale()=='en' ? $parent->item->MakerNameEn : $parent->item->MakerNameJp);
                $tooltip .= '<br>'.__('screenwords.catalogCode').'：'.$parent->item->CatalogCode;
                $attr = ['title'=>$tooltip];
            }
            
            $parentTree = array(
                'key'=>$parent->id,
                'text'=>$jtreename,
                'icon'=>$jtreeicon,
                'a_attr'=>$attr,
                'children'=>array()
            );

            if ($parent->FolderName !== NULL && $parent->FolderName !== '') {
            
                $FavoriteChild = Favorite::where('ParentId','=',$parent->id)->orderBy('id','asc')->get();
                foreach($FavoriteChild as $child) {
                    $itemnm = App::getLocale()=='en' ? $child->item->ItemNameEn : $child->item->ItemNameJp;
                    $makernm = App::getLocale()=='en' ? $child->item->MakerNameEn : $child->item->MakerNameJp;
                    
                    $tooltip = $itemnm;
                    $tooltip .= '<br>'.__('screenwords.capacity').'：'.$child->item->AmountUnit;
                    $tooltip .= '<br>'.__('screenwords.standard').'：'.$child->item->Standard;
                    $tooltip .= '<br>'.__('screenwords.maker').'：'.$makernm;
                    $tooltip .= '<br>'.__('screenwords.catalogCode').'：'.$child->item->CatalogCode;
                    $attr = ['title'=>$tooltip];

                    $childTree = array(
                        'key'=>$child->id,
                        'text'=>$itemnm,
                        'icon'=>'jstree-file',
                        'a_attr'=>$attr
                    );
                    array_push($parentTree['children'],$childTree);
                    $childTree = array();
                }
            }
            if ($parent->ItemClass == 1) {
                array_push($jsonFavoriteTreeReagent,$parentTree);
            }
            elseif($parent->ItemClass == 2){
                array_push($jsonFavoriteTreeArticle,$parentTree);
            }
            $parentTree = array();
        }
        
        if (!is_array($jsonFavoriteTreeReagent) || count($jsonFavoriteTreeReagent) <= 0){
            $parentTree = array(
                'key'=>-100,
                'text'=>$nodata,
                'icon'=>'jstree-file',
                'children'=>array()
            );
            array_push($jsonFavoriteTreeReagent,$parentTree);
        }

        if (!is_array($jsonFavoriteTreeArticle) || count($jsonFavoriteTreeArticle) <= 0){
            $parentTree = array(
                'key'=>-100,
                'text'=>$nodata,
                'icon'=>'jstree-file',
                'children'=>array()
            );
            array_push($jsonFavoriteTreeArticle,$parentTree);
        }
        $jsonFavoriteTreeReagent = json_encode($jsonFavoriteTreeReagent, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
        $jsonFavoriteTreeReagent = str_replace('¥u0022', '¥¥¥"', $jsonFavoriteTreeReagent);
        $jsonFavoriteTreeArticle = json_encode($jsonFavoriteTreeArticle, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);        
        $jsonFavoriteTreeArticle = str_replace('¥u0022', '¥¥¥"', $jsonFavoriteTreeArticle);

        return [$jsonFavoriteTreeReagent,$jsonFavoriteTreeArticle];
    }

    /*カート追加*/
    public static function cartAdd($id) {

        $where = [
            'CatalogItemId' => $id,
            'UserId' => Auth::id()];
       
        $Cart = Cart::where($where)->first();

        if($Cart == NULL){
            //登録
            $CatalogItem = CatalogItem::findOrFail($id);

            $Cart = new Cart();
            $Cart->UserId = Auth::id();
            $Cart->CatalogItemId = $id;
            $Cart->ItemClass = $CatalogItem->ItemClass;
            $Cart->MakerId = $CatalogItem->MakerId;
            $Cart->CatalogCode = $CatalogItem->CatalogCode;
            $Cart->MakerNameJp = $CatalogItem->maker->MakerNameJp;
            $Cart->MakerNameEn = $CatalogItem->maker->MakerNameEn;
            $Cart->ItemNameJp = $CatalogItem->ItemNameJp;
            $Cart->ItemNameEn = $CatalogItem->ItemNameEn;
            $Cart->AmountUnit = $CatalogItem->AmountUnit;
            $Cart->Standard = $CatalogItem->Standard;
            $Cart->CASNo = $CatalogItem->CASNo;
            $Cart->UnitPrice = $CatalogItem->UnitPrice;
            $Cart->Remark1 = $CatalogItem->Remark1;
            $Cart->Remark2 = $CatalogItem->Remark2;
            $Cart->OrderRequestNumber = 1;
            $Cart->SupplierId = $CatalogItem->maker->supplier->id;
        }
        else {
            //更新カウントアップ
            $Cart->OrderRequestNumber = $Cart->OrderRequestNumber + 1;
        }

        $Cart->save();

    }    

    /*お気に入り追加*/
    public static function favoriteAdd($id,$favoriteUserId,$isShared) {

        $CatalogItem = CatalogItem::findOrFail($id);

        $where = [
            'MakerId'       => $CatalogItem->MakerId,
            'CatalogCode'   => $CatalogItem->CatalogCode,
            'ItemNameJp'    => $CatalogItem->ItemNameJp,
            'AmountUnit'    => $CatalogItem->AmountUnit,
            'Standard'      => $CatalogItem->Standard
        ];
        $Item = Item::where($where)->first();
        if($Item == NULL){
            /*Items登録*/
            $Item = new Item();
            $Item->ItemClass = $CatalogItem->ItemClass;
            $Item->MakerId = $CatalogItem->MakerId;
            $Item->MakerNameJp = $CatalogItem->maker->MakerNameJp;
            $Item->MakerNameEn = $CatalogItem->maker->MakerNameEn;
            $Item->CatalogCode = $CatalogItem->CatalogCode;
            $Item->ItemNameJp = $CatalogItem->ItemNameJp;
            $Item->ItemNameEn = $CatalogItem->ItemNameEn;
            $Item->AmountUnit = $CatalogItem->AmountUnit;
            $Item->Standard = $CatalogItem->Standard;
            $Item->CASNo = $CatalogItem->CASNo;
            $Item->UnitPrice = $CatalogItem->UnitPrice;
            $Item->save();
        }       

        /*Favorites登録*/
        $where = [
            'UserId'    => $favoriteUserId,
            'ParentId'  => -1,
            'ItemClass' => $CatalogItem->ItemClass,
            'ItemId'    => $Item->id
        ];
        if ($isShared) {
            $where = [
                'IsShared'  => 1,
                'ParentId'  => -1,
                'ItemClass' => $CatalogItem->ItemClass,
                'ItemId'    => $Item->id
            ];
        }
        $Favorites = Favorite::where($where)->get();

        if ($Favorites->count() < 1) {
            $Favorite = new Favorite();
            $Favorite->UserId = $favoriteUserId;
            $Favorite->ItemId = $Item->id;/*追加したデータのidを取得*/
            $Favorite->ItemClass = $CatalogItem->ItemClass;
            if ($isShared) {
                $Favorite->IsShared = 1;
            }
            $Favorite->save();
        }
    }

    /*フォルダー追加*/
    public static function folderAdd($ItemClass,$FolderName,$IsShared) {

        $Favorite = new Favorite();
        $Favorite->UserId = Auth::id();
        $Favorite->ParentId = -1;
        $Favorite->ItemId = 0;
        $Favorite->ItemClass = $ItemClass;
        $Favorite->FolderName = $FolderName;
        if ($IsShared=='true') {
            $Favorite->IsShared = 1;
        }
        $Favorite->save();

    }


    /*本日日付取得*/
    public static function getToday_ymd(){
        $today = Carbon::today();
        return $today->format('Y/m/d');
    }

    /*年度開始日取得*/
    public static function getKishuYMD(){
        $today = Carbon::today();
        $Condition = Condition::first();
        $FiscalStartMonth = 4;
        if ($Condition->FiscalStartMonth != null){
            $FiscalStartMonth = $Condition->FiscalStartMonth;
        }
        $strMonth = sprintf('%02d', $FiscalStartMonth);
        if($today->month < $FiscalStartMonth){
            $today->subYear();
        }
        return $today->year.'/'.$strMonth.'/01';
    }

    public static function setMailConfig($ref){
        $config = array(
            'transport' => 'smtp',
            'host' => $ref['host'],
            'port' => $ref['port'],
            'encryption' => $ref['encryption'],
            'username' => $ref['username'],
            'password' => $ref['password'],
            'timeout' => null,
            'auth_mode' => null,
        );
        Config::set('mail.mailers.smtp',$config);       
    }

}