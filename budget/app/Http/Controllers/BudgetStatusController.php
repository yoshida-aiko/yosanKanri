<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Query;
use Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Library\BaseClass;
use App\Order;
use App\Budget;
use App\Delivery;
use App\Item;
use Auth;
use Carbon\Carbon;

class BudgetStatusController extends Controller
{
    public function index(Request $request){

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

        $Budgets = Budget::where('useStartDate','<=',$startDate)->where('useEndDate','>=',$endDate)->get();
        
        $BudgetLists = array();
        foreach($Budgets as $Budget){
            $BudgetUsed = 0;
            $BudgetScheduled = 0;
            $BudgetRemainBal = 0;
            $BudgetScheduledRemain = 0;
            //DB::enableQueryLog();
            $remain_D = Delivery::where('BudgetId','=',$Budget->id)->sum('DeliveryPrice');
                        /*->where('DeliveryDate','>=',$startDate)
                        ->where('DeliveryDate','<=',$endDate)->sum('DeliveryPrice');*/
            
                        //dd(DB::getQueryLog());

            $remain_O = Order::where('BudgetId','=',$Budget->id)->sum(DB::raw('UnitPrice * (OrderNumber - DeliveryNumber)'));
                        /*->where('OrderDate','>=',$startDate)
                        ->where('OrderDate','<=',$endDate)->sum(DB::raw('UnitPrice * (OrderNumber - DeliveryNumber)'));*/
            
            /*執行額をセット*/
            if ($remain_D != null) {
                $BudgetUsed = $remain_D;
            }
            /*執行予定額をセット*/
            if ($remain_O != null) {
                $BudgetScheduled = $remain_O;
            }
            /*執行済残高（[残予算]予算－[残予算]執行額*/
            $BudgetRemainBal = $Budget->budgetAmount - floatval($BudgetUsed);
            /*執行予定込残高([残予算]執行済残高-[残予算]執行予定額)*/
            $BudgetScheduledRemain = $BudgetRemainBal - floatval($BudgetScheduled);
            $item = [
                'BudgetId' => $Budget->id,
                'BudgetNameJp' => $Budget->budgetNameJp,
                'Budget' => number_format($Budget->budgetAmount),
                'BudgetUsed' => number_format($BudgetUsed),
                'BudgetScheduled' => number_format($BudgetScheduled),
                'BudgetRemainBal' => number_format($BudgetRemainBal),
                'BudgetScheduledRemain' => number_format($BudgetScheduledRemain)
            ];
            array_push($BudgetLists,$item);
        }

        $BudgetDetails = null;
        $hidSelectedBudgetId = $request->hidSelectedBudgetId;

        if ($hidSelectedBudgetId > 0){
            $BudgetDetails = $this->getDetail_Data($hidSelectedBudgetId,$startDate,$endDate);
        }

        return view('BudgetStatus/index',compact('BudgetLists','BudgetDetails','startDate','endDate','hidSelectedBudgetId'));
    }

    /*詳細を取得*/
    function getDetail(Request $request){

        $response = array();
        $response['status'] = 'OK';

        try{
            $selectBudgetId = $request->BudgetId;
            $startDate = $request->startDate;
            $endDate = $request->endDate;
            $response['datas'] = $this->getDetail_Data($selectBudgetId,$startDate,$endDate);
        }
        catch(Exception $e){
            $response['status'] = 'NG';
            $response['errorMsg'] = $e->getMessage();
        }
        return Response::json($response);
    }

