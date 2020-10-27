@extends('layouts.app')

@section('content')
<script src="{{ asset('js/supplierScript.js') }}" defer></script>
<div class="container">
<h5 class="master-title">{{__('screenwords.master_supplier')}}</h5>
<div class="wrapper">
    <div class="divMasterList master3columnList ">
        <table id="tblSupplierMasterList" class="table table-fixed table-masterFixed master3column table-striped">
            <thead>
                <th>&nbsp;</th>
                <th>{{__('screenwords2.supplierName')}}</th>
                <th>{{__('screenwords2.chargeUser')}}</th>
            </thead>
            <tbody>
            @foreach($Suppliers as $Supplier)
            <tr>
                <td>
                    <form id="frmSupplierDelete" action="{{ route('Supplier.destroy', $Supplier->id) }}" method='post'>
                        @csrf
                        @method('DELETE')
                        <input type="submit" value="&#xf1f8;" 
                            onClick="if (!confirm('削除しますか？')){ return false;} return true;" class="fa btn-delete-icon">
                    </form>
                </td>
                <td><a href="{{ route('Supplier.edit', $Supplier->id) }}">{{ App::getLocale()=='en' ? $Supplier->SupplierNameEn : $Supplier->SupplierNameJp}}</a></td>
                <td>{{ App::getLocale()=='en' ? $Supplier->ChargeUserEn : $Supplier->ChargeUserJp}}</td>
            </tr>
            @endforeach
            </tbody>

        </table>
    </div>
    <div class="divMasterInput">
        <form id="frmSupplierMaster" class="frmMasterInput" action="{{action('SupplierController@store')}}" method="POST">
            
            {{-- エラーメッセージ --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                </div>
                <?php
                $editSupplier->SupplierNameJp = old('SupplierNameJp');
                $editSupplier->SupplierNameEn = old('SupplierNameEn');
                $editSupplier->ChargeUserJp = old('ChargeUserJp');
                $editSupplier->ChargeUserEn = old('ChargeUserEn');
                $editSupplier->Tel = old('Tel');
                $editSupplier->Fax = old('Fax');
                $editSupplier->EMail = old('email');
                ?>
            @endif
            @if (session('exclusiveError'))
            <div class="alert alert-danger">
                <ul>
                    <li>{{ session('exclusiveError') }}</li>
                </ul>
            </div>
            @endif
            @if (session('MainSupplierIdError'))
            <div class="alert alert-danger">
                <ul>
                    <li>{{ session('MainSupplierIdError') }}</li>
                </ul>
            </div>
            @endif

            @csrf
            <div class="form-group">
                <label for="SupplierNameJp" class="required">{{__('screenwords2.supplierNameJp')}}</label>
                <input type="text" id="SupplierNameJp" name="SupplierNameJp" value="{{ $editSupplier->SupplierNameJp }}" >
           </div>
           <div class="form-group">
                <label for="SupplierNameEn" id="lblSupplierNameEn">{{__('screenwords2.supplierNameEn')}}</label>
                <input type="text" id="SupplierNameEn" name="SupplierNameEn" value="{{ $editSupplier->SupplierNameEn }}" {{ session('bilingual') == 0 ? 'readonly' : '' }}>
           </div>
            <div class="form-group">
                <label for="ChargeUserJp">{{__('screenwords2.chargeUserJp')}}</label>
                <input type="text" id="ChargeUserJp" name="ChargeUserJp" value="{{ $editSupplier->ChargeUserJp }}" >
            </div>
            <div class="form-group">
                <label for="ChargeUserEn">{{__('screenwords2.chargeUserEn')}}</label>
                <input type="text" id="ChargeUserEn" name="ChargeUserEn" value="{{ $editSupplier->ChargeUserEn }}"  {{ session('bilingual')  == 0 ? 'readonly' : '' }}>
            </div>
            <div class="form-group">
                <label for="Tel">{{__('screenwords2.tel')}}</label>
                <input type="tel" id="Tel" name="SupplierTel" value="{{ $editSupplier->Tel }}">
            </div>
            <div class="form-group">
                <label for="Fax">{{__('screenwords2.fax')}}</label>
                <input type="tel" id="Fax" name="Fax" value="{{ $editSupplier->Fax }}">
            </div>
            <div class="form-group">
                <label for="email" class="required">{{__('screenwords2.eMail')}}</label>
                <input type="text" id="EMail" name="email" value="{{ $editSupplier->EMail }}" >
                <div class="alert-string" style="margin-left:150px;">{{__('screenwords2.eMailAnnotation')}}</div>
            </div>
            <div class="form-group text-center">
                <button id="submit_supplier_regist" name="submit_supplier_regist" class="btn btn-primary" >{{__('screenwords2.register')}}</button>
                <input id="btn_supplier_clear" type="button" class="btn btn-secondary" value="{{__('screenwords2.clear')}}">
                <input type="hidden" id="id" name="id" value="{{ $editSupplier->id }}" >
                <input type="hidden" id="bilingual" name="bilingual" value="{{ session('bilingual') }}" >
            </div>

        </form>
    </div>
</div>
</div>
@endsection