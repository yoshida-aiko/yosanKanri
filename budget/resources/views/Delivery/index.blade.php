@extends('layouts.app')

@section('content')
<script src="{{ asset('js/deliveryScript.js') }}" defer></script>

<div class="container">
<div class="loading"><div class="loadingwrapper"><div class="ball-grid-pulse"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><div class="loadingtext">loading...</div></div></div>
<div class="wrapper">

    <div class="flexmain" >
        <h6 class="h6-title">納品リスト</h6>
        @if(!$Orders->isEmpty())
        <!--<form action="{{action('DeliveryController@insertDelivery')}}" method='post'>-->
        <div class="divOrderRequestHeaderButton">
            <input type="button" id="btnDelivery" value="納品" class="btn btn-primary" >
        </div>
        <div class="pagenationStyle">
        @if(!$Orders->isEmpty())
            {{$Orders->appends(request()->query())->links()}}    
        @endif
        </div>
            <table id="table-deliveryFixed" class="table table-fixed table-deliveryFixed table-striped">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th><input type="checkbox" name="chkTargetAll"></th>
                        <th>@sortablelink('ItemNameJp','商品名')</th>
                        <th>@sortablelink('SupplierNameJp','発注先')</th>
                        <th class="align-center ">@sortablelink('OrderSlipNo','注文No.')</th>
                        <th class="align-center ">納品日</th>
                        <th class="align-center ">@sortablelink('DeliveryNumber','納品済数')</th>
                        <th class="align-center ">@sortablelink('OrderNumber','発注数')</th>
                        <th class="align-center ">納品数</th>
                        <th class="align-center ">執行額</th>
                        <th>@sortablelink('RequestUserNameJp','依頼者')</th>
                        <th>@sortablelink('BudgetNameJp','予算科目')</th>
                        <th>@sortablelink('RecieveUserNameJp','発注者')</th>
                    </tr>
                </thead>
                <tbody>
                
                @foreach ($Orders as $order)
                    <tr class="table-deliveryFixed-tr">
                        <td>
                            <form action="{{ route('Delivery.destroy', $order->id) }}" method='post'>
                                @csrf
                                @method('DELETE')
                                <input type="submit" value="&#xf1f8;" 
                                    onClick="if (!confirm('削除しますか？')){ return false;} return true;" class="fa btn-delete-icon">
                                <input type="hidden" name="orderId" value="{{$order->id}}">
                            </form>
                        </td>
                        <td><input type="checkbox" name="chkTarget[]" ></td>
                        <td><p>{{ $order->item->ItemNameJp }}</p><p>容量：{{$order->item->AmountUnit}} 規格：{{$order->item->Standard}} カタログコード：{{$order->item->CatalogCode}} メーカー：{{$order->item->MakerNameJp}}</p></td>
                        <td>{{ $order->SupplierNameJp }}</td>
                        <td class="align-center">{{ $order->orderslip->OrderSlipNo }}</td>
                        <td>
                            <?php
                                $DeliveryDate = date('Y/m/d');
                                $dtUseDate = date('Y/m/d');
                                if ($order->UseEndDate != null){
                                    $dtUseDate = date('Y/m/d',strtotime(str_replace('/','-',$order->UseEndDate)));
                                    if ($DeliveryDate > $dtUseDate) {
                                        $DeliveryDate = $dtUseDate;
                                    }
                                }
                            ?>
                            <input type="text" name="deliveryDate" class="inpDeliveryDate" value="{{$DeliveryDate}}">
                        </td>
                        <td class="align-right">{{ $order->DeliveryNumber }}</td>
                        <td class="align-right">{{ $order->OrderNumber }}</td>
                        <td class="align-right tdOrderInputNumber">
                            <?php
                            if ($order->DeliveryNumber >= 0) {
                                $DeliveryExpectedNumber = $order->OrderNumber - $order->DeliveryNumber;
                                $DeliveryExpectedNumberFormat = number_format($order->OrderNumber - $order->DeliveryNumber);
                            }
                            else{
                                $DeliveryExpectedNumber = '1';
                            }
                            ?>
                            <span class="spnOrderInputNumber">{{ $DeliveryExpectedNumberFormat }}</span>
                            <input type="number" class="inpOrderInputNumber inpDeliveryExpectedNumber" pattern="[0-9]*" title="数字のみ" value="{{ $DeliveryExpectedNumber }}" min="1" max="{{$order->OrderNumber}}">
                            <input type="hidden" class="hidUnitPrice" value="{{$order->UnitPrice}}">
                        </td>
                        <td class="align-right tdOrderInputNumber tdSummaryPrice">
                            <?php
                                $Summary = $order->UnitPrice * intval($DeliveryExpectedNumber);
                                $SummaryFormat = number_format($order->UnitPrice * intval($DeliveryExpectedNumber));
                            ?>
                            <span class="spnOrderInputNumber">{{ $SummaryFormat }}</span>
                            <input type="text" class="inpOrderInputNumber inpSummaryPrice" pattern="[0-9]*"  title="数字のみ" min="1" value="{{ $Summary }}" >
                        </td>
                        <td>{{ $order->RequestUserNameJp }}</td>

                        <td class="tdBudget">
                            <span class="spnSelectBudget">{{ $order->BudgetNameJp }}</span>
                            <select class="selSelectBudget">
                                @foreach($Budgets as $Budget)
                                <option value="{{$Budget->id}}">{{$Budget->budgetNameJp}}</option>
                                @endforeach
                            </select>
                            <span class="spnBudgetId" style="display:none;">{{ $order->BudgetId }}</span>
                        </td>
                        <td>{{ $order->RecieveUserNameJp }}</td>
                        <td class="tdAmountUnit" style="display:none;">{{$order->item->AmountUnit}}</td>
                        <td class="tdStandard" style="display:none;">{{$order->item->Standard}}</td>
                        <td class="tdCatalogCode" style="display:none;">{{$order->item->CatalogCode}}</td>
                        <td class="tdUnitPrice" style="display:none;">{{$order->UnitPrice}}</td>
                        <td class="tdMakerName" style="display:none;">{{$order->item->MakerNameJp}}</td>
                        <td class="tdSupplierName" style="display:none;">{{$order->MainSupplierNameJp}}</td>
                        <td class="tdItemName" style="display:none;">{{$order->item->ItemNameJp}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        <!--</form>-->
        @else
        <p>データがありません</p>
        @endif
        <div id="modal-detail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
            <!--以下modal-dialogのCSSの部分で modal-lgやmodal-smを追加するとモーダルのサイズを変更することができる-->
            <div  class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="Modal">商品詳細</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="modal-detail-table">
                            <tbody>
                                <tr>
                                    <td>商品名：</td><td id="detailProductName" colspan="3"></td>
                                </tr>
                                <tr>
                                    <td>容量：</td><td id="detailAmount"></td><td>規格：</td><td id="detailStandard"></td>
                                </tr>
                                <tr>
                                    <td>カタログコード：</td><td id="detailCatalogCode"></td><td>単価：</td><td id="detailUnitPrice"></td>
                                </tr>
                                <tr>
                                    <td>メーカー：</td><td id="detailMakerName"></td><td>優先する発注先：</td><td id="detailSupplierName"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
                    </div>
                </div>
            </div>
        </div>
    </div>



</div>
</div>

@endsection