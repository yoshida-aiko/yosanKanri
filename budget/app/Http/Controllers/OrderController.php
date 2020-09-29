<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query;
use Illuminate\Http\Request;
use Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Library\BaseClass;
use App\OrderRequest;
use App\Order;
use App\Budget;
use App\Item;
use App\Favorite;
use App\Maker;
use App\User;
use App\Delivery;
use App\Supplier;
use Auth;
use Carbon\Carbon;
use PDF;

class OrderController extends Controller
{
    public function index(Request $request){

        $itemclass = '-1';
        $itemclassList = array(1,2);
        if($request->has('rdoItemClass')){
            $itemclass = $request->rdoItemClass;
        }
        if ($itemclass <> '-1'){
            $itemclassList = array($itemclass);
        }

        $useridlist = array(Auth::id(),'-1');
        /*OrderRequest*/
        $OrderRequests = OrderRequest::whereIn('ReceiveUserId',$useridlist)->whereIn('ItemClass',$itemclassList)
            ->where('RequestProgress','=',0)->where('BudgetId','=',-1)->sortable()->paginate(25);
        $parentTree = array();
        $childTree = array();
        /*予算リスト*/
        $arrayBudgetTree = array();
        /*予算一覧*/
        $arrayBudgetList = array();
        /*コンテキストメニュー用予算リスト*/
        $arrayBudgetForContext = array();

        /*Budget*/
        $parentBudgets = Budget::all();
        /*OrderRequest*/
        $orderRequest_Orders = OrderRequest::select(['order_requests.*','budgets.budgetNameJp as BudgetNameJp','budgets.budgetNameEn as BudgetNameEn'])
        ->leftjoin('budgets', function($join) {
            $join->on('order_requests.BudgetId','=','budgets.id');
        })->whereIn('ReceiveUserId',$useridlist)->where('RequestProgress','=',0)->where('BudgetId','<>',-1)->sortable()->paginate(25);

        /*$orderRequest_Orders = OrderRequest::whereIn('ReceiveUserId',$useridlist)
            ->where('RequestProgress','=',0)->where('BudgetId','<>',-1)->get();*/
        $orderSum = 0;
        foreach($parentBudgets as $parentBudget){
            $childTree = array();
            $orderSum = 0;
            foreach($orderRequest_Orders as $orderRequest_Order){
                if ($orderRequest_Order->BudgetId == $parentBudget->id) {
                    $sumprice = 0;
                    if ($orderRequest_Order->UnitPrice !== 0 && $orderRequest_Order->RequestNumber !== 0) {
                        $sumprice = $orderRequest_Order->UnitPrice * $orderRequest_Order->RequestNumber;
                    }
                    $item = [
                        'OrderId'=>$orderRequest_Order->id,
                        'ProductNameJp'=>$orderRequest_Order->item->ItemNameJp,
                        'ProductNameEn'=>$orderRequest_Order->item->ItemNameEn,
                        'AmountUnit'=>$orderRequest_Order->item->AmountUnit,
                        'UnitPrice'=>number_format($orderRequest_Order->UnitPrice),
                        'RequestNumber'=>number_format($orderRequest_Order->RequestNumber),
                        'SummaryPrice'=>number_format($sumprice),
                        'Standard'=>$orderRequest_Order->item->Standard,
                        'MakerNameJp'=>$orderRequest_Order->item->MakerNameJp,
                        'MakerNameEn'=>$orderRequest_Order->item->MakerNameEn,
                        'CatalogCode'=>$orderRequest_Order->item->CatalogCode,
                        'RequestUserId'=>$orderRequest_Order->RequestUserId,
                        'RequestUserName'=>$orderRequest_Order->user->UserNameJp
                    ];
                    
                    array_push($childTree,$item);
                    $orderSum = $orderSum + $sumprice;
                }
            }            
            
            $addFlag = 0;
            if (count($childTree) == 0){
                $today = Carbon::today();
                $st = new Carbon($parentBudget->useStartDate);
                $et = new Carbon($parentBudget->useEndDate);
                if ($st > $today || $et < $today){
                    $addFlag = 1;
                }
            }

            $BudgetUsed = 0;
            $BudgetScheduled = 0;
            $BudgetRemainBal = 0;
            $BudgetScheduledRemain = 0;
            if ($addFlag == 0){
                /*執行額を取得 */
                $remain_D = Delivery::where('BudgetId','=',$parentBudget->id)
                    ->sum('DeliveryPrice');
                /*執行予定額を取得 */
                $remain_O_getdatas = Order::where('BudgetId','=',$parentBudget->id);
                $remain_O = 0;
                foreach($remain_O_getdatas as $getdata){
                    $remain_O = $remain_O + ($getdata->UnitPrice * ($getdata->OrderNumber - $getdata->DeliveryNumber));
                }

                /*執行額をセット*/
                if ($remain_D !== null) {
                    $BudgetUsed = $remain_D;
                }
                /*執行予定額をセット*/
                if ($remain_O !== null) {
                    $BudgetScheduled = $remain_O;
                }
                /*執行済残高（[残予算]予算－[残予算]執行額*/
                $BudgetRemainBal = $parentBudget->budgetAmount - floatval($BudgetUsed);

                /*.執行予定込残高([残予算]執行済残高-[残予算]執行予定額)*/
                $BudgetScheduledRemain = $BudgetRemainBal - floatval($BudgetScheduled);
                
                $parentTree = array(
                    'id'=>$parentBudget->id,
                    'BudgetNameJp'=>$parentBudget->budgetNameJp,
                    'BudgetNameEn'=>$parentBudget->budgetNameEn,
                    'orderFee'=>number_format($orderSum),
                    'remainFee'=>number_format($BudgetScheduledRemain - $orderSum),
                    'children'=>array()
                );
                foreach($childTree as $child){
                    array_push($parentTree['children'],$child);
                }
                /*予算リスト*/
                array_push($arrayBudgetTree,$parentTree);
                $itemcontext = [
                    'BudgetId'=>$parentBudget->id,
                    'BudgetNameJp'=>$parentBudget->budgetNameJp,
                    'BudgetNameEn'=>$parentBudget->budgetNameEn
                ];
                /*予算リストコンテキストメニュー用*/
                array_push($arrayBudgetForContext,$itemcontext);
                $parentTree = array();
    
                /*予算一覧を作成する*/
                $item =[
                    'BudgetNameJp'=>$parentBudget->budgetNameJp,
                    'BudgetNameEn'=>$parentBudget->budgetNameEn,
                    'BudgetAmount'=>number_format($parentBudget->budgetAmount),
                    'BudgetUsed'=>number_format($BudgetUsed),
                    'BudgetScheduled'=>number_format($BudgetScheduled),
                    'BudgetRemainBal'=>number_format($BudgetRemainBal),
                    'BudgetScheduledRemain'=>number_format($BudgetScheduledRemain),
    
                ];
                array_push($arrayBudgetList,$item);
            }
        }

        //発注先
        $Suppliers = Supplier::all();

        return view('Order/index',compact('OrderRequests','arrayBudgetTree','orderRequest_Orders','arrayBudgetList','arrayBudgetForContext','Suppliers','itemclass'));
    }

    /*削除*/
    public function destroy($id){

        $OrderRequest = OrderRequest::findOrFail($id);
        $OrderRequest->delete();

        return redirect()->route('Order.index');
    }

    /*OrderRequestにBudgetIdを付与する */
    public function updateOrderRequestGiveBudget(Request $request) {

        $response = array();
        $response['status'] = 'OK';
        
        try{
            $OrderRequest = OrderRequest::findOrFail($request->orderrequestid);
            $OrderRequest->BudgetId = $request->budgetid;
            $OrderRequest->save();
        }
        catch(Exception $e) {
            $response['status'] = $e->getMessage();
        }

        return Response::json($response);
    }

    /*単価・数量の更新*/
    public function updateListPrice(Request $request) {
        
        $response = array();
        $response['status'] = 'OK';
        
        try{
            $id = $request->id;
            $price = $request->price;
            $ordernum = $request->ordernum;

            $OrderRequest = OrderRequest::findOrFail($id);
            if ($price <> '-1') {
                $OrderRequest->UnitPrice = $price;
            }
            if ($ordernum <> '-1') {
                $OrderRequest->RequestNumber = $ordernum;
            }
            
            $OrderRequest->save();
        }
        catch(Exception $e) {
            $response['status'] = $e->getMessage();
        }
        return Response::json($response);
    }    
    /*発注先の更新*/
    public function updateSupplier(Request $request) {
        
        $response = array();
        $response['status'] = 'OK';
        
        try{
            $id = $request->id;
            $supplierId = $request->supplierid;

            $OrderRequest = OrderRequest::findOrFail($id);
            if ($supplierId <> '') {
                $OrderRequest->SupplierId = $supplierId;
            }
            $OrderRequest->save();
        }
        catch(Exception $e) {
            $response['status'] = $e->getMessage();
        }
        return Response::json($response);
    }

    public function createPDF(Request $request) {
        
        $response = array();
        $response['status'] = 'OK';
        
        try{

            $id = $request->id;
            $pdf = PDF::loadHTML('<h1>Hello World</h1>');
        }
        catch(Exception $e) {
            $response['status'] = $e->getMessage();
        }

        return $pdf->inline();
    }

}
