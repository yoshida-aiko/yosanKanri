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
            'useStartDate' => ['required','string', 'max:10'],
            'useEndDate' => ['required','string', 'max:10'],
            'displayOrder' => ['nullable', 'integer', 'max:11']
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
        $Budgets = Budget::where('Status','=', 1)
                ->where('fiscalYear','=', $Nendo)
                ->get();
        $editBudget = new Budget();

        return view('Budget/index',compact('Budgets','editBudget','Nendo'));
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

        $editBudget = Budget::findOrFail($id);

        return view('Budget/index',compact('Budgets','editBudget','Nendo'));
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
