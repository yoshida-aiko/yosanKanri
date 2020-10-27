@extends('layouts.app')

@section('content')
<script src="{{ asset('js/supplierScript.js') }}" defer></script>
<div class="container">
<h5 class="master-title">発注先マスタ</h5>
<div class="wrapper">
    <div class="divMasterList master3columnList ">
        <table id="tblSupplierMasterList" class="table table-fixed table-masterFixed master3column table-striped">
            <thead>
                <th>&nbsp;</th>
                @if(App::getLocale()=='en') 
                <th>Supplier name</th>
                <th>Charge user</th>
                @else 
                <th>発注先</th>
                <th>担当者</th>
                @endif             
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
                $editSupplier->ChargeUserJp = old('ChargeUserJp');
                $editSupplier->Tel = old('Tel');
                $editSupplier->Fax = old('Fax');
                $editSupplier->EMail = old('EMail');
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
                <label for="SupplierNameJp" class="required">{{ App::getLocale()=='en' ? 'Supplier name(Jp)' : '発注先名(和名)'}}</label>
                <input type="text" id="SupplierNameJp" name="SupplierNameJp" value="{{ $editSupplier->SupplierNameJp }}" >
           </div>
           <div class="form-group">
                <label for="SupplierNameEn" id="lblSupplierNameEn">{{ App::getLocale()=='en' ? 'Supplier name(En)' : '発注先名(英名)'}}</label>
                <input type="text" id="SupplierNameEn" name="SupplierNameEn" value="{{ $editSupplier->SupplierNameEn }}" {{ session('bilingual') == 0 ? 'readonly' : '' }}>
           </div>
            <div class="form-group">
                <label for="ChargeUserJp">{{ App::getLocale()=='en' ? 'Charge user name(Jp)' : '担当者名(和名)'}}</label>
                <input type="text" id="ChargeUserJp" name="ChargeUserJp" value="{{ $editSupplier->ChargeUserJp }}" >
            </div>
            <div class="form-group">
                <label for="ChargeUserEn">{{ App::getLocale()=='en' ? 'Charge user name(En)' : '担当者名(英名)'}}</label>
                <input type="text" id="ChargeUserEn" name="ChargeUserEn" value="{{ $editSupplier->ChargeUserEn }}"  {{ session('bilingual')  == 0 ? 'readonly' : '' }}>
            </div>
            <div class="form-group">
                <label for="Tel">{{ App::getLocale()=='en' ? 'Tel' : '電話番号'}}</label>
                <input type="tel" id="Tel" name="SupplierTel" value="{{ $editSupplier->Tel }}">
            </div>
            <div class="form-group">
                <label for="Fax">{{ App::getLocale()=='en' ? 'Fax' : 'ファックス番号'}}</label>
                <input type="tel" id="Fax" name="Fax" value="{{ $editSupplier->Fax }}">
            </div>
            <div class="form-group">
                <label for="email" class="required">{{ App::getLocale()=='en' ? 'Email' : 'メールアドレス'}}</label>
                <input type="text" id="EMail" name="email" value="{{ $editSupplier->EMail }}" >
                <div class="alert-string" style="margin-left:150px;">※カンマで区切り、CCアドレスを指定できます</div>
            </div>
            <div class="form-group text-center">
                <button id="submit_supplier_regist" name="submit_supplier_regist" class="btn btn-primary" >{{ App::getLocale()=='en' ? 'Register' : '保存'}}</button>
                <input id="btn_supplier_clear" type="button" class="btn btn-secondary" value="{{ App::getLocale()=='en' ? 'Clear' : 'クリア'}}">
                <input type="hidden" id="id" name="id" value="{{ $editSupplier->id }}" >
                <input type="hidden" id="bilingual" name="bilingual" value="{{ session('bilingual') }}" >
            </div>

        </form>
    </div>
</div>
</div>
@endsection