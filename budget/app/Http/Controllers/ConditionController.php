<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Hash;
use App\Condition;


class ConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $Condition = Condition::first();
        if ($Condition == null) {
            $Condition = new Condition();
            $Condition->FiscalStartMonth = 4;
            $Condition->NewBulletinTerm = 5;
            $Condition->BulletinTerm = 30;
            $Condition->bilingual = 0;
            $Condition->SMTPServerPort = 25;
            $Condition->SMTPAuthFlag = 0;
            $Condition->ExecutionBasis = 1;
        } 

        return view('Condition/index',compact('Condition'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $Condition = new Condition();

        return view('Condition\create',compact('Condition'));
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
            return redirect()->route('Condition.index');
        }

        $isUpdate = false;

        $Condition = Condition::first();
        if ($Condition != null) {
            $isUpdate = true;
        }

        if (!$isUpdate){
            $Condition = new Condition(); 
        }

        $rules = [
            'SystemNameJp' => ['required', 'string', 'max:50'],
            'FiscalStartMonth' => ['required', 'integer'],
            'BulletinTerm' => ['required', 'integer'],
            'NewBulletinTerm' => ['required', 'integer'],
            'email' => ['required', 'string', 'max:100'],
            'SMTPServerId' => ['required', 'string', 'max:100'],
            'SMTPServerPort' => ['required', 'integer'],
        ];
        if ($request['bilingual'] == "1") {
            $rules['SystemNameEn'] = ['required', 'string', 'max:50'];
        }
        if ($request['SMTPAuthFlag'] == "1") {
            $rules['SMTPAccount'] = ['required', 'string', 'max:100'];
            $rules['SMTPPassword'] = ['required', 'string', 'min:50'];
        }
        $this->validate($request, $rules);

        $Condition->VersionNo = 0;
        $Condition->bilingual = $request->bilingual;
        $Condition->SystemNameJp = $request->SystemNameJp;
        $Condition->SystemNameEn = $request->SystemNameEn;
        $Condition->FiscalStartMonth = $request->FiscalStartMonth;
        $Condition->NewBulletinTerm = $request->NewBulletinTerm;
        $Condition->BulletinTerm = $request->BulletinTerm;
        $Condition->SMTPServerId = $request->SMTPServerId;
        $Condition->SMTPServerPort = $request->SMTPServerPort;
        $Condition->SMTPAccount = $request->SMTPAccount;
        // $Condition->SMTPPassword = Hash::make($request->SMTPPassword);
        $Condition->SMTPPassword = $request->SMTPPassword;
        if ($request->SMTPAuthFlag =="") {
            $Condition->SMTPAuthFlag = 0;
        }else {
            $Condition->SMTPAuthFlag = 1;
        }
        
        $Condition->SMTPConnectMethod = 2;
        $Condition->Organization = "インフォグラム";
        $Condition->Department = "福岡本社";
        $Condition->EMail = $request->email;
        $Condition->ExecutionBasis = $request->ExecutionBasis;
       
        $Condition->save();

        return view('Condition/index',compact('Condition'));
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
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Condition = Condition::first();
        if ($Condition == null) {
            $Condition = new Condition();
            $Condition->FiscalStartMonth = 4;
            $Condition->NewBulletinTerm = 5;
            $Condition->BulletinTerm = 30;
            $Condition->bilingual = 0;
            $Condition->SMTPServerPort = 25;
            $Condition->SMTPAuthFlag = 0;
            $Condition->ExecutionBasis = 1;
        } 

        return view('Condition/index',compact('Condition'));
    }
}
