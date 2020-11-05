@extends('layouts.app')

@section('content')
<script src="{{ asset('js/makerScript.js') }}" defer></script>
<div class="container">
<h5 class="master-title">{{__('screenwords.master_maker')}}</h5>
<div class="wrapper">
    <div class="divMasterList master3columnList">
        <table id="tblMakerMasterList" class="table table-fixed table-masterFixed master3column table-striped">
            <thead>
                <th>&nbsp;</th>
                <th>{{__('screenwords2.makerName')}}</th>
                <th>{{__('screenwords2.prioritySupplier')}}</th>
            </thead>
            <tbody>
            @foreach($Makers as $Maker)
            <tr>
                <td>
                    <form id="frmMakerDelete" action="{{ route('Maker.destroy', $Maker->id) }}" method='post'>
                        @csrf
                        @method('DELETE')
                        @if ($Maker->CatalogUseFlag == "0")
                            <input type="submit" value="&#xf1f8;" 
                                onClick="if (!confirm('{{ __('messages.confirmDelete') }}')){ return false;} return true;" class="fa btn-delete-icon">
                        @else
                            <input type="submit" value="&#xf1f8;" 
                                onClick="if (!confirm('{{ __('messages.confirmDelete') }}')){ return false;} return true;" class="btn-delete-icon resetLinkOff">
                        @endif
                    </form>
                </td>
                <td><a href="{{ route('Maker.edit', $Maker->id) }}">{{ App::getLocale()=='en' ? $Maker->MakerNameEn : $Maker->MakerNameJp}}</a></td>
                <td>{{ App::getLocale()=='en' 
                    ? $Maker->supplier['SupplierNameEn'] ?? ''
                    : $Maker->supplier['SupplierNameJp'] ?? ''}}
                </td>
            </tr>
            @endforeach
            </tbody>

        </table>
    </div>
    <div class="divMasterInput">
        <form id="frmMakerMaster" class="frmMasterInput" action="{{action('MakerController@store')}}" method="POST">
            
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
                $editMaker->MakerNameEn = old('MakerNameEn');
                ?>
            @endif
            @if (session('exclusiveError'))
            <div class="alert alert-danger">
                <ul>
                    <li>{{ session('exclusiveError') }}</li>
                </ul>
            </div>
            @endif
            
            @csrf
            <div class="form-group">
                <label for="MakerNameJp" class="required">{{__('screenwords2.makerNameJp')}}</label>
                <input type="text" id="MakerNameJp" name="MakerNameJp" value="{{ $editMaker->MakerNameJp }}" >
           </div>
           <div class="form-group">
                <label for="MakerNameEn" id="lblMakerNameEn">{{__('screenwords2.makerNameEp')}}</label>
                <input type="text" id="MakerNameEn" name="MakerNameEn" value="{{ $editMaker->MakerNameEn }}"  {{ session('bilingual') == 0 ? 'readonly' : '' }}>
           </div>
            <div class="form-group">
                <label for="SupplierNameJp">{{__('screenwords2.prioritySupplier')}}</label>
                <select name="MainSupplierId">
                @foreach($Suppliers as $Supplier)
                    <option value="{{ $Supplier->id }}" {{$editMaker->MainSupplierId == $Supplier->id ? 'selected' : '' }}>
                        {{ App::getLocale()=='en' ? $Supplier->SupplierNameEn : $Supplier->SupplierNameJp}}
                    </option>
                @endforeach
                </select>
            </div>
            <div class="form-group text-center">
                <button id="submit_Maker_regist" name="submit_Maker_regist" class="btn btn-primary" >{{__('screenwords2.register')}}</button>
                <input id="btn_Maker_clear" type="button" class="btn btn-secondary" value="{{__('screenwords2.clear')}}">
                <input type="hidden" id="id" name="id" value="{{ $editMaker->id }}" >
                <input type="hidden" id="bilingual" name="bilingual" value="{{ session('bilingual') }}" >
            </div>


        </form>

    </div>
</div>
</div>
@endsection