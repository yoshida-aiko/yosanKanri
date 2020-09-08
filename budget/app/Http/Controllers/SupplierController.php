<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Supplier;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Suppliers = Supplier::where('Status','=', 1)->get();
        $editSupplier = new Supplier();

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
            'ChargeUserJp' => ['nullable', 'string', 'min:50'],
            'Tel' => ['nullable','string', 'max:20'],
            'Fax' => ['nullable','string', 'max:20'],
            'EMail' => ['required', 'string', 'email', 'max:100', Rule::unique('suppliers', 'EMail')->whereNull('deleted_at')->whereNot('EMail', $request->EMail)]
        ];
        $this->validate($request, $rules);

        if ($isUpdate){
            $Supplier = Supplier::findOrFail($request->id);
        }
        else {
            $Supplier = new Supplier();
        }
        $Supplier->SupplierNameJp = $request->SupplierNameJp;
        $Supplier->ChargeUserJp = $request->ChargeUserJp;
        $Supplier->Tel = $request->Tel;
        $Supplier->Fax = $request->Fax;
        $Supplier->EMail = $request->EMail;

        $Supplier->save();
        $Suppliers = Supplier::where('Status','=', 1)->get();
        $editSupplier = new Supplier();

        return view('Supplier/index',compact('Suppliers'));
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
        $Suppliers = Supplier::where('Status','=', 1)->get();
        $editSupplier = Supplier::findOrFail($id);
        return view('Supplier/index',compact('Suppliers','editSupplier'));
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
            'Tel' => ['string', 'max:20'],
            'Fax' => ['string', 'max:20'],
            'EMail' => ['required', 'string', 'email', 'max:100']
        ];
        $this->validate($request, $rules);

        $Supplier = Supplier::findOrFail($id);
        $Supplier->SupplierNameJp = $request->SupplierNameJp;
        $Supplier->ChargeUserJp = $request->ChargeUserJp;
        $Supplier->Tel = $request->Tel;
        $Supplier->Fax = $request->Fax;
        $Supplier->EMail = $request->EMail;
        $Supplier->save();
 
        $editSupplier = new Supplier();

        return view('Supplier/index',compact('Suppliers','editSupplier'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Supplier = Supplier::findOrFail($id);
        $Supplier->delete();

        return redirect()->route('Supplier.index');
    }
}
