<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CatalogItem;
use App\Maker;
use App\Cart;
use App\Favorite;
use App\Item;
use Auth;
use Illuminate\Support\Facades\Request as PostRequest;

class SearchPageController extends Controller
{
    //
    public function index(Request $request)
    {
        /*初期表示かどうか*/
        $isFirst = false;

        /*メーカーマスタを取得*/
        $Makers = Maker::where('Status','=', 1)->get();

        $searchReagentName = '';
        $searchStandard = '';
        $searchCasNo = '';
        $searchCatalogCode = '';
        $makerCheckbox = '';

        $searchFormTab = $request->input('submit_key');
        $searchReagentNameR = $request->input('searchReagentNameR');
        $searchStandardR = $request->input('searchStandardR');
        $searchCasNoR = $request->input('searchCasNoR');
        $searchCatalogCodeR = $request->input('searchCatalogCodeR');
        $makerCheckboxR = $request->input('makerCheckboxR');
        $searchReagentNameA = $request->input('searchReagentNameA');
        $searchStandardA = $request->input('searchStandardA');
        $searchCatalogCodeA = $request->input('searchCatalogCodeA');
        $makerCheckboxA = $request->input('makerCheckboxA');

        /*試薬の場合*/
        if ($searchFormTab == '1') {
            $searchReagentName = $searchReagentNameR;
            $searchStandard = $searchStandardR;
            $searchCasNo = $searchCasNoR;
            $searchCatalogCode = $searchCatalogCodeR;
            $makerCheckbox = $makerCheckboxR;
        }
        /*物品の場合*/
        elseif ($searchFormTab == '2'){
            $searchReagentName = $searchReagentNameA;
            $searchStandard = $searchStandardA;
            $searchCasNo = "";
            $searchCatalogCode = $searchCatalogCodeA;
            $makerCheckbox = $makerCheckboxA;          
        }
        else {
            if($request->session()->has('searchPageCatalogItemWhere_searchFormTab')) {
                $searchFormTab = $request->session()->get('searchPageCatalogItemWhere_searchFormTab');
                $searchReagentName = $request->session()->get('searchPageCatalogItemWhere_searchReagentName');
                $searchStandard = $request->session()->get('searchPageCatalogItemWhere_searchStandard');
                $searchCasNo = $request->session()->get('searchPageCatalogItemWhere_searchCasNo');
                $searchCatalogCode = $request->session()->get('searchPageCatalogItemWhere_searchCatalogCode');
                $makerCheckbox = $request->session()->get('searchPageCatalogItemWhere_makerCheckbox');
                if ($searchFormTab == '1') {
                    $searchReagentNameR = $searchReagentName;
                    $searchStandardR = $searchStandard;
                    $searchCasNoR = $searchCasNo;
                    $searchCatalogCodeR = $searchCatalogCode;
                    $makerCheckboxR = $makerCheckbox;
                }
                elseif ($searchFormTab == '2'){
                    $searchReagentNameA = $searchReagentName;
                    $searchStandardA = $searchStandard;
                    $searchCasNoA = $searchCasNo;
                    $searchCatalogCodeA = $searchCatalogCode;
                    $makerCheckboxA = $makerCheckbox;
                }
            }
            else{
                $isFirst = true;
            }      
        }

        if (!$isFirst){
            $query = CatalogItem::where(function ($query) use ($searchFormTab) {
                //ItemClass
                if (!empty($searchFormTab)) {
                    $query->where('ItemClass','=', $searchFormTab);
                }
            })->where(function ($query) use ($searchReagentName) {
                //試薬名
                if (!empty($searchReagentName)) {
                    $query->orWhere('ItemNameJp','like', '%'.$searchReagentName.'%')
                        ->orWhere('ItemNameEn','like', '%'.$searchReagentName.'%');
                }
            })->where(function ($query) use ($searchStandard) {
                //規格
                if (!empty($searchStandard)) {
                    $query->where('Standard','like', '%'.$searchStandard.'%');
                }
            })->where(function ($query) use ($searchCasNo) {
                //CASNO
                if (!empty($searchCasNo)) {
                    $query->where('CASNo','like', '%'.$searchCasNo.'%');
                }
            })->where(function ($query) use ($searchCatalogCode) {
                //カタログコード
                if (!empty($searchCatalogCode)) {
                    $query->where('CatalogCode','like', '%'.$searchCatalogCode.'%');
                }
            })->where(function ($query) use ($makerCheckbox) {
                //メーカー
                if (!empty($makerCheckbox)) {
                    $query->whereIn('MakerId',$makerCheckbox);
                }
            });

            /*セッションに条件を保存*/
            $request->session()->put('searchPageCatalogItemWhere_searchFormTab',$searchFormTab);
            $request->session()->put('searchPageCatalogItemWhere_searchReagentName',$searchReagentName);
            $request->session()->put('searchPageCatalogItemWhere_searchStandard',$searchStandard);
            $request->session()->put('searchPageCatalogItemWhere_searchCasNo',$searchCasNo);
            $request->session()->put('searchPageCatalogItemWhere_searchCatalogCode',$searchCatalogCode);
            $request->session()->put('searchPageCatalogItemWhere_makerCheckbox',$makerCheckbox);

            $CatalogItems = $query->sortable()->paginate(25);;
        }
        else {
            $CatalogItems = collect(new CatalogItem());
        }
        /*発注依頼　カートの中身を取得*/
        /*Cartを取得*/
        $Carts = Cart::where('UserId','=', Auth::id())->orderBy('id','asc')->get();

        /*お気に入りを取得*/
        /*Favoritesを取得*/
        $Favorites = Favorite::where('UserId','=', Auth::id())->orderBy('id','asc')->get();

        return view('SearchPage/index',compact('CatalogItems','Makers','Carts','Favorites','searchFormTab','searchReagentNameR','searchStandardR','searchCasNoR','searchCatalogCodeR','makerCheckboxR','searchReagentNameA','searchStandardA','searchCatalogCodeA','makerCheckboxA'));
    }

    /*発注依頼カート、お気に入りに追加*/
    public function update($id) {
        $submitkey = PostRequest::input('cartFavorite_submit_key');
        if ($submitkey == 'btnCart') {
            $this->cartAdd($id);
        }
        elseif($submitkey == 'btnFavorite'){
            $this->favoriteAdd($id);
        }
        return redirect()->route('SearchPage.index');
    }

    public function cartAdd($id) {

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

    public function favoriteAdd($id) {

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
        $Favorite = new Favorite();
        $Favorite->UserId = Auth::id();
        $Favorite->ItemId = $Item->id;/*追加したデータのidを取得*/
        $Favorite->ItemClass = $CatalogItem->ItemClass;
        $Favorite->save();
    }

    /*発注依頼　お気に入り削除*/
    public function destroy(Request $request,$id) {
        $delType = $request->deleteType;
        if ($delType == 'delCartReagent' || $delType == 'delCartArticle') {
            $Cart = Cart::findOrFail($id);
            $Cart->delete();                
        }
        elseif($delType == 'delFavoriteReagent' || $delType == 'delFavoriteArticle') {
            $Favorite = Favorite::findOrFail($id);
            $Favorite->delete();
        }
        return redirect()->route('SearchPage.index');
    }

}