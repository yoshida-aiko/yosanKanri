<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Query;
use Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Library\BaseClass;
use App\OrderRequest;
use App\Order;
use App\Budget;
use App\Delivery;
use App\Supplier;
use App\OrderSlip;
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
            $startDate = $today->subMonthsNoOverflow(3)->format('Y').'/04/01';
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
            $remain_D = Delivery::where('BudgetId','=',$Budget->id)
                        ->where('DeliveryDate','>=',$startDate)
                        ->where('DeliveryDate','<=',$endDate)->sum('DeliveryPrice');
            
                        //dd(DB::getQueryLog());

            $remain_O = Order::where('BudgetId','=',$Budget->id)
                        ->where('OrderDate','>=',$startDate)
                        ->where('OrderDate','<=',$endDate)->sum(DB::raw('UnitPrice * (OrderNumber - DeliveryNumber)'));
            
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
                'BudgetNameJp' => $Budget->budgetNameJp,
                'Budget' => number_format($Budget->budgetAmount),
                'BudgetUsed' => number_format($BudgetUsed),
                'BudgetScheduled' => number_format($BudgetScheduled),
                'BudgetRemainBal' => number_format($BudgetRemainBal),
                'BudgetScheduledRemain' => number_format($BudgetScheduledRemain)
            ];
            array_push($BudgetLists,$item);
        }

        $BudgetDetails = array();
        $selectBudgetId = $request->BudgetId;
        if($selectBudgetId <> ''){
            $details = Delivery::where('BudgetId','=',$selectBudgetId)->get();
            foreach($details as $detail){
                $item = [
                    'ExecDate' => $detail->DeliveryDate,
                    'ItemNameJp' => $detail->item->ItemNameJp,
                    'ItemNameEn' => $detail->item->ItemNameEn,
                    'UnitPrice' => number_format($detail->item->UnitPrice),
                    'ExecNumber' => number_format($detail->DeliveryNumber),
                    'ExecPrice' => number_format($detail->item->UnitPrice * floatval($detail->DeliveryNumber)),
                ];
                array_push($BudgetDetails,$item);
            }
        }

        return view('BudgetStatus/index',compact('BudgetLists','BudgetDetails'));
    }
}
