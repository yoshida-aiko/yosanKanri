<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Config;
use Response;
use App\Http\Controllers\Controller;
use App\Library\BaseClass;
use App\CatalogItem;
use App\OrderRequest;
use App\Favorite;
use App\Maker;
use App\Cart;
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

        $isHeaderSearch = false;
        if ($request->has('txtHeaderProductSearch')){
            $isHeaderSearch = true;
        }
        /*メーカーマスタを取得*/
        $Makers = Maker::all();

        $searchFormTab = '';
        $searchReagentNameR = '';
        $searchStandardR = '';
        $searchCasNoR = '';
        $searchCatalogCodeR = '';
        $makerCheckboxR = '';
        $searchReagentNameA = '';
        $searchStandardA = '';
        $searchCatalogCodeA = '';
        $makerCheckboxA = '';
    
        if (!$isHeaderSearch){

            $searchFormTab = $request->searchFormTab;
            $searchReagentNameR = $request->input('searchReagentNameR');
            $searchStandardR = $request->input('searchStandardR');
            $searchCasNoR = $request->input('searchCasNoR');
            $searchCatalogCodeR = $request->input('searchCatalogCodeR');
            $makerCheckboxR = $request->input('makerCheckboxR');
            $searchReagentNameA = $request->input('searchReagentNameA');
            $searchStandardA = $request->input('searchStandardA');
            $searchCatalogCodeA = $request->input('searchCatalogCodeA');
            $makerCheckboxA = $request->input('makerCheckboxA');
            $page = $request->input('page');
            if (!empty($page)){
                $request->session()->put('searchPageCatalogItemWhere_page',$page);
            }
            
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
        }
        

        if (!$isFirst){
            if (!$isHeaderSearch){
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
            }
            else{
                $txtHeaderProductSearch = $request->txtHeaderProductSearch;
                $query = CatalogItem::where(function ($query) use ($txtHeaderProductSearch) {
                    //試薬名
                    if (!empty($txtHeaderProductSearch)) {
                        $query->orWhere('ItemNameJp','like', '%'.$txtHeaderProductSearch.'%')
                            ->orWhere('ItemNameEn','like', '%'.$txtHeaderProductSearch.'%')
                            ->orWhere('CatalogCode','like', '%'.$txtHeaderProductSearch.'%');
                    }
                });
            }

            $CatalogItems = $query->sortable()->paginate(25);
        }
        else {
            $CatalogItems = collect(new CatalogItem());
        }
        /*発注依頼　カートの中身を取得*/
        /*Cartを取得*/
        $Carts = Cart::where('UserId','=', Auth::id())->orderBy('id','asc')->get();

        /*お気に入りを取得*/
        return view('SearchPage/index',compact('CatalogItems','Makers','Carts','searchFormTab','searchReagentNameR','searchStandardR','searchCasNoR','searchCatalogCodeR','makerCheckboxR','searchReagentNameA','searchStandardA','searchCatalogCodeA','makerCheckboxA'));
    }

    public function getData_Favorite(Request $request){

        $response = array();
        $response['status'] = 'OK';
        try{
            $isShared = $request->isFavoriteSharedChecked;
            list($jsonFavoriteTreeReagent,$jsonFavoriteTreeArticle) = BaseClass::getFavoriteTree($isShared);
            $response['jsonFavoriteTreeReagent'] = $jsonFavoriteTreeReagent;
            $response['jsonFavoriteTreeArticle'] = $jsonFavoriteTreeArticle;
        }
        catch(Exception $e){
            $response['status'] = 'NG';
        }
        return Response::json($response);
    }

    public function checkOrderRequest(Request $request) {

        $response = array();
        $response['status'] = 'OK';
        try{
           $id = $request->update_id;
            //Cartに追加しようとしている商品
            $CatalogItem = CatalogItem::findOrFail($id);
            //OrderRequestされている商品
            $OrderRequests = OrderRequest::all();
            foreach($OrderRequests as $OrderRequest) {
                if( $OrderRequest->item->MakerId == $CatalogItem->MakerId &&
                    $OrderRequest->item->CatalogCode == $CatalogItem->CatalogCode &&
                    $OrderRequest->item->ItemNameJp == $CatalogItem->ItemNameJp &&
                    $OrderRequest->item->AmountUnit == $CatalogItem->AmountUnit &&
                    $OrderRequest->item->Standard == $CatalogItem->Standard
                ) {
                    $response['status'] = 'Duplicate';
                    break;
                }
            }

        }
        catch(Exception $e){
            $response['status'] = $e->getMessage();
        }
        return Response::json($response);
    }

    public function favoriteAddProcess(Request $request){

        $response = array();
        $response['status'] = 'OK';
        
        try{
            $page = PostRequest::session()->get('searchPageCatalogItemWhere_page');
            $id = $request->update_id;
            $favoriteUserId = Auth::id();
            $isShared = false;
            if ($request->isFavoriteSharedChecked=='true'){
                $isShared = true;
            }
            BaseClass::favoriteAdd($id,$favoriteUserId,$isShared);
        }catch (QueryException $e) {
            logger()->error("お気に入り登録　QueryException");
            logger()->error($e->getMessage()); 
            $response['status'] = 'NG';        

        }catch(Exception $e){
            $response['status'] = $e->getMessage();
        }
        return Response::json($response);
               
    }

    public function cartAddProcess(Request $request){

        $response = array();
        $response['status'] = 'OK';
        
        try{
            $page = PostRequest::session()->get('searchPageCatalogItemWhere_page');
            $id = $request->update_id;
            BaseClass::cartAdd($id);
        }catch (QueryException $e) {
            logger()->error("発注依頼リスト登録　QueryException");
            logger()->error($e->getMessage()); 
            $response['status'] = 'NG';        
        }
        catch(Exception $e){
            $response['status'] = $e->getMessage();
        }
        return Response::json($response);
               
    }

    /*発注依頼削除*/
    public function destroy(Request $request,$id) {
        $delType = $request->deleteType;
        if ($delType == 'delCartReagent' || $delType == 'delCartArticle') {
            $Cart = Cart::findOrFail($id);
            $Cart->delete();                
        }
        return redirect()->route('SearchPage.index');
    }

    /*カートを削除する*/
    public function deleteCart(Request $request) {

        $response = array();
        $response['status'] = 'OK';
        try{
            $Cart = Cart::findOrFail($request->cart_id);
            $Cart->delete();
        }
        catch(Exception $e){
            $response['status'] = $e->getMessage();
        }
        return Response::json($response);
    }

    /*カート発注数を更新する*/
    public function updateCartOrderRequestNum(Request $request) {

        $response = array();
        $response['status'] = 'OK';
        try{
            $this->updateCartNumber($request->update_id,$request->order_number);
        }
        catch(Exception $e){
            $response['status'] = $e->getMessage();
        }
        return Response::json($response);
    }

    /*カートの数を更新する*/
    public function updateCartNumber($id,$number) {
        $response = array();
        $response['status'] = 'OK';
        try{
            $Cart = Cart::findOrFail($id);
            if ($Cart->OrderRequestNumber != $number){
                $Cart->OrderRequestNumber = $number;
                $Cart->save();
            }
        }
        catch(Exception $e){
            throw $e;
        }
    }    
}