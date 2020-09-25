<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Budget;
use Carbon\Carbon;

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
        $query = Budget::where('Status','=', 1);
        $query->where('fiscalYear','=', $Nendo);
        $Budgets = $query->get();

        $editBudget = new Budget();

        /*$thisYear = date('Y');       //本年度
        $fiscalYears = array();
        for ($year=2000; $year <= 2100 ; $year++) { 
            array_push($fiscalYears,$year);
        }
        
        if (!empty($Nendo)) {
            $query->where('fiscalYear','=', $Nendo);
            $thisYear = $Nendo;
        }else{
            $query->where('fiscalYear','=', '2020');
        }*/

        
        
        
        /*return view('Budget/index',compact('Budgets','editBudget','thisYear','fiscalYears'));*/

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

   /*  public function select($fiscalYear)
    {
        $Budgets = Budget::where('Status','=', 1)
        ->where('fiscalYear','=', $fiscalYear)
        ->get();

        $editBudget = new Budget();
        $thisYear = date('Y');       //本年度
        $fiscalYears = array();
        for ($year=2000; $year <= 2100 ; $year++) { 
            array_push($fiscalYears,$year);
        }
        
        return view('Budget/index',compact('Budgets','editBudget','thisYear','fiscalYears'));
    } */
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
            'budgetAmount' => ['required', 'double'],
            'useStartDate' => ['required','string', 'max:10'],
            'useEndDate' => ['required','string', 'max:10'],
            'displayOrder' => ['required', 'integer', 'max:11']
        ];
        $this->validate($request, $rules);

        if ($isUpdate){
            $Budget = Budget::findOrFail($request->id);
        }
        else {
            $Budget = new Budget();
        }
        $Budget->fiscalYear = $request->fiscalYear;
        $Budget->budgetNameJp = $request->budgetNameJp;
        $Budget->budgetAmount = $request->budgetAmount;
        $Budget->useStartDate = $request->useStartDate;
        $Budget->useEndDate = $request->useEndDate;
        $Budget->displayOrder = $request->displayOrder;

        $Budget->save();
        $Budgets = Budget::where('Status','=', 1)->get();
        $editBudget = new Budget();

        return view('Budget/index',compact('Budgets','editBudget'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,Request $request)
    {
        $Budgets = Budget::where('Status','=', 1)
        // ->where('fiscalYear','=', $request->fiscalYear)
        ->get();
        $editBudget = Budget::findOrFail($id);
        $thisYear = date('Y');       //本年度
        $fiscalYears = array();
        for ($year=2000; $year <= 2100 ; $year++) { 
            array_push($fiscalYears,$year);
        }
        return view('Budget/index',compact('Budgets','editBudget','thisYear','fiscalYears'));
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
            'budgetAmount' => ['required', 'double'],
            'useStartDate' => ['required','string', 'max:10'],
            'useEndDate' => ['required','string', 'max:10'],
            'displayOrder' => ['required', 'integer', 'max:11']
        ];
        $this->validate($request, $rules);

        $Budget = Budget::findOrFail($id);
        $Budget->fiscalYear = $request->fiscalYear;
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
