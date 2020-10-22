@extends('layouts.app')

@section('content')
<script src="{{ asset('js/purchaseScript.js') }}" defer></script>

<div class="container">
<div class="loading"><div class="loadingwrapper"><div class="ball-grid-pulse"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><div class="loadingtext">loading...</div></div></div>
<div class="wrapper">

    <div class="flexmain" >
        <h6 class="h6-title">購入履歴</h6>
        <form id="searchConditionForm" action="{{route('Purchase.index')}}" method="GET">
            <div class="divPurchaseHeaderButton">
                <div style="padding-top:3px;">
                    <input type="radio" id="rdoBoth" name="rdoItemClass" value="-1" @if($itemclass=='-1') checked='checked' @endif ><label for="rdoBoth">両方</label>
                    <input type="radio" id="rdoReagent" name="rdoItemClass" value="1"  @if($itemclass=='1') checked='checked' @endif ><label for="rdoReagent">試薬</label>
                    <input type="radio" id="rdoArticle" name="rdoItemClass" value="2" @if($itemclass=='2') checked='checked' @endif ><label for="rdoArticle">物品</label>
                </div>
                <div>
                    <span>発注日</span>
                    <input type="text" id="txtStartDate" name="startDate" class="inpExecDate" readonly="readonly" value="{{$startDate}}" >
                    <span style="padding:0 5px;">～</span>
                    <input type="text" id="txtEndDate" name="endDate" class="inpExecDate" readonly="readonly"  value="{{$endDate}}" >    
                </div>
                <div>
                    <span>発注依頼者</span>
                    <select name="selOrderRequestUser" class="selSearchPurchase">
                        <option value="-1">すべて</option>
                        @if(!$Users->isEmpty())
                        @foreach($Users as $User)
                        <option value="{{$User->id}}"  @if($requestUserId==$User->id) selected @endif >{{$User->UserNameJp}}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
                <div>
                    <span style="padding-left:0px;padding-right:20px;">検 索</span>
                    <input type="text" name="searchWord" class="inpSearchWord" value="{{$searchWord}}" placeholder="商品名またはカタログコードの一部で検索します" />
                </div>
                <div>
                    <span style="padding-left:27px;">メーカー</span>
                    <select name="selMaker" class="selSearchPurchase">
                        <option value="-1">すべて</option>
                        @if(!$Makers->isEmpty())
                        @foreach($Makers as $Maker)
                        <option value="{{$Maker->id}}" @if($makerId==$Maker->id) selected @endif>{{$Maker->MakerNameJp}}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div style="display:inline-block;padding-left:15px; ">
                    <input type="button" id="btnSearch" value="検索" class="btn btn-width70 btn-primary" >
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
                        <th class="align-center ">@sortablelink('OrderDate','発注日')</th>
                        <th class="align-center ">@sortablelink('DeliveryDate','納品日')</th>
                        <th>@sortablelink('ItemNameJp','商品名・規格・カタログコード・メーカー')</th>
                        <th class="align-center ">@sortablelink('UnitPrice','定価単価')</th>
                        <th class="align-center ">@sortablelink('DeliveryNumber','数量')</th>
                        <th class="align-center ">@sortablelink('DeliveryPrice','納入金額')</th>
                        <th>@sortablelink('BudgetNameJp','使用予算')</th>
                        <th>@sortablelink('RequestUserNameJp','発注依頼者')</th>
                    </tr>
                </thead>
                <tbody>
                @if(!$Deliveries->isEmpty())
                
                @foreach ($Deliveries as $delivery)
                    <tr class="table-purchaseFixed-tr">
                        <td class="table-purchaseFixed-buttontd">
                            <input type="button" name="btnOrderRequest" class="btn btn-secondary" value="発注依頼" >
                            <input type="hidden" name="hidDeliveryNumber" value="{{$delivery->DeliveryNumber}}">
                            <input type="hidden" name="hidOrderId" value="{{$delivery->OrderId}}">
                        </td>
                        <td>{{ $delivery->OrderDate }}</td>
                        <td>{{ $delivery->DeliveryDate }}</td>
                        <td>
                            <p>{{ $delivery->ItemNameJp }}</p>
                            <p>容量：{{$delivery->AmountUnit}} 規格：{{$delivery->Standard}} カタログコード：{{$delivery->CatalogCode}} メーカー：{{$delivery->MakerNameJp}}</p>
                            <input type="hidden" class="hidAmountUnit" value="{{$delivery->AmountUnit}}">
                            <input type="hidden" class="hidStandard" value="{{$delivery->Standard}}">
                            <input type="hidden" class="hidCatalogCode" value="{{$delivery->CatalogCode}}">
                            <input type="hidden" class="hidMakerNameJp" value="{{$delivery->MakerNameJp}}">
                            <input type="hidden" class="hidSupplierNameJp" value="{{$delivery->SupplierNameJp}}">
                            <input type="hidden" class="hidOrderRemark" value="{{$delivery->OrderRemark}}">
                        </td>
                        <td class="align-right">\{{ number_format($delivery->UnitPrice) }}</td>
                        <td class="align-right">{{ number_format($delivery->DeliveryNumber) }}</td>
                        <td class="align-right">\{{ number_format($delivery->DeliveryPrice) }}</td>
                        <td>{{ $delivery->BudgetNameJp }}</td>
                        <td>{{ $delivery->RequestUserNameJp }}</td>
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
                        <h5 class="modal-title" id="Modal">発注依頼</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <section class="update">
                            @csrf
                            <div class="form-group" style="margin-top:10px;">
                                <label for="txtOrderNumber" class="required">数量</label>
                                <input type="number" id="txtOrderNumber" style="width:90px;text-align:right;" required="required" min="1" name="OrderNumber" value="{{old('OrderNumber')}}">
                            </div>
                            <div class="form-group" style="margin-top:10px;">
                                <label for="txtOrderRemark">備考</label>
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
                        <button type="button" id="btnOrderRequest" class="btn btn-primary" >発注依頼</button>
                        <button type="button" id="btnClear" class="btn btn-width70 btn-secondary" >クリア</button>
                        <button type="button" class="btn btn-width70 btn-secondary" data-dismiss="modal">閉じる</button>
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