<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Supplier;
use App\Maker;
use App\Rules\Exists;
use App\Exceptions\ExclusiveLockException;
use App\Condition;

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

        $rules = [
            'SupplierNameJp' => ['required', 'string', 'max:50'],
            'ChargeUserJp' => ['nullable', 'string', 'max:50'],
            'SupplierTel' => ['nullable','string', 'max:20'],
            'Fax' => ['nullable','string', 'max:20'],
            'email' => ['required', 'string', 'max:100']
        ];
        if ($request->session()->has('bilingual') == "1") {
            $rules['SupplierNameEn'] = ['required', 'string', 'max:50'];
            $rules['ChargeUserEn'] = ['nullable', 'string', 'max:50'];
        }
        $this->validate($request, $rules);

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

        return view('Supplier/index',compact('Suppliers','editSupplier','bilingual'));
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'SupplierNameJp' => ['required', 'string', 'max:50'],
            'ChargeUserJp' => ['string', 'min:50'],
            'SupplierTel' => ['string', 'max:20'],
            'Fax' => ['string', 'max:20'],
            'email' => ['required', 'string', 'max:100']
        ];
        if ($request->session()->has('bilingual') == "1") {
            $rules['SupplierNameEn'] = ['required', 'string', 'max:50'];
            $rules['ChargeUserEn'] = ['nullable', 'string', 'max:50'];
        }
        $this->validate($request, $rules);

        $Supplier = Supplier::findOrFail($id);
        $Supplier->SupplierNameJp = $request->SupplierNameJp;
        $Supplier->SupplierNameEn = $request->SupplierNameEn;
        $Supplier->ChargeUserJp = $request->ChargeUserJp;
        $Supplier->Tel = $request->SupplierTel;
        $Supplier->Fax = $request->Fax;
        $Supplier->EMail = $request->email;
        $Supplier->save();
 
        $editSupplier = new Supplier();
        $bilingual = $request->bilingual;

        return view('Supplier/index',compact('Suppliers','editSupplier','bilingual'));
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
            return redirect()->back()->with('MainSupplierIdError', '優先する発注先に指定しているメーカーがある為、削除できません');
        }
        $Supplier = Supplier::lockForUpdate()->withTrashed()->find($id);
        $Supplier->delete();

        return redirect()->route('Supplier.index');
    }
}
