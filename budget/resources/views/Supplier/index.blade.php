@extends('layouts.app')

@section('content')
<script src="{{ asset('js/supplierScript.js') }}" defer></script>
<div class="container">
<h5 class="master-title">発注先マスタ</h5>
<div class="wrapper">
    <div class="divMasterList">
        <table id="tblSupplierMasterList" class="table table-fixed table-masterFixed masterSupplier table-striped">
            <thead>
                <th></th>
                <th>発注先</th>
                <th>担当者</th>
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
                <td><a href="{{ route('Supplier.edit', $Supplier->id) }}">{{$Supplier->SupplierNameJp}}</a></td>
                <td>{{$Supplier->ChargeUserJp}}</td>
            </tr>
            @endforeach
            </tbody>

        </table>
    </div>
    <div class="divMasterInput">
        <form id="frmSupplierMaster" class="frmSupplierMasterInput" action="{{action('SupplierController@store')}}" method="POST">
            
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

            @csrf
            <div class="form-group">
                <label for="SupplierNameJp" class="required">発注先名</label>
                <input type="text" id="SupplierNameJp" name="SupplierNameJp" value="{{ $editSupplier->SupplierNameJp }}" >
           </div>
            <div class="form-group">
                <label for="ChargeUserJp">担当者名</label>
                <input type="text" id="ChargeUserJp" name="ChargeUserJp" value="{{ $editSupplier->ChargeUserJp }}" >
            </div>
            <div class="form-group">
                <label for="Tel">電話番号</label>
                <input type="tel" id="Tel" name="SupplierTel" value="{{ $editSupplier->Tel }}">
            </div>
            <div class="form-group">
                <label for="Fax">ファックス番号</label>
                <input type="tel" id="Fax" name="Fax" value="{{ $editSupplier->Fax }}">
            </div>
            <div class="form-group">
                <label for="email" class="required">メールアドレス</label>
                <input type="text" id="EMail" name="email" value="{{ $editSupplier->EMail }}" >
                <div class="alert-string" style="margin-left:150px;">※カンマで区切り、CCアドレスを指定できます</div>
            </div>
            <div class="form-group text-center">
                <button id="submit_supplier_regist" name="submit_supplier_regist" class="btn btn-primary" >保存</button>
                <input id="btn_supplier_clear" type="button" class="btn btn-secondary" value="クリア">
                <input type="hidden" id="id" name="id" value="{{ $editSupplier->id }}" >
            </div>

        </form>
    </div>
</div>
</div>
@endsection