<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query;
use Illuminate\Http\Request;
use Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Library\BaseClass;
use App\Mail\OrderEmail;
use App\OrderRequest;
use App\Order;
use App\Budget;
use App\Item;
use App\Favorite;
use App\Maker;
use App\User;
use App\Delivery;
use App\Supplier;
use App\Sequence;
use App\OrderSlip;
use Auth;
use Carbon\Carbon;
use PDF;
use Mail;

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
        if ($request->sort <> 'SupplierNameJp' &&
            $request->sort <> 'OrderPrice' &&
            $request->sort <> 'BudgetNameJp'
        ){
            $OrderRequests = OrderRequest::select([
                'order_requests.*',
                'order_requests.ItemClass as ItemClass',
                'items.ItemNameJp as ItemNameJp',
                'items.ItemNameEn as ItemNameEn',
                'items.AmountUnit as AmountUnit',
                'items.Standard as Standard',
                'items.CatalogCode as CatalogCode',
                'items.MakerNameJp as MakerNameJp',
                'items.MakerNameEn as MakerNameEn',
                'users.UserNameJp as RequestUserNameJp',
                'users.UserNameEn as RequestUserNameEn',
                'suppliers.SupplierNameJp',
                'suppliers.SupplierNameEn'
            ])
            ->leftjoin('items', function($join) {
                $join->on('order_requests.ItemId','=','items.id');
            })
            ->leftjoin('makers', function($join) {
                $join->on('items.MakerId','=','makers.id');
            })
            ->leftjoin('suppliers', function($join) {
                $join->on('makers.MainSupplierId','=','suppliers.id');
            })
            ->leftjoin('users', function($join) {
                $join->on('order_requests.RequestUserId','=','users.id');
            })
            ->whereIn('ReceiveUserId',$useridlist)->whereIn('order_requests.ItemClass',$itemclassList)
            ->where('RequestProgress','=',0)->where('BudgetId','=',-1)->sortable()->paginate(25);
        }
        else{
            $OrderRequests = OrderRequest::select([
                'order_requests.*',
                'order_requests.ItemClass as ItemClass',
                'items.ItemNameJp as ItemNameJp',
                'items.ItemNameEn as ItemNameEn',
                'items.AmountUnit as AmountUnit',
                'items.Standard as Standard',
                'items.CatalogCode as CatalogCode',
                'items.MakerNameJp as MakerNameJp',
                'items.MakerNameEn as MakerNameEn',
                'users.UserNameJp as RequestUserNameJp',
                'users.UserNameEn as RequestUserNameEn',
                'suppliers.SupplierNameJp',
                'suppliers.SupplierNameEn'
            ])
            ->leftjoin('items', function($join) {
                $join->on('order_requests.ItemId','=','items.id');
            })
            ->leftjoin('makers', function($join) {
                $join->on('items.MakerId','=','makers.id');
            })
            ->leftjoin('suppliers', function($join) {
                $join->on('makers.MainSupplierId','=','suppliers.id');
            })
            ->leftjoin('users', function($join) {
                $join->on('order_requests.RequestUserId','=','users.id');
            })
            ->whereIn('ReceiveUserId',$useridlist)->whereIn('order_requests.ItemClass',$itemclassList)
            ->where('RequestProgress','=',0)->where('BudgetId','=',-1)->paginate(25);            
        }
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
        $orderRequest_Orders = OrderRequest::select([
            'order_requests.*',
            'budgets.budgetNameJp as BudgetNameJp',
            'budgets.budgetNameEn as BudgetNameEn',
            'items.ItemNameJp as ItemNameJp',
            'suppliers.SupplierNameJp as SupplierNameJp',
            'users.UserNameJp as RequestUserNameJp',
            'items.UnitPrice as UnitPrice',
            DB::raw('order_requests.UnitPrice * order_requests.RequestNumber as OrderPrice')
        ])
        ->leftjoin('budgets', function($join) {
            $join->on('order_requests.BudgetId','=','budgets.id');
        })
        ->leftjoin('suppliers', function($join) {
            $join->on('order_requests.SupplierId','=','suppliers.id');
        })
        ->leftjoin('users', function($join) {
            $join->on('order_requests.RequestUserId','=','users.id');
        })
        ->leftjoin('items', function($join) {
            $join->on('order_requests.ItemId','=','items.id');
        })
        ->whereIn('ReceiveUserId',$useridlist)->where('RequestProgress','=',0)->where('BudgetId','<>',-1)->sortable()->paginate(25);

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
                        'RequestUserNameJp'=>$orderRequest_Order->user->UserNameJp,
                        'RequestUserNameEn'=>$orderRequest_Order->user->UserNameEn
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
                /*$remain_O_getdatas = Order::where('BudgetId','=',$parentBudget->id);*/
                $remain_O = Order::where('BudgetId','=',$parentBudget->id)->sum(DB::raw('UnitPrice * (OrderNumber - DeliveryNumber)'));;
                /*foreach($remain_O_getdatas as $getdata){
                    $remain_O = $remain_O + ($getdata->UnitPrice * ($getdata->OrderNumber - $getdata->DeliveryNumber));
                }*/

                /*執行額をセット*/
                if ($remain_D != null) {
                    $BudgetUsed = $remain_D;
                }
                /*執行予定額をセット*/
                if ($remain_O != null) {
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

    /*発注書理 */
    public function orderExec(Request $request){

        $response = array();
        $response['status'] = 'OK';
        $response['errorMsg'] = '';
        $msg = '';
        try{
            $arrayOrderRequestIds = $request->arrayOrderRequestIds;
            $howToOrder = $request->howToOrderFlag;/*0:Mail 1:PDF 9:Other*/
            $arrayOrderInformation = $this->orderProcessing($arrayOrderRequestIds,$howToOrder);
            if (is_array($arrayOrderInformation)){
                switch ($howToOrder)
                {
                    case 0: //Mail
                        $msg = $this->orderSendMail($arrayOrderInformation);
                        break;
                    default: //Other
                        break;
                }
            }
            else {
                $response['status'] = 'NG';
                $response['errorMsg'] = $arrayOrderInformation;
                
            }
            if ($msg!=""){
                $response['status'] = 'NG';
                $response['errorMsg'] = $msg;
            }
        }
        catch(Exception $e){
            $response['status'] = 'NG';
            $response['errorMsg'] = $e->getMessage();
        }
        return Response::json($response);
    }

    /*発注更新処理*/
    public function orderProcessing($arrayOrderRequestIds,$howToOrder){
        
        $ret = "";
        $today = Carbon::now();
        $reqDt = $today->format('ymd');
        $orderDt = $today->format('Y/m/d');
        $arrayOrderInformation = [];
        $arraychild = [];

        DB::beginTransaction();
        try{
            /*１．発注依頼データを発注済に更新*/
            $targetOrderRequests = OrderRequest::select(['order_requests.*','budgets.budgetNameJp as BudgetNameJp','budgets.budgetNameEn as BudgetNameEn'])
            ->leftjoin('budgets', function($join) {
                $join->on('order_requests.BudgetId','=','budgets.id');
            })->whereIn('order_requests.id',$arrayOrderRequestIds)->get();
            foreach($targetOrderRequests as $target){
                $target->RequestProgress = 1;/*発注済*/
                $target->OrderDate = $orderDt;
                $target->save();
            }
            /*２．発注伝票テーブルの作成*/
            /*1)発注伝票番号の取得 */
            $Sequence = DB::table('sequences')->where('name','=','ORDER_SLIP_NO')->lockForUpdate()->first();
            $currentNo = $Sequence->CurrentNo;

            /*2)取得日付が当日以上だった場合、発注伝票番号をカウントアップして連番(Sequences)を更新*/
            if($currentNo >= (float)($reqDt.'000001')){
                $currentNo++;
            }
            else {
                $currentNo = (float)($reqDt.'000001');
            }

            DB::table('sequences')->where('name','=','ORDER_SLIP_NO')->update(['CurrentNo' => $currentNo]);

            /*1)発注伝票エンティティを作成 */
            $groupBySuppliers = $targetOrderRequests->groupBy('SupplierId')->toArray();
            foreach(array_keys($groupBySuppliers) as $groupBySupplier ){

                $OrderSlip = new OrderSlip();
                $OrderSlip->OrderSlipNo = $currentNo;
                $OrderSlip->OrderDate = $orderDt;
                $OrderSlip->SupplierId = $groupBySupplier;
                $OrderSlip->UserId = Auth::id();
                $OrderSlip->OrderMethod = $howToOrder;
                $OrderSlip->save();

                /*３．発注テーブル登録*/
                $forOrders = $targetOrderRequests->where('SupplierId','=',$groupBySupplier);
                $authUser = Auth::user();
                $supplierNameJp = '';
                $supplierChargeUserJp = '';
                $SupplierMailAddress = '';
                foreach($forOrders as $forOrder){

                    $Order = new Order();
                    $Order->OrderSlipId = $OrderSlip->id;
                    $Order->OrderRequestId = $forOrder->id;
                    $Order->BudgetId = $forOrder->BudgetId;
                    $Order->ItemId = $forOrder->ItemId;
                    $Order->ItemClass = $forOrder->ItemClass;
                    $Order->OrderDate = $orderDt;
                    $Order->UnitPrice = $forOrder->UnitPrice;
                    $Order->OrderNumber = $forOrder->RequestNumber;
                    $Order->DeliveryNumber = 0;
                    $Order->DeliveryProgress = config('const.DeliveryProgress.unpaid');//0：未納
                    $Order->save();

                    $item_c = [
                        'OrderItemNameJp' => $forOrder->item->ItemNameJp,
                        'OrderItemNameEn' => $forOrder->item->ItemNameEn,
                        'OrderStandard' => $forOrder->item->Standard,
                        'OrderAmountUnit' => $forOrder->item->AmountUnit,
                        'OrderCatalogCode' => $forOrder->item->CatalogCode,
                        'OrderMakerNameJp' => $forOrder->item->MakerNameJp,
                        'OrderNumber' => $forOrder->RequestNumber,
                        'OrderRequestUserNameJp' => $authUser->UserNameJp,
                        'OrderBudgetNameJp' => $forOrder->BudgetNameJp,
                        'OrderRemark' => $forOrder->OrderRemark,
                    ];
                    array_push($arraychild,$item_c);

                    $supplierNameJp = $forOrder->supplier->SupplierNameJp;
                    $supplierChargeUserJp = $forOrder->supplier->ChargeUserJp;
                    $SupplierMailAddress = $forOrder->supplier->EMail;
                }


                $item_p = [
                    'OrderSlipNo' => $currentNo,
                    'OrderDate' => $orderDt,
                    'OrderMailTitle' => '発注のご依頼',
                    'SupplierNameJp' => $supplierNameJp,
                    'SupplierChargeUserJp' => $supplierChargeUserJp,
                    'SupplierMailAddress' => $SupplierMailAddress,
                    'FromUserNameJp' => $authUser->UserNameJp,
                    'FromUserMailAddress' => $authUser->email,
                    'FromUserSignature' => $authUser->Signature,
                    'OrderRequests' => $arraychild,
                    'SystemName' => config(['app.name']),
                ];
                $arraychild = [];
                array_push($arrayOrderInformation,$item_p);
            }
            
            DB::commit();
            $ret = $arrayOrderInformation;
        }
        catch(Exception $e) {
            DB::rollback();
            throw $e;
            $ret = $e->getMessage();
        }

        return $ret;
    }


    /*発注PDF作成*/
    public function createPDF(Request $request) {    
        try{
            
            $arrayOrderRequestIds = explode(",",$request->arrayOrderRequestIds);

            $arrayOrderInformation = $this->orderProcessing($arrayOrderRequestIds,1);

            if(is_array($arrayOrderInformation)){
                $pdf = PDF::loadView('orderpdf',compact('arrayOrderInformation'))
                    ->setPaper('A4')
                    ->setOption('encoding','utf-8')
                    ->setOption('margin-top', 10)
                    ->setOption('margin-left', 10)
                    ->setOption('margin-right', 10)
                    ->setOption('orientation', 'Landscape')
                    ->setOption('footer-font-size', 9)
                    ->setOption('footer-center', '[page] ページ')
                    ->setOption('footer-font-name', 'IPAexMincho');
            }
        }
        catch(Exception $e) {
            throw $e;
        }

        return $pdf->download('order.pdf');
    }

    /*発注メール送信処理*/
    public function orderSendMail($arrayOrderInformation){

        $retMessage = "";
        try{
            foreach($arrayOrderInformation as $orderInformation){
                $arrayMailAddress = explode(",", $orderInformation['SupplierMailAddress']);
                $arrayMailAddress = array_map('trim', $arrayMailAddress);
                $strMailAddress = implode(",",$arrayMailAddress);
                $to = [];
                foreach($arrayMailAddress as $emailaddr){
                    $item =[
                        'email' => $emailaddr
                    ];
                    array_push($to,$item);
                }
                $cc = [
                    $orderInformation['FromUserMailAddress']
                ];
                Mail::to($to)->cc($cc)->send(new OrderEmail($orderInformation));
            }
        }
        catch(Exception $e){
            $retMessage = $e->getMessage();
        }
        if(count(Mail::failures()) > 0){
            $retMessage .= "メール送信エラーアドレス：".Mail::failures();
        }
        return $retMessage;
    }




}
