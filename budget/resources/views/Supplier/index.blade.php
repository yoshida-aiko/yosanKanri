@extends('layouts.app')

@section('content')
<script src="{{ asset('js/supplierScript.js') }}" defer></script>
<div class="container">
<h5 class="master-title">発注先マスタ</h5>
<div class="wrapper">
    <div class="divMasterList">
        <table id="tblSupplierMasterList" class="table table-fixed table-masterFixed table-striped">
            <thead>
                <th></th>
                <th style="min-width:120px; width:280px">発注先</th>
                <th style="min-width:120px; ">担当者</th>
            </thead>
            <tbody>
            
            </tbody>

            </table>
    </div>
    <div class="divMasterInput">

    </div>
</div>
</div>
@endsection