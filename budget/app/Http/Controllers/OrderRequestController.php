<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query;
use Illuminate\Http\Request;
use Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Library\BaseClass;
use App\OrderRequest;
use App\Cart;
use App\Item;
use App\Favorite;
use App\Maker;
use App\User;
use Auth;

class OrderRequestController extends Controller
{
    public function index(Request $request){

        /*Cart*/
        $Carts = Cart::select([
            'carts.*',
            DB::raw('carts.UnitPrice * carts.OrderRequestNumber as OrderPrice'),
            'suppliers.SupplierNameJp as SupplierName'
        ])
        ->leftjoin('suppliers', function($join) {
            $join->on('carts.SupplierId','=','suppliers.id');
        })->where('UserId','=',Auth::id())->sortable()->paginate(25);
        
        /*Makers*/
        $Makers = Maker::all();

        /*Users*/
        $Users = User::where('UserAuthString','like', '%Order%')->get();

        return view('OrderRequest/index',compact('Carts','Makers','Users'));
    }

    public function getData_Favorite(Request $request){

        $response = array();
        $response['status'] = 'OK';
        try{
            list($jsonFavoriteTreeReagent,$jsonFavoriteTreeArticle) = BaseClass::getFavoriteTree();
            $response['jsonFavoriteTreeReagent'] = $jsonFavoriteTreeReagent;
            $response['jsonFavoriteTreeArticle'] = $jsonFavoriteTreeArticle;
        }
        catch(Exception $e){
            $response['status'] = 'NG';
        }
        return Response::json($response);
    }
    
    /*お気に入りから発注依頼リストへ*/
    public function moveToCart(Request $request) {
        
        $response = array();
        $response['status'] = 'OK';
        
        try{
            $id = $request->update_id;
            $Favorite = Favorite::findOrFail($id);

            $Cart = new Cart();
            $Cart->UserId = Auth::id();
            $Cart->CatalogItemId = $Favorite->ItemId;
            $Cart->ItemClass = $Favorite->ItemClass;
            $Cart->MakerId = $Favorite->item->MakerId;
            $Cart->CatalogCode = $Favorite->item->CatalogCode;
            $Cart->MakerNameJp = $Favorite->item->MakerNameJp;
            $Cart->MakerNameEn = $Favorite->item->MakerNameEn;
            $Cart->ItemNameJp = $Favorite->item->ItemNameJp;
            $Cart->ItemNameEn = $Favorite->item->ItemNameEn;
            $Cart->AmountUnit = $Favorite->item->AmountUnit;
            $Cart->Standard = $Favorite->item->Standard;
            $Cart->CASNo = $Favorite->item->CASNo;
            $Cart->UnitPrice = $Favorite->item->UnitPrice;
            $Cart->OrderRequestNumber = 1;

            $supplierid = -1;
            if ($Favorite->item->MakerId > 0){
                $Maker = Maker::findOrFail($Favorite->item->MakerId);
                $supplierid = $Maker->MainSupplierId;
            }
            $Cart->SupplierId = $supplierid;
            $Cart->save();
        }
        catch(Exception $e) {
            $response['status'] = $e->getMessage();
        }
        return Response::json($response);
    }

    /*単価・数量・備考の更新*/
    public function updateListPrice(Request $request) {
        
        $response = array();
        $response['status'] = 'OK';
        
        try{
            $id = $request->id;
            $price = $request->price;
            $ordernum = $request->ordernum;
            $remark = $request->remark;

            $Cart = Cart::findOrFail($id);
            if ($price <> '-1') {
                $Cart->UnitPrice = $price;
            }
            if ($ordernum <> '-1') {
                $Cart->OrderRequestNumber = $ordernum;
            }
            $Cart->OrderRemark = $remark;
            $Cart->save();
        }
        catch(Exception $e) {
            $response['status'] = $e->getMessage();
        }
        return Response::json($response);
    }

    /*発注依頼リスト削除*/
    public function destroy($id){

        $Cart = Cart::findOrFail($id);
        $Cart->delete();

        return redirect()->route('OrderRequest.index');
    }

    /*新規商品入力*/
    public function newProductStore(Request $request) {

        $Cart = new Cart();
        $Cart->UserId = Auth::id();
        $Cart->CatalogItemId = -1;
        $Cart->ItemClass = $request->newItemClass;
        $targetMaker = Maker::findOrFail($request->newMaker);
        $Cart->MakerId =$request->newMaker;
        $Cart->CatalogCode = $request->newCatalogCode;
        $Cart->MakerNameJp = $targetMaker->MakerNameJp;
        $Cart->MakerNameEn = $targetMaker->MakerNameEn;
        $Cart->ItemNameJp = $request->newProductName;
        $Cart->ItemNameEn = $request->newProductName;
        $Cart->AmountUnit = $request->newAmountUnit;
        $Cart->Standard = $request->newStandard;
        $Cart->CASNo = '';
        $Cart->UnitPrice = $request->newUnitPrice;
        $Cart->OrderRequestNumber = 1;
        $Cart->SupplierId = $targetMaker->MainSupplierId;
        $Cart->save();

        return redirect()->route('OrderRequest.index');
    }

    /* 発注依頼ボタンクリック時 */
    public function orderRequest(Request $request) {

        $response = array();
        $response['status'] = 'OK';
        
        DB::beginTransaction();
        try{
            $arrayCartIds = $request->arrayCartIds;
            $orderUserId = $request->orderuserid;

            /*対象カートを取得*/
            $targetCarts = Cart::whereIn('id',$arrayCartIds)->get();
            $requestdt = $request->today;
            foreach($targetCarts as $targetCart) {
                /*発注依頼データ作成*/
                $OrderRequest = new OrderRequest();
                $OrderRequest->RequestDate = $requestdt;
                $OrderRequest->OrderDate = null;
                $OrderRequest->RequestUserId = $targetCart->UserId;
                $OrderRequest->ReceiveUserId = $orderUserId;
                $OrderRequest->BudgetId = -1;
                $OrderRequest->ItemClass = $targetCart->ItemClass;
                $OrderRequest->UnitPrice = $targetCart->UnitPrice;
                $OrderRequest->RequestNumber = $targetCart->OrderRequestNumber;
                $OrderRequest->RequestProgress = 0;
                $OrderRequest->OrderRemark = $targetCart->OrderRemark;

                //優先する発注先※Cartの優先する発注先IDが-1の時CartのMakerIdを元にメーカーの優先する発注先を取得
                if ($targetCart->SupplierId == -1){
                    $Maker = Maker::findOrFail($targetCart->MakerId);
                    $OrderRequest->SupplierId = $Maker->MainSupplierId;
                }
                else {
                    $OrderRequest->SupplierId = $targetCart->SupplierId;
                }

                /*商品データ検索*/
                $where = [
                    'MakerId'       => $targetCart->MakerId,
                    'CatalogCode'   => $targetCart->CatalogCode,
                    'ItemNameJp'    => $targetCart->ItemNameJp,
                    'AmountUnit'    => $targetCart->AmountUnit,
                    'Standard'      => $targetCart->Standard
                ];
                $targetItemId = -1;
                $Item = Item::where($where)->first();
                if ($Item == NULL){
                    $newItem = new Item();
                    $newItem->ItemClass = $targetCart->ItemClass;
                    $newItem->MakerId = $targetCart->MakerId;
                    $newItem->MakerNameJp = $targetCart->MakerNameJp;
                    $newItem->MakerNameEn = $targetCart->MakerNameEn;
                    $newItem->CatalogCode = $targetCart->CatalogCode;
                    $newItem->ItemNameJp = $targetCart->ItemNameJp;
                    $newItem->ItemNameEn = $targetCart->ItemNameEn;
                    $newItem->AmountUnit = $targetCart->AmountUnit;
                    $newItem->Standard = $targetCart->Standard;
                    $newItem->CASNo = $targetCart->CASNo;
                    $newItem->UnitPrice = $targetCart->UnitPrice;
                    $newItem->save();

                    $targetItemId = $newItem->id;
                }
                else {
                    $targetItemId = $Item->id;
                }
                $OrderRequest->ItemId = $targetItemId;
                $OrderRequest->save();

                /*対象カートデータを削除*/
                $targetCart->delete();
            }
            DB::commit();
        }
        catch(Exception $e) {
            DB::rollback();
            //$response['status'] = $e->getMessage();
        }

        return Response::json($response);

    }

}
