<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Query;
use Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Library\BaseClass;
use App\Delivery;
use App\User;
use App\Maker;
use App\Order;
use App\OrderRequest;
use Auth;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function index(Request $request){

        
        list($Deliveries,$itemclass,$startDate,$endDate,$searchWord,$requestUserId,$makerId) = $this->getData($request,true);
        
        //ユーザー
        $Users = User::all();
        //メーカー
        $Makers = Maker::all();


        return view('Purchase/index',compact('Deliveries','Users','Makers','itemclass','startDate','endDate','searchWord','requestUserId','makerId'));
    }

    function outputCSV(Request $request){
        return response()->streamDownload(
            function() use($request) {

                list($Deliveries,$itemclass,$startDate,$endDate,$searchWord,$requestUserId,$makerId) = $this->getData($request,false);

                $stream = fopen('php://output', 'w');
                stream_filter_prepend($stream,'convert.iconv.utf-8/cp932//TRANSLIT');
                fputcsv($stream, [
                    '発注日',
                    '納品日',
                    '商品名',
                    '規格',
                    'カタログコード',
                    'メーカー',
                    '定価単価',
                    '数量',
                    '納入金額',
                    '使用予算',
                    '発注依頼者'
                ]);

                foreach($Deliveries as $Delivery){
                    fputcsv($stream, [
                        $Delivery->OrderDate,
                        $Delivery->DeliveryDate,
                        $Delivery->ItemNameJp,
                        $Delivery->Standard,
                        $Delivery->CatalogCode,
                        $Delivery->MakerNameJp,
                        $Delivery->UnitPrice,
                        $Delivery->DeliveryNumber,
                        $Delivery->DeliveryPrice,
                        $Delivery->BudgetNameJp,
                        $Delivery->RequestUserNameJp
                    ]);
                }
                fclose($stream);
            }, 
            'Purchase.csv',
            [
                'Content-Type' => 'application/octet-stream',
            ]
        );
    }


    function getData($request,$isIndex){

        /*ItemClass*/
        $itemclass = '-1';
        $itemclassList = array(1,2);
        if($request->has('rdoItemClass')){
            $itemclass = $request->rdoItemClass;
        }
        if ($itemclass <> '-1'){
            $itemclassList = array($itemclass);
        }
        /*発注日*/
        $today = Carbon::today();
        $startDate = "";
        $endDate = $today->format('Y/m/d');
        if ($request->has('startDate')){
            $startDate = $request->startDate;
        }
        else {
            $startDate = BaseClass::getKishuYMD();
        }
        
        if ($request->has('endDate')){
            $endDate = $request->endDate;
        }
        /*検索ワード*/
        $searchWord = "";
        if ($request->has('searchWord')){
            $searchWord = $request->searchWord;
        }
        /*発注依頼者*/
        $requestUserId = -1;
        if ($request->has('selOrderRequestUser')){
            $requestUserId = $request->selOrderRequestUser;
        }
        /*メーカー*/
        $makerId = -1;
        if ($request->has('selMaker')){
            $makerId = $request->selMaker;
        }

//DB::enableQueryLog();
        $Deliveries = Delivery::select([
            'deliveries.*',
            'items.ItemNameJp as ItemNameJp',
            'items.ItemNameEn as ItemNameEn',
            'items.MakerNameJp as MakerNameJp',
            'items.MakerNameEn as MakerNameEn',
            'items.AmountUnit as AmountUnit',
            'items.Standard as Standard',
            'items.CatalogCode as CatalogCode',
            'budgets.budgetNameJp as BudgetNameJp',
            'budgets.budgetNameEn as BudgetNameEn',
            'users.UserNameJp as RequestUserNameJp',
            'users.UserNameEn as RequestUserNameEn',
            'suppliers.SupplierNameJp',
            'suppliers.SupplierNameEn',
            'order_requests.UnitPrice as UnitPrice',
            'order_requests.OrderRemark as OrderRemark'
        ])->leftjoin('items', function($join) {
            $join->on('deliveries.ItemId','=','items.id');
        })->leftjoin('budgets', function($join) {
            $join->on('deliveries.BudgetId','=','budgets.id');
        })->leftjoin('order_requests', function($join) {
            $join->on('deliveries.OrderRequestId','=','order_requests.id');
        })->leftjoin('makers', function($join) {
            $join->on('items.MakerId','=','makers.id');
        })->leftjoin('suppliers', function($join) {
            $join->on('makers.MainSupplierId','=','suppliers.id');
        })->leftjoin('users', function($join) {
            $join->on('order_requests.RequestUserId','=','users.id');
        })
        ->whereIn('deliveries.ItemClass',$itemclassList)
        ->where('deliveries.OrderDate','>=',$startDate)
        ->where('deliveries.OrderDate','<=',$endDate)
        ->where(function ($Deliveries) use ($searchWord) {
            //検索ワード
            if ($searchWord <> '') {
                $Deliveries->orWhere('items.ItemNameJp','like', '%'.$searchWord.'%')
                ->orWhere('items.ItemNameEn','like', '%'.$searchWord.'%')
                ->orWhere('items.CatalogCode','like', '%'.$searchWord.'%');
            }
        })
        ->where(function ($Deliveries) use ($requestUserId) {
            //発注依頼者
            if ($requestUserId <> '-1') {
                $Deliveries->where('order_requests.RequestUserId','=',$requestUserId);
            }
        })
        ->where(function ($Deliveries) use ($makerId) {
            //メーカー
            if ($makerId <> '-1') {
                $Deliveries->where('items.MakerId','=',$makerId);
            }
        })->sortable()->paginate(25);
//dd(DB::getQueryLog());        

        return [$Deliveries,$itemclass,$startDate,$endDate,$searchWord,$requestUserId,$makerId];
    }

    /*発注依頼*/
    public function insertOrderRequest(Request $request){
        
        $response = array();
        $response['status'] = 'OK';

        try{
            $orderid = $request->OrderId;
            $ordernum = $request->OrderNumber;
            $orderremark = $request->OrderRemark;

            /*発注データ取得*/
            $Order = Order::select([
                'orders.*',
                'order_requests.SupplierId'
            ])->leftjoin('order_requests', function($join) {
                $join->on('orders.OrderRequestId','=','order_requests.id');
            })->where('orders.id','=',$orderid)->first();
            
            /*発注依頼データInsert*/
            $OrderRequest = new OrderRequest();
            $OrderRequest->RequestDate = BaseClass::getToday_ymd();
            $OrderRequest->RequestUserId = Auth::id();
            $OrderRequest->ReceiveUserId = -1;
            $OrderRequest->BudgetId = -1;
            $OrderRequest->ItemId = $Order->ItemId;
            $OrderRequest->ItemClass = $Order->ItemClass;
            $OrderRequest->UnitPrice = $Order->UnitPrice;
            $OrderRequest->RequestNumber = $ordernum;
            $OrderRequest->SupplierId = $Order->SupplierId;
            $OrderRequest->RequestProgress = 0;//発注区分(0：未発注)
            $OrderRequest->OrderRemark = $orderremark;
            $OrderRequest->save();
        }
        catch(Exception $e){
            $response['status'] = 'NG';
            $response['errorMsg'] = $e->getMessage();
        }
        return Response::json($response);
    }

}
