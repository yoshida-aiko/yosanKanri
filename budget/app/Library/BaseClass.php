<?php


namespace App\Library;

use Auth;
use App\CatalogItem;
use App\Cart;
use App\Favorite;
use App\Item;

class BaseClass
{
    public static function getFavoriteTree() {

        /*お気に入りTreeを取得*/
        $favoriteUserId =  Auth::id();
        $ParentFavorite = Favorite::where('UserId','=', $favoriteUserId)->where('ParentId','=', -1)->orderBy('id','asc')->get();

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
            }
            else{
                $jtreeicon = 'jstree-file';
                $jtreename = str_replace(' ','',$parent->item->ItemNameJp);
            }
            $parentTree = array(
                'key'=>$parent->id,
                'text'=>$jtreename,
                'icon'=>$jtreeicon,
                'children'=>array()
            );

            if ($parent->FolderName !== NULL && $parent->FolderName !== '') {
            
                $FavoriteChild = Favorite::where('ParentId','=',$parent->id)->orderBy('id','asc')->get();
                foreach($FavoriteChild as $child) {
                    $childTree = array(
                        'key'=>$child->id,
                        'text'=>$child->item->ItemNameJp,
                        'icon'=>'jstree-file'
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
        }
        else {
            //更新カウントアップ
            $Cart->OrderRequestNumber = $Cart->OrderRequestNumber + 1;
        }

        $Cart->save();

    }    

    /*お気に入り追加*/
    public static function favoriteAdd($id) {

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

        $favoriteUserId =  Auth::id();
        /*$chkShared = $request->input('submit_chkSharedKey');
        if (!empty($chkShared)) {
            if(in_array('共用',$chkShared,true)) {
                $favoriteUserId = -1;
            }
        }*/
        /*Favorites登録*/
        $where = [
            'UserId'    => $favoriteUserId,
            'ParentId'  => -1,
            'ItemClass' => $CatalogItem->ItemClass,
            'ItemId'    => $Item->id
        ];
        $Favorites = Favorite::where($where)->get();

        if ($Favorites->count() < 1) {
            $Favorite = new Favorite();
            $Favorite->UserId = $favoriteUserId;
            $Favorite->ItemId = $Item->id;/*追加したデータのidを取得*/
            $Favorite->ItemClass = $CatalogItem->ItemClass;
            $Favorite->save();
        }
    }

    /*フォルダー追加*/
    public static function folderAdd($ItemClass,$FolderName) {

        $Favorite = new Favorite();
        $Favorite->UserId = Auth::id();
        $Favorite->ParentId = -1;
        $Favorite->ItemId = 1;
        $Favorite->ItemClass = $ItemClass;
        $Favorite->FolderName = $FolderName;
        $Favorite->save();

    }
}