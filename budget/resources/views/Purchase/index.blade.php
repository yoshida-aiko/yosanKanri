@extends('layouts.app')

@section('content')
<script src="{{ asset('js/purchaseScript.js') }}" defer></script>

<div class="container">
<div class="loading"><div class="loadingwrapper"><div class="ball-grid-pulse"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><div class="loadingtext">loading...</div></div></div>
<div class="wrapper">

    <div class="flexmain" >
        <h6 class="h6-title">{{ __('screenwords.buyingHistory') }}</h6>
        <form id="searchConditionForm" action="{{route('Purchase.index')}}" method="GET">
            <div class="divPurchaseHeaderButton">
                <div style="padding-top:3px;">
                    <input type="radio" id="rdoBoth" name="rdoItemClass" value="-1" @if($itemclass=='-1') checked='checked' @endif ><label for="rdoBoth">{{ __('screenwords.both') }}</label>
                    <input type="radio" id="rdoReagent" name="rdoItemClass" value="{{config('const.ItemClass.reagent')}}"  @if($itemclass==config('const.ItemClass.reagent')) checked='checked' @endif ><label for="rdoReagent">{{ __('screenwords.reagent') }}</label>
                    <input type="radio" id="rdoArticle" name="rdoItemClass" value="{{config('const.ItemClass.article')}}" @if($itemclass==config('const.ItemClass.article')) checked='checked' @endif ><label for="rdoArticle">{{ __('screenwords.article') }}</label>
                </div>
                <div>
                    <span>{{ __('screenwords.orderDate') }}</span>
                    <input type="text" id="txtStartDate" name="startDate" class="inpExecDate" readonly="readonly" value="{{$startDate}}" >
                    <span style="padding:0 5px;">～</span>
                    <input type="text" id="txtEndDate" name="endDate" class="inpExecDate" readonly="readonly"  value="{{$endDate}}" >    
                </div>
                <div>
                    <span>{{ __('screenwords.orderRequestUser') }}</span>
                    <select name="selOrderRequestUser" class="selSearchPurchase">
                        <option value="-1">{{ __('screenwords.all') }}</option>
                        @if(!$Users->isEmpty())
                        @foreach($Users as $User)
                        <option value="{{$User->id}}"  @if($requestUserId==$User->id) selected @endif >{{$User->UserNameJp}}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
                <div>
                    <span style="padding-left:0px;padding-right:20px;">{{ __('screenwords.search') }}</span>
                    <input type="text" name="searchWord" class="inpSearchWord" value="{{$searchWord}}" placeholder="{{ __('screenwords.searchHeaderPlaceholder') }}" />
                </div>
                <div>
                    <span style="padding-left:27px;">{{ __('screenwords.maker') }}</span>
                    <select name="selMaker" class="selSearchPurchase">
                        <option value="-1">{{ __('screenwords.all') }}</option>
                        @if(!$Makers->isEmpty())
                        @foreach($Makers as $Maker)
                        <option value="{{$Maker->id}}" @if($makerId==$Maker->id) selected @endif>{{$Maker->MakerNameJp}}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div style="display:inline-block;padding-left:15px; ">
                    <input type="button" id="btnSearch" value="{{ __('screenwords.search') }}" class="btn btn-width70 btn-primary" >
                    <input type="button" id="btnCsv" value="CSV" class="btn btn-width70 btn-secondary" @if($Deliveries->isEmpty()) disabled='disabled' @endif >
            </div>
        </form>

        <div class="pagenationStyle" style="top:50px;right:10px;">
        @if(!$Deliveries->isEmpty())
            {{$Deliveries->appends(request()->query())->links()}}    
        @endif
        </div>
            <table id="table-purchaseFixed" class="table table-fixed table-purchaseFixed table-striped">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th class="align-center ">@sortablelink('OrderDate',__('screenwords.orderDate'))</th>
                        <th class="align-center ">@sortablelink('DeliveryDate',__('screenwords.deliveryDate'))</th>
                        <th>@sortablelink('ItemNameJp',__('screenwords.items'))</th>
                        <th class="align-center ">@sortablelink('UnitPrice',__('screenwords.fixedPriceUnitPrice'))</th>
                        <th class="align-center ">@sortablelink('DeliveryNumber',__('screenwords.quantity'))</th>
                        <th class="align-center ">@sortablelink('DeliveryPrice',__('screenwords.paymentAmount'))</th>
                        <th>@sortablelink('BudgetNameJp',__('screenwords.useBudget'))</th>
                        <th>@sortablelink('RequestUserNameJp',__('screenwords.orderRequestUser'))</th>
                    </tr>
                </thead>
                <tbody>
                @if(!$Deliveries->isEmpty())
                
                @foreach ($Deliveries as $delivery)
                    <tr class="table-purchaseFixed-tr">
                        <td class="table-purchaseFixed-buttontd">
                            <input type="button" name="btnOrderRequest" class="btn btn-secondary" value="{{ __('screenwords.orderRequest') }}" >
                            <input type="hidden" name="hidDeliveryNumber" value="{{$delivery->DeliveryNumber}}">
                            <input type="hidden" name="hidOrderId" value="{{$delivery->OrderId}}">
                        </td>
                        <td>{{ $delivery->OrderDate }}</td>
                        <td>{{ $delivery->DeliveryDate }}</td>
                        <td>
                            <p>
                                @if(App::getLocale()=='en') {{ $delivery->ItemNameEn }}
                                @else {{ $delivery->ItemNameJp }}
                                @endif   
                            </p>
                            <p>{{ __('screenwords.capacity') }}：{{$delivery->AmountUnit}} {{ __('screenwords.standard') }}：{{$delivery->Standard}} {{ __('screenwords.catalogCode') }}：{{$delivery->CatalogCode}} {{ __('screenwords.maker') }}：
                                @if(App::getLocale()=='en') {{$delivery->MakerNameEn}}
                                @else {{$delivery->MakerNameJp}}
                                @endif
                            </p>

                            <input type="hidden" class="hidAmountUnit" value="{{$delivery->AmountUnit}}">
                            <input type="hidden" class="hidStandard" value="{{$delivery->Standard}}">
                            <input type="hidden" class="hidCatalogCode" value="{{$delivery->CatalogCode}}">
                            <input type="hidden" class="hidMakerName" value="@if(App::getLocale()=='en') {{$delivery->MakerNameEn}} @else {{$delivery->MakerNameJp}} @endif ">
                            <input type="hidden" class="hidSupplierName" value="@if(App::getLocale()=='en') {{$delivery->SupplierNameEn}} @else {{$delivery->SupplierNameJp}} @endif ">
                            <input type="hidden" class="hidOrderRemark" value="{{$delivery->OrderRemark}}">
                        </td>
                        <td class="align-right">\{{ number_format($delivery->UnitPrice) }}</td>
                        <td class="align-right">{{ number_format($delivery->DeliveryNumber) }}</td>
                        <td class="align-right">\{{ number_format($delivery->DeliveryPrice) }}</td>
                        <td>
                            @if(App::getLocale()=='en') {{ $delivery->BudgetNameEn }}
                            @else {{ $delivery->BudgetNameJp }}
                            @endif   
                        </td>
                        <td>
                            @if(App::getLocale()=='en') {{ $delivery->RequestUserNameEn }}
                            @else {{ $delivery->RequestUserNameJp }}
                            @endif   
                        </td>
                    </tr>
                @endforeach
                @endif
                </tbody>
            </table>
        
        
        <div id="modal-orderRequest" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
            <!--以下modal-dialogのCSSの部分で modal-lgやmodal-smを追加するとモーダルのサイズを変更することができる-->
            <div  class="modal-dialog modal-sm100" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="Modal">{{ __('screenwords.orderRequest') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('screenwords.close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <section class="update">
                            @csrf
                            <div class="form-group" style="margin-top:10px;">
                                <label for="txtOrderNumber" class="required">{{ __('screenwords.quantity') }}</label>
                                <input type="number" id="txtOrderNumber" style="width:90px;text-align:right;" required="required" min="1" name="OrderNumber" value="{{old('OrderNumber')}}">
                            </div>
                            <div class="form-group" style="margin-top:10px;">
                                <label for="txtOrderRemark">{{ __('screenwords.remark') }}</label>
                                <input type="text" id="txtOrderRemark" name="OrderRemark" value="{{old('OrderRemark')}}">
                            </div>
                            <input type="hidden" id="hidInsertOrderId" value="">
                            {{-- エラーメッセージ --}}
                            <div id="divError" class="alert alert-danger" style="display:none;" >
                            <ul></ul>
                            </div>
                        </section>                    
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnOrderRequest" class="btn btn-primary" >{{ __('screenwords.orderRequest') }}</button>
                        <button type="button" id="btnClear" class="btn btn-width70 btn-secondary" >{{ __('screenwords.clear') }}</button>
                        <button type="button" class="btn btn-width70 btn-secondary" data-dismiss="modal">{{ __('screenwords.close') }}</button>
                    </div>
                </div>
            </div>
        </div>
        @component('components.productDetail')
        @endcomponent
    </div>
</div>
</div>

@endsection