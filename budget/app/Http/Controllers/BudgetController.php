<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Budget;
use Carbon\Carbon;
use App\Rules\Exists;
use App\Exceptions\ExclusiveLockException;

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

        $rules = [
            'budgetNameJp' => ['required', 'string', 'max:50'],
            'budgetAmount' => ['required', 'numeric'],
            'useStartDate' => ['required','date', 'date_format:Y/m/d','max:10'],
            'useEndDate' =>  ['required','date', 'date_format:Y/m/d','after:yesterday','max:10'],
            'displayOrder' => ['nullable', 'integer']
        ];
        $this->validate($request, $rules);

        if ($isUpdate){
            $Budget = Budget::findOrFail($request->id);
        }
        else {
            $Budget = new Budget();
        }
        $Nendo = $request->year;
        $Budget->fiscalYear = $Nendo;
        $Budget->budgetNameJp = $request->budgetNameJp;
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

        return view('Budget/index',compact('Budgets','editBudget','Nendo'));
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'budgetNameJp' => ['required', 'string', 'max:50'],
            'budgetAmount' => ['required', 'numeric'],
            'useStartDate' => ['required','date', 'date_format:Y/m/d','max:10'],
            'useEndDate' =>  ['required','date', 'date_format:Y/m/d','after:yesterday','max:10'],
            'displayOrder' => ['nullable', 'integer']
        ];
        $this->validate($request, $rules);

        $Budget = Budget::findOrFail($id);
        $Budget->fiscalYear = $request->year;
        $Budget->budgetNameJp = $request->budgetNameJp;
        $Budget->budgetAmount = $request->budgetAmount;
        $Budget->useStartDate = $request->useStartDate;
        $Budget->useEndDate = $request->useEndDate;
        $Budget->displayOrder = $request->displayOrder;
        $Budget->save();
 
        $editBudget = new Budget();

        return view('Budget/index',compact('Budgets','editBudget'));
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
