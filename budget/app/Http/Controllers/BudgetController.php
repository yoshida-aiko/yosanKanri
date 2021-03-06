<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;
use App\Budget;
use Carbon\Carbon;
use App\Rules\Exists;
use App\Exceptions\ExclusiveLockException;
use App\Condition;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $Nendo = 0;
        // 年度受け取り
        if ($request->has('fiscalYear')){
            $Nendo = $request->fiscalYear;
        }
        else {
            $today = Carbon::today();

            $Nendo = $today->year;
            if ($Nendo < 4) {
                $Nendo = $Nendo - 1;
            }
        }

        $Budgets = Budget::where('fiscalYear','=', $Nendo)
                ->orderByRaw('displayOrder IS NULL ASC')
                ->orderBy('displayOrder', 'asc')
                ->get();

        foreach ($Budgets as $Budget) {
            $Budget->budgetAmount = number_format($Budget->budgetAmount);
        }
        
        $editBudget = new Budget();

        //設定マスタよりバイリンガル取得
        $Condition = Condition::first();
        $bilingual = 0;
        if ($Condition != null) {
            $bilingual = $Condition->bilingual;
        }
        $request->session()->put('bilingual', $bilingual);

        return view('Budget/index',compact('Budgets','editBudget','Nendo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $Budget = new Budget();

        return view('Budget\create',compact('Budget'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->action === 'back') {
            return redirect()->route('Budget.index');
        }

        $isUpdate = false;

        if ($request->id != "")
        {
            $isUpdate = true;
        }

        $rules['budgetNameJp'] = ['required', 'string', 'max:50'];
        if ($request->session()->get('bilingual') == "1") {
            $rules['budgetNameEn'] = ['required', 'string', 'max:50'];
        }
        $rules['budgetAmount'] = ['required', 'numeric'];
        $rules['useStartDate'] = ['required','date', 'date_format:Y/m/d','max:10'];
        $rules['useEndDate'] = ['required','date', 'date_format:Y/m/d','after:yesterday','max:10'];
        $rules['displayOrder'] = ['nullable', 'integer'];
        $this->validate($request, $rules);
        
        try {
            if ($isUpdate){
                $Budget = Budget::findOrFail($request->id);
            }
            else {
                $Budget = new Budget();
            }
            $Nendo = $request->year;
            $Budget->fiscalYear = $Nendo;
            $Budget->budgetNameJp = $request->budgetNameJp;
            $Budget->budgetNameEn = $request->budgetNameEn;
            $Budget->budgetAmount = $request->budgetAmount;
            $Budget->useStartDate = $request->useStartDate;
            $Budget->useEndDate = $request->useEndDate;
            $Budget->displayOrder = $request->displayOrder;

            $Budget->save();
            $Budgets = Budget::where('fiscalYear','=', $Nendo)
                    ->orderByRaw('displayOrder IS NULL ASC')
                    ->orderBy('displayOrder', 'asc')
                    ->get();
            foreach ($Budgets as $Budget) {
                $Budget->budgetAmount = number_format($Budget->budgetAmount);
            }
            
            $editBudget = new Budget();
            $status = true;

            return view('Budget/index',compact('Budgets','editBudget','Nendo','status'));

        } catch (QueryException $e) {
            logger()->error("予算マスタ保存処理　QueryException"); 
            throw $e;       
        }      
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,Request $request)
    {
        try {
            $exists  = Budget::where('id',$id)->exists();
            if (!$exists) {
                throw new ExclusiveLockException;
            }
            // 年度　初期値設定
            $today = Carbon::today();
            $Nendo = $today->year;
            if ($Nendo < 4) {
                $Nendo = $Nendo - 1;
            }
            // Budgetテーブル情報取得
            $editBudget = Budget::findOrFail($id);  
            $Nendo = $editBudget->fiscalYear;
            $Budgets = Budget::where('fiscalYear','=', $Nendo)
                    ->orderByRaw('displayOrder IS NULL ASC')
                    ->orderBy('displayOrder', 'asc')
                    ->get();

            foreach ($Budgets as $Budget) {
                $Budget->budgetAmount = number_format($Budget->budgetAmount);
            }

            return view('Budget/index',compact('Budgets','editBudget','Nendo'));
        } catch (ExclusiveLockException $e) {
            throw $e;
        }        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Budget = Budget::lockForUpdate()->withTrashed()->find($id);
        $Budget->delete();

        return redirect()->route('Budget.index');
    }

}
