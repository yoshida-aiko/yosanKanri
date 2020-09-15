@extends('layouts.app')

@section('content')
<script src="{{ asset('js/makerScript.js') }}" defer></script>
<div class="container">
<h5 class="master-title">メーカーマスタ</h5>
<div class="wrapper">
    <div class="divMasterList">
        <table id="tblMakerMasterList" class="table table-fixed table-masterFixed masterSupplier table-striped">
            <thead>
                <th></th>
                <th>メーカー名</th>
                <th>優先する発注先</th>
            </thead>
            <tbody>
            @foreach($Makers as $Maker)
            <tr>
                <td>
                    <form id="frmMakerDelete" action="{{ route('Maker.destroy', $Maker->id) }}" method='post'>
                        @csrf
                        @method('DELETE')
                        <input type="submit" value="&#xf1f8;" 
                            onClick="if (!confirm('削除しますか？')){ return false;} return true;" class="fa btn-delete-icon">
                    </form>
                </td>
                <td><a href="{{ route('Maker.edit', $Maker->id) }}">{{$Maker->MakerNameJp}}</a></td>
                <td>{{$Maker->supplier->SupplierNameJp}}</td>
            </tr>
            @endforeach
            </tbody>

        </table>
    </div>
    <div class="divMasterInput">
    <form id="frmMakerMaster" class="frmMakerMasterInput" action="{{action('MakerController@store')}}" method="POST">
            
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
                $editMaker->id = old('id');
                $editMaker->MakerNameJp = old('MakerNameJp');
                ?>
            @endif

            @csrf
            <div class="form-group">
                <label for="MakerNameJp" class="required">発注先名</label>
                <input type="text" id="MakerNameJp" name="MakerNameJp" value="{{ $editMaker->MakerNameJp }}" >
           </div>
            <div class="form-group">
                <label for="ChargeUserJp">優先する発注先</label>
                <select>
                @foreach($Suppliers as $Supplier)
                    @if ($editMaker->MainSupplierId == $Supplier->id)
                        <option value="{{ $Supplier->id }}" selected>{{$Supplier->SupplierNameJp}}</option>
                    @else
                        <option value="{{ $Supplier->id }}">{{$Supplier->SupplierNameJp}}</option>
                    @endif
                @endforeach
                </select>
            </div>
            <div class="form-group text-center">
                <button id="submit_Maker_regist" name="submit_Maker_regist" class="btn btn-primary" >保存</button>
                <input id="btn_Maker_clear" type="button" class="btn btn-secondary" value="クリア">
                <input type="hidden" id="id" name="id" value="{{ $editMaker->id }}" >
            </div>


        </form>

    </div>
</div>
</div>
@endsection