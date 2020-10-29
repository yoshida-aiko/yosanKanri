@extends('layouts.app')

@section('content')
<script src="{{ asset('js/orderRequestScript.js') }}" defer></script>

<div class="container">
<div class="loading"><div class="loadingwrapper"><div class="ball-grid-pulse"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><div class="loadingtext">loading...</div></div></div>
<div class="wrapper">

    <div class="leftside-fixed-240">
        @component('components.favorite')
            @slot('url','OrderRequest.update')
            @slot('itemClass',1)
        @endcomponent
    </div>
    <div class="toggle-fixed-37">
        <div id="toggle-button-favorite"></div>
    </div>
    <div class="flexmain" >
        <h6 class="h6-title">{{ __('screenwords.orderRequestList') }}</h6>
        <div class="divOrderRequestHeaderButton">
            <input type="button" id="btnNewProduct" value="{{ __('screenwords.newItemInput') }}" title="カタログにない商品を登録します" class="btn btn-secondary" >
            <label for="selTooRequest">{{ __('screenwords.requestDestination') }}：</label>
                <select id="selOrderRequestUser">
                    <option value="-1">{{ __('screenwords.all') }}</option>
                    @foreach ($Users as $User)
                        <option value="{{ $User->id}}">
                        @if(App::getLocale()=='en') {{$User->UserNameEn}}
                        @else {{$User->UserNameJp}}
                        @endif
                        </option>
                    @endforeach
                </select>
            <input type="button" id="btnOrderRequest" value="{{ __('screenwords.orderRequest') }}" title="{{ __('screenwords.orderRequestTooltip') }}" class="btn btn-primary" @if($Carts->isEmpty()) disabled='disabled' @endif >
        </div>
        <div class="pagenationStyle">
        @if(!$Carts->isEmpty())
            {{$Carts->appends(request()->query())->links()}}    
        @endif
        </div>        
        <table id="table-orderRequestFixed" class="table table-fixed table-orderRequestFixed table-striped">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th><input type="checkbox" name="chkTargetAll" @if(!$Carts->isEmpty()) checked @endif ></th>
                    <th class="align-center ">@sortablelink('ItemClass',__('screenwords.type'))</th>
                    <th>@sortablelink(__('screenwords.sortItemName'),__('screenwords.itemName'))</th>
                    <th class="align-center ">@sortablelink('AmountUnit',__('screenwords.capacity'))</th>
                    <th class="align-center ">@sortablelink('CatalogCode',__('screenwords.catalogCode'))</th>
                    <th>@sortablelink(__('screenwords.sortMakerName'),__('screenwords.makerName'))</th>
                    <th class="align-center ">@sortablelink('UnitPrice',__('screenwords.unitPrice'))</th>
                    <th class="align-center ">@sortablelink('OrderRequestNumber',__('screenwords.quantity'))</th>
                    <th class="align-center ">@sortablelink('OrderPrice',__('screenwords.amount'))</th>
                    <th> {{ __('screenwords.remark') }} </th>
                </tr>
            </thead>
            <tbody>
            @if(!$Carts->isEmpty())
            @foreach ($Carts as $Cart)
                <tr class="table-orderRequestFixed-tr">
                    <td>
                        <form action="{{ route('OrderRequest.destroy', $Cart->id) }}" method='post'>
                            @csrf
                            @method('DELETE')
                            <input type="submit" value="&#xf1f8;" 
                                onClick="if (!confirm({{ __('messages.confirmDelete') }})){ return false;} return true;" class="fa btn-delete-icon">
                            <input type="hidden" name="cartId" value="{{$Cart->id}}">
                        </form>
                    </td>
                    <td><input type="checkbox" name="chkTarget[]" checked ></td>
                    <td class="align-center">
                        @if($Cart->ItemClass == '1')
                        {{ __('screenwords.reagent') }}
                        @elseif($Cart->ItemClass == '2')
                        {{ __('screenwords.article') }}
                        @endif
                    </td>
                    <td>
                        @if(App::getLocale()=='en') {{$Cart->ItemNameEn}}
                        @else {{$Cart->ItemNameJp}}
                        @endif
                    </td>
                    <td class="align-center">{{ $Cart->AmountUnit }}</td>
                    <td class="align-center">{{ $Cart->CatalogCode }}</td>
                    <td>
                        @if(App::getLocale()=='en') {{$Cart->MakerNameEn}}
                        @else {{$Cart->MakerNameJp}}
                        @endif
                    </td>
                    <td class="align-right tdOrderInputNumber">
                        <?php
                        if ($Cart->UnitPrice > 0) {
                            $UnitPrice = number_format($Cart->UnitPrice);
                        }
                        else{
                            $UnitPrice = '';
                        }
                        ?>
                        <span class="spnOrderInputNumber">\{{ $UnitPrice }}</span>
                        <input type="text" class="inpOrderInputNumber inpOrderUnitPrice" pattern="[0-9]*" title="数字のみ" value="{{ $Cart->UnitPrice }}" >
                    </td>
                    <td class="align-right tdOrderInputNumber">
                        <span class="spnOrderInputNumber">{{ $Cart->OrderRequestNumber }}</span>
                        <input type="number" class="inpOrderInputNumber inpOrderRequestNumber" pattern="[0-9]*"  title="数字のみ" min="1" value="{{ $Cart->OrderRequestNumber }}" >
                    </td>
                    <td class="align-right tdOrderTotalFee">
                        <?php
                            $TotalFee = number_format($Cart->UnitPrice * $Cart->OrderRequestNumber);
                        ?>
                        \{{$TotalFee}}
                    </td>
                    <td class="tdOrderRemark">
                        <p class="pOrderRemark" title="{{ $Cart->OrderRemark }}">{{ $Cart->OrderRemark }}</p>
                        <input type="text" class="inpOrderRemark" value="{{ $Cart->OrderRemark }}" >
                    </td>
                    <td style="display:none;">
                        {{$Cart->Standard}}
                    </td>
                    <td style="display:none;">
                        @if(App::getLocale()=='en') {{$Cart->SupplierNameEn}}
                        @else {{$Cart->SupplierNameJp}}
                        @endif
                    </td>
                    <td style="display:none;">
                        {{$Cart->id}}
                    </td>
                </tr>
            @endforeach
            @endif
            </tbody>
        </table>
        @component('components.productDetail')
        @endcomponent
    </div>

    <!--新規商品入力-->
    <div id="modal-newProduct" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
        <!--以下modal-dialogのCSSの部分で modal-lgやmodal-smを追加するとモーダルのサイズを変更することができる-->
        <div  class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="Modal">{{ __('screenwords.newItemInput') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('screenwords.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <section class="sectionNewProduct">
                        @csrf
                        <div class="form-group" >
                            <div class="divNewProductItemClass">
                            <label class="required">&nbsp;</label>
                            <label for="ItemClass_Reagent">
                                <input type="radio" id="ItemClass_Reagent" name="newItemClass" value="{{config('const.ItemClass.reagent')}}" required="required">{{ __('screenwords.reagent') }}</label>
                            <label for="ItemClass_Article">
                                <input type="radio" id="ItemClass_Article" name="newItemClass" value="{{config('const.ItemClass.article')}}">{{ __('screenwords.article') }}</label>
                            </div>
                        </div>
                        <div class="form-group" >
                            <label for="newProductName" class="required">{{ __('screenwords.itemName') }}</label>
                            <input type="text" id="newProductName" name="newProductName" value="{{old('newProductName')}}" required="required" size="50">
                        </div>
                        <div class="form-group" >
                            <label for="newStandard">{{ __('screenwords.standard') }}</label>
                            <input type="text" id="newStandard" name="newStandard" value="{{old('newStandard')}}" size="50">
                        </div>
                        <div class="form-group" >
                            <label for="newAmountUnit">{{ __('screenwords.capacityUnit') }}</label>
                            <input type="text" id="newAmountUnit" name="newAmountUnit" value="{{old('newAmountUnit')}}" size="50">
                        </div>
                        <div class="form-group" >
                            <label for="newCatalogCode">{{ __('screenwords.catalogCode') }}</label>
                            <input type="text" id="newCatalogCode" name="newCatalogCode" value="{{old('newCatalogCode')}}" size="50">
                        </div>
                        <div class="form-group" >
                            <label for="newMaker" class="required">{{ __('screenwords.maker') }}</label>
                            <select id="newMaker" name="newMaker" required>
                                @foreach($Makers as $Maker)
                                <option value="{{ $Maker->id }}">
                                    @if(App::getLocale()=='en') {{$Maker->MakerNameEn}}
                                    @else {{$Maker->MakerNameJp}}
                                    @endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" >
                            <label for="newUnitPrice" class="required">{{ __('screenwords.unitPrice') }}</label>
                            <input type="text" id="newUnitPrice" name="newUnitPrice" value="{{old('newUnitPrice')}}" required="required" style="width:100px;text-align:right;">&emsp;円
                        </div>
                        {{-- エラーメッセージ --}}
                        <div id="divError" class="alert alert-danger" style="display:none;" >
                        <ul></ul>
                        </div>
                    </section>
                </div>
                <div class="modal-footer">
                    <input type="button" id="submit_newProduct_save" name="submit_newProduct" class="btn btn-width70 btn-primary" value="{{ __('screenwords.save') }}" />
                    <button type="button" id="btnClear" class="btn btn-width70 btn-secondary" >{{ __('screenwords.clear') }}</button>
                    <button type="button" class="btn btn-width70 btn-secondary" data-dismiss="modal">{{ __('screenwords.close') }}</button>
                </div>
            </div>
        </div>
    </div>


</div>
</div>

@endsection