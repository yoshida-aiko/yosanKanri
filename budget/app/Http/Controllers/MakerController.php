<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Maker;
use App\Supplier;
use App\Rules\Exists;
use App\Exceptions\ExclusiveLockException;

class MakerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Makers = Maker::with(['supplier'])->get();

        $editMaker = new Maker();
        //発注先取得
        $Suppliers = Supplier::select('id','SupplierNameJp')->get();
        return view('Maker/index',compact('Makers','editMaker','Suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $Maker = new Maker();

        return view('Maker/create',compact('Maker'));
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
            return redirect()->route('Maker.index');
        }

        $isUpdate = false;

        if ($request->id != "")
        {
            $isUpdate = true;
        }

        $rules = [
            'MakerNameJp' => ['required', 'string', 'max:255'],
        ];
        $this->validate($request, $rules);

        if ($isUpdate){
            $Maker = Maker::findOrFail($request->id);
        }
        else {
            $Maker = new Maker();
        }
        $Maker->MakerNameJp = $request->MakerNameJp;
        $Maker->MainSupplierId = $request->MainSupplierId;

        $Maker->save();
        $Makers = Maker::with(['supplier'])->get();
        $editMaker = new Maker();
        $Suppliers = Supplier::select('id','SupplierNameJp')->get();

        return view('Maker/index',compact('Makers','editMaker','Suppliers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $Makers = Maker::with(['supplier'])->get();
            $exists  = Maker::where('id',$id)->exists();
            if (!$exists) {
                throw new ExclusiveLockException;
            }
            $editMaker = Maker::findOrFail($id);
            $Suppliers = Supplier::select('id','SupplierNameJp')->get();
        
            return view('Maker/index',compact('Makers','editMaker','Suppliers'));
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
            'MakerNameJp' => ['required', 'string', 'max:255'],
        ];
        $this->validate($request, $rules);

        $Maker = Maker::findOrFail($id);
        $Maker->MakerNameJp = $request->MakerNameJp;
        $Maker->MainSupplierId = $request->MainSupplierId;
        $Maker->save();
 
        $editMaker = new Maker();
        $Suppliers = Supplier::select('id','SupplierNameJp')->get();

        return view('Maker/index',compact('Makers','editMaker','Suppliers'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Maker = Maker::lockForUpdate()->withTrashed()->find($id);
        $Maker->delete();

        return redirect()->route('Maker.index');
    }
}
