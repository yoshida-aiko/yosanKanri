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
       
    </div>
</div>
</div>
@endsection