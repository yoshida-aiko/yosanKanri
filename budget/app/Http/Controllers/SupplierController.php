<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;
use App\Supplier;
use App\Maker;
use App\Rules\Exists;
use App\Exceptions\ExclusiveLockException;
use App\Condition;
use App;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $Suppliers = Supplier::all();
        $editSupplier = new Supplier();
        $Condition = Condition::first();
        $bilingual = 0;
        if ($Condition != null) {
            $bilingual = $Condition->bilingual;
        }
        $request->session()->put('bilingual', $bilingual);

        return view('Supplier/index',compact('Suppliers','editSupplier'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $Supplier = new Supplier();

        return view('Supplier\create',compact('Supplier'));
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
            return redirect()->route('Supplier.index');
        }

        $isUpdate = false;

        if ($request->id != "")
        {
            $isUpdate = true;
        }

        $rules['SupplierNameJp'] = ['required', 'string', 'max:50'];
        if ($request->session()->get('bilingual') == "1") {
            $rules['SupplierNameEn'] = ['required', 'string', 'max:50'];
        }
        $rules['ChargeUserJp'] =  ['nullable', 'string', 'max:50'];
        if ($request->session()->get('bilingual') == "1") {
            $rules['ChargeUserEn'] = ['nullable', 'string', 'max:50'];
        }
        $rules['SupplierTel'] = ['nullable', 'string', 'max:20'];
        $rules['Fax'] = ['nullable', 'string', 'max:20'];
        $rules['email'] = ['required', 'string', 'max:100'];
        $this->validate($request, $rules);

        try {
            if ($isUpdate){
                $Supplier = Supplier::findOrFail($request->id);
            }
            else {
                $Supplier = new Supplier();
            }            
            $Supplier->SupplierNameJp = $request->SupplierNameJp;
            $Supplier->SupplierNameEn = $request->SupplierNameEn;
            $Supplier->ChargeUserJp = $request->ChargeUserJp;
            $Supplier->ChargeUserEn = $request->ChargeUserEn;
            $Supplier->Tel = $request->SupplierTel;
            $Supplier->Fax = $request->Fax;
            $Supplier->EMail = $request->email;

            $Supplier->save();
            $Suppliers = Supplier::all();
            $editSupplier = new Supplier();
            $bilingual = $request->bilingual;
            $status = true;

            return view('Supplier/index',compact('Suppliers','editSupplier','bilingual','status'));
        } catch (QueryException $e) {
            logger()->error("発注先マスタ保存処理　QueryException"); 
            throw $e;       
        }
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        try {
            $Suppliers = Supplier::all();
            $exists  = Supplier::where('id',$id)->exists();
            if (!$exists) {
                throw new ExclusiveLockException;
            }
               
            $editSupplier = Supplier::findOrFail($id); 
            $bilingual = $request->bilingual;

            return view('Supplier/index',compact('Suppliers','editSupplier','bilingual'));
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
        //メーカーマスタの優先する発注先に登録されていれば、メッセージを出力
        $exists = Maker::where('MainSupplierId',$id)->exists();
        if ($exists) {

            $msg = __('messages.makerSupplierIdError');
           
            return redirect()->back()->with('MainSupplierIdError', $msg);
        }
        $Supplier = Supplier::lockForUpdate()->withTrashed()->find($id);
        $Supplier->delete();

        return redirect()->route('Supplier.index');
    }
}