    function getDetail_Data($BudgetId,$startDate,$endDate){

        $BudgetDetails = array();
        $selectBudgetId = $BudgetId;
        if($selectBudgetId <> ''){
            $details = Delivery::select([
                'deliveries.*',
                'items.ItemNameJp as ItemNameJp',
                'items.ItemNameEn as ItemNameEn',
                'order_requests.UnitPrice as UnitPrice'
            ])->leftjoin('items', function($join) {
                $join->on('deliveries.ItemId','=','items.id');
            })->leftjoin('order_requests', function($join) {
                $join->on('deliveries.OrderRequestId','=','order_requests.id');
            })->where('deliveries.BudgetId','=',$selectBudgetId)
            ->where('deliveries.DeliveryDate','>=',$startDate)
            ->where('deliveries.DeliveryDate','<=',$endDate)
            ->orderBy('deliveries.DeliveryDate')->get();
            foreach($details as $detail){
                $item = [
                    'ExecDate' => $detail->DeliveryDate,
                    'ItemNameJp' => $detail->ItemNameJp,
                    'ItemNameEn' => $detail->ItemNameEn,
                    'UnitPrice' => number_format($detail->UnitPrice),
                    'ExecNumber' => number_format($detail->DeliveryNumber),
                    'ExecPrice' => number_format($detail->DeliveryPrice),
                ];
                array_push($BudgetDetails,$item);
            }
            $details_minou = Order::select([
                'orders.*',
                'order_requests.UnitPrice as UnitPrice'
            ])->leftjoin('order_requests', function($join) {
                $join->on('orders.OrderRequestId','=','order_requests.id');
            })->where('orders.BudgetId','=',$selectBudgetId)
            ->where('orders.DeliveryProgress','<>',1)
            ->where('orders.OrderDate','>=',$startDate)
            ->where('orders.OrderDate','<=',$endDate)
            ->orderBy('orders.OrderDate')->get();
            foreach($details_minou as $minou){
                $minounumber = $minou->OrderNumber - $minou->DeliveryNumber;
                $item = [
                    'ExecDate' => '',
                    'ItemNameJp' => $minou->item->ItemNameJp,
                    'ItemNameEn' => $minou->item->ItemNameEn,
                    'UnitPrice' => number_format($minou->UnitPrice),
                    'ExecNumber' => number_format($minounumber),
                    'ExecPrice' => number_format($minou->UnitPrice * floatval($minounumber)),
                ];
                array_push($BudgetDetails,$item);
            }
        }
        return $BudgetDetails;
    }
    
    /*残高調整 */
    function balanceAdjustment(Request $request){

        $response = array();
        $response['status'] = 'OK';
        $response['errorMsg'] = '';

        DB::beginTransaction();
        try{
            /*商品データ作成*/
            $Item = new Item();
            $Item->ItemClass = 9; //9：残高調整
            $Item->ItemNameJp = $request->ExecRemark;
            $Item->ItemNameEn = $request->ExecRemark;
            $Item->MakerNameJp = "";
            $Item->MakerNameEn = "";
            $Item->CatalogCode = "";
            $Item->AmountUnit = "";
            $Item->Standard = "";
            $Item->CASNo = "";
            $Item->UnitPrice = 0;
            $Item->save();
            
            /*納品データ作成*/
            $Delivery = new Delivery();
            $Delivery->UserId = Auth::id();
            $Delivery->OrderSlipId = -1;
            $Delivery->OrderSlipNo = "";
            $Delivery->OrderId = -1;
            $Delivery->OrderRequestId = -1;
            $Delivery->BudgetId = $request->BudgetId;
            $Delivery->ItemId = $Item->id;
            $Delivery->ItemClass = 9;
            $Delivery->OrderDate = "";
            $Delivery->DeliveryDate = $request->ExecDate;
            $Delivery->DeliveryNumber = 0;
            $Delivery->DeliveryPrice = $request->ExecPrice;
            $Delivery->save();

            DB::commit();
        }
        catch(Exception $e){
            DB::rollback();
            $response['status'] = 'NG';
            $response['errorMsg'] = $e->getMessage();
        }

        return Response::json($response);
    }

    function outputCSV(Request $request){

        
        return response()->streamDownload(
            function() use($request) {

                $startDate = $request->startDate;
                $endDate = $request->endDate;

                $stream = fopen('php://output', 'w');
                stream_filter_prepend($stream,'convert.iconv.utf-8/cp932//TRANSLIT');
                fputcsv($stream, [
                    '予算科目',
                    '執行日',
                    '商品名',
                    '単価',
                    '数量',
                    '執行額'
                ]);

                $Budgets = Budget::where('useStartDate','<=',$startDate)->where('useEndDate','>=',$endDate)->get();               
                
                foreach($Budgets as $Budget){
                    $details = $this->getDetail_Data($Budget->id,$startDate,$endDate);
                    if (count($details) > 0){
                        foreach($details as $detail){
                            fputcsv($stream, [
                                $Budget->budgetNameJp,
                                $detail['ExecDate'],
                                $detail['ItemNameJp'],
                                $detail['UnitPrice'],
                                $detail['ExecNumber'],
                                $detail['ExecPrice']
                            ]);
                        }
                    }
                }

                fclose($stream);

            }, 
            'BudgetStatus.csv',
            [
                'Content-Type' => 'application/octet-stream',
            ]
        );
    }



}
