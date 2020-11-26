<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query;
use Illuminate\Http\Request;
use Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Library\BaseClass;
use Auth;
use App\Order;
use App\OrderRequest;
use App\Budget;
use App\Delivery;
use App\OrderRequestLog;
use Carbon\Carbon;

class DeliveryController extends Controller
{
    public function index(Request $request){

        $Orders = Order::select([
            'orders.*',
            'items.ItemNameJp as ItemNameJp',
            'items.ItemNameEn as ItemNameEn',
            'budgets.budgetNameJp as BudgetNameJp',
            'budgets.budgetNameEn as BudgetNameEn',
            'budgets.useStartDate as UseStartDate',
            'budgets.useEndDate as UseEndDate',
            'recusers.UserNameJp as RecieveUserNameJp',
            'recusers.UserNameEn as RecieveUserNameEn',
            'requsers.UserNameJp as RequestUserNameJp',
            'requsers.UserNameEn as RequestUserNameEn',
            'suppliers.SupplierNameJp as SupplierNameJp',
            'suppliers.SupplierNameEn as SupplierNameEn',
            'order_slips.OrderSlipNo as OrderSlipNo',
            'mainsup.SupplierNameJp as MainSupplierNameJp',
            'mainsup.SupplierNameEn as MainSupplierNameEn'
        ])
        ->leftjoin('budgets', function($join) {
            $join->on('orders.BudgetId','=','budgets.id');
        })->leftjoin('items', function($join) {
            $join->on('orders.ItemId','=','items.id');
        })->leftjoin('makers', function($join) {
            $join->on('items.MakerId','=','makers.id');
        })->leftjoin('suppliers as mainsup', function($join) {
            $join->on('makers.MainSupplierId','=','mainsup.id');
        })->leftjoin('order_requests', function($join) {
            $join->on('orders.OrderRequestId','=','order_requests.id');
        })->leftjoin('suppliers', function($join) {
            $join->on('order_requests.SupplierId','=','suppliers.id');
        })->leftjoin('users as requsers', function($join) {
            $join->on('order_requests.RequestUserId','=','requsers.id');
        })->leftjoin('order_slips', function($join) {
            $join->on('orders.OrderSlipId','=','order_slips.id');
        })->leftjoin('users as recusers', function($join) {
            $join->on('order_slips.UserId','=','recusers.id');
        })->where('DeliveryProgress','=',config('const.DeliveryProgress.unpaid'))->sortable()->paginate(25);

        //予算マスタ
        $Budgets = Budget::all();

        return view('Delivery/index',compact('Orders','Budgets'));
    }

    /*削除*/
    public function destroy($id){

        $Order = Order::findOrFail($id);
        $OrderRequestId = $Order->OrderRequestId;
        $OrderRequest = OrderRequest::findOrFail($OrderRequestId);
        $OrderRequest->delete();
        $Order->delete();
 
         return redirect()->route('Delivery.index');
    }
     
    /*納品登録*/
    public function insertDelivery(Request $request){

        $response = array();
        $response['status'] = 'OK';
        $response['errorMsg'] = '';

        /*Delivery登録用Insert*/
        $deliveryInsert = [];
        /*OrderRequestLog登録用Insert */
        $orderRequestLogInsert = [];
        /*OrderRequest削除用Delete*/
        $orderRequestDelete = [];
        /*Order更新用Update */
        $orderUpdate =[];

        //変わるもの
        //orderid、納品日、納品数、執行額、予算科目
        $arrayOrderList = $request->arr;

        DB::beginTransaction();

        try{
            foreach($arrayOrderList as $orderCol){
                $order = explode('@',$orderCol);
                
                //$orderid = $order['OId'];
                $orderid = intval($order[0]);
                
                $UpdateOrder = Order::findOrFail($orderid);
                $orderrequestid = $UpdateOrder->OrderRequestId;
                $expectednum = intval($order[2]);//intval($order['Number']);
                $ordernum = $UpdateOrder->OrderNumber;
                //$isPayingComp = false;
                //Orderテーブル更新
                $UpdateOrder->DeliveryNumber += $expectednum;//納品済み数の計算　[発注]納品済数 + [発注]予定納品数
                
                if ( $UpdateOrder->DeliveryNumber >= $ordernum){
                    $UpdateOrder->DeliveryProgress = config('const.DeliveryProgress.payingcomp');//完納
                    //OrderRequestテーブルの削除
                    $orderRequestDelete[] = $orderrequestid;
                }
                $UpdateOrder->save();
                
                //OrderRequestLogテーブル登録
                $OrderRequest = OrderRequest::findOrFail($orderrequestid);
                $OrderRequestLog = OrderRequestLog::where('id','=',$orderrequestid)->first();
                
                if ($OrderRequestLog==null){ 
                    $OrderRequestLog = new OrderRequestLog();
                    $OrderRequestLog->id = $OrderRequest->id;
                    $OrderRequestLog->RequestDate = $OrderRequest->RequestDate;
                    $OrderRequestLog->OrderDate = $OrderRequest->OrderDate;
                    $OrderRequestLog->RequestUserId = $OrderRequest->RequestUserId;
                    $OrderRequestLog->ReceiveUserId = $OrderRequest->ReceiveUserId;
                    $OrderRequestLog->BudgetId = $OrderRequest->BudgetId;
                    $OrderRequestLog->ItemId = $OrderRequest->ItemId;
                    $OrderRequestLog->ItemClass = $OrderRequest->ItemClass;
                    $OrderRequestLog->UnitPrice = $OrderRequest->UnitPrice;
                    $OrderRequestLog->RequestNumber = $OrderRequest->RequestNumber;
                    $OrderRequestLog->SupplierId = $OrderRequest->SupplierId;
                    $OrderRequestLog->RequestProgress = $OrderRequest->RequestProgress;
                    $OrderRequestLog->OrderRemark = $OrderRequest->OrderRemark;
                    $orderRequestLogInsert[] = $OrderRequestLog->toArray();
                    
                }
                
                
                //Deliveryテーブルの登録
                $Delivery = new Delivery();
                $Delivery->UserId = Auth::id();
                $Delivery->OrderSlipId = $UpdateOrder->OrderSlipId;
                $Delivery->OrderSlipNo = $UpdateOrder->orderSlip->OrderSlipNo;
                $Delivery->OrderId = $UpdateOrder->id;
                $Delivery->OrderRequestId = $UpdateOrder->OrderRequestId;
                $Delivery->BudgetId = intval($order[4]);//$order['BId'];
                $Delivery->ItemId = $UpdateOrder->ItemId;
                $Delivery->ItemClass = $UpdateOrder->ItemClass;
                $Delivery->OrderDate = $UpdateOrder->OrderDate;
                $Delivery->DeliveryDate = $order[1];//$order['Date'];
                $Delivery->DeliveryNumber = $expectednum;
                $Delivery->DeliveryPrice = floatval($order[3]);//$order['Price'];
                $deliveryInsert[] = $Delivery->toArray();

            }
            Delivery::insert($deliveryInsert);
            OrderRequestLog::insert($orderRequestLogInsert);
            OrderRequest::whereIn('id',$orderRequestDelete)->delete();
            
            DB::commit();
        }
        catch (QueryException $e) {
            DB::rollback();
            logger()->error("納品処理　QueryException");
            logger()->error($e->getMessage()); 
            $response['status'] = 'NG';      
        }
        catch(Exception $e){
            DB::rollback();
            $response['status'] = 'NG';
        }

        return Response::json($response);
    }

}
