@extends('layouts.app')

@section('content')
<script src="{{ asset('js/orderScript.js') }}" defer></script>


<div class="container">
<div class="loading"><div class="loadingwrapper"><div class="ball-grid-pulse"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><div class="loadingtext">loading...</div></div></div>
<div id="divOrderRequestList" class="wrapper" style="padding-bottom: 0px;display:none;">

    <div class="leftside-fixed-400">
        <h6 class="h6-title">予算リスト</h6>
        <div class="divOrderHeaderButton">
            <input type="button" id="btnToOrderList" class="btn btn-primary" value="発注リスト" style="margin-left:300px;" >
        </div>
        <div id="budgetTree" class="budgetTree">
            @foreach ($arrayBudgetTree as $arrBudget)
            <div class="parentBudget">
                @if ($arrBudget['children'] <> null)
                <span class="fa fa-caret-down"></span>
                <span class="fa fa-folder-open"></span>
                @else
                <span>&nbsp;</span>
                <span class="fa fa-folder"></span>
                @endif
                <span>{{$arrBudget['BudgetNameJp']}}</span>
                <span class="smalllabel">発注額:</span>
                <span>{{$arrBudget['orderFee']}}</span>
                <span class="smalllabel">残高:</span>
                <span>{{$arrBudget['remainFee']}}</span>
            </div>
            @if ($arrBudget['children'] <> null)
            <ul class="childBudget">
            @foreach ($arrBudget['children'] as $arrChild)
                <?php
                    $tooltip = $arrChild['ProductNameJp'].'【容量】'.$arrChild['AmountUnit']
                        .'【規格】'.$arrChild['Standard'].'【メーカー】'.$arrChild['MakerNameJp']
                        .'【ｶﾀﾛｸﾞｺｰﾄﾞ】'.$arrChild['CatalogCode'].'【依頼者】'.$arrChild['RequestUserName'];
                ?>
                <li>
                    <span data-toggle="tooltip" title="{{$tooltip}}">{{$arrChild['ProductNameJp']}}</span>
                    <span>{{$arrChild['UnitPrice']}}</span>
                    <span>{{$arrChild['RequestNumber']}}</span>
                    <span>{{$arrChild['SummaryPrice']}}</span>
                    <span style="display:none;">{{$arrChild['OrderId']}}</span>
                </li>
            @endforeach
            </ul>
            @endif
            @endforeach
        </div>
    </div>
    <div class="flexmain" >
        <h6 class="h6-title">発注依頼リスト</h6>
        <div class="divOrderHeaderButton">
            <form action="{{action('OrderController@index')}}" method="get">
                <input type="radio" id="rdoBoth" name="rdoItemClass" value="-1" @if($itemclass=='-1') checked='checked' @endif onchange="submit(this.form)"><label for="rdoBoth">両方</label>
                <input type="radio" id="rdoReagent" name="rdoItemClass" value="1"  @if($itemclass=='1') checked='checked' @endif onchange="submit(this.form)"><label for="rdoReagent">試薬</label>
                <input type="radio" id="rdoArticle" name="rdoItemClass" value="2" @if($itemclass=='2') checked='checked' @endif onchange="submit(this.form)"><label for="rdoArticle">物品</label>
            </form>
        </div>
        @if(!$OrderRequests->isEmpty())
        <div class="pagenationStyle">
        @if(!$OrderRequests->isEmpty())
            {{$OrderRequests->appends(request()->query())->links()}}    
        @endif
        </div>
            <table id="table-orderFixed" class="table table-fixed table-orderFixed table-striped">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th class="align-center ">@sortablelink('ItemClass','種類')</th>
                        <th>@sortablelink('ItemNameJp','商品名')</th>
                        <th class="align-center ">@sortablelink('AmountUnit','容量')</th>
                        <th class="align-center ">@sortablelink('Standard','規格')</th>
                        <th class="align-center ">@sortablelink('CatalogCode','カタログコード')</th>
                        <th>@sortablelink('MakerNameJp','メーカー名')</th>
                        <th class="align-center ">@sortablelink('UnitPrice','単価')</th>
                        <th class="align-center ">@sortablelink('RequestNumber','数量')</th>
                        <th class="align-center ">金額</th>
                        <th>@sortablelink('RequestUserNameJp','依頼者')</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($OrderRequests as $OrderRequest)
                    <tr class="table-orderFixed-tr">
                        <td>
                            <form action="{{ route('Order.destroy', $OrderRequest->id) }}" method='post'>
                                @csrf
                                @method('DELETE')
                                <input type="submit" value="&#xf1f8;" 
                                    onClick="if (!confirm('削除しますか？')){ return false;} return true;" class="fa btn-delete-icon">
                                <input type="hidden" name="orderreqId" value="{{$OrderRequest->id}}">
                            </form>
                        </td>
                        <td class="align-center">
                            @if($OrderRequest->ItemClass == '1')
                                試薬
                            @elseif($OrderRequest->ItemClass == '2')
                                物品
                            @endif
                        </td>
                        <td>{{ $OrderRequest->ItemNameJp }}</td>
                        <td class="align-center">{{ $OrderRequest->AmountUnit }}</td>
                        <td class="align-center">{{ $OrderRequest->Standard }}</td>
                        <td class="align-center">{{ $OrderRequest->CatalogCode }}</td>
                        <td>{{ $OrderRequest->item->MakerNameJp }}</td>
                        <td class="align-right tdOrderInputNumber">
                        <?php
                            if ($OrderRequest->UnitPrice > 0) {
                                $UnitPrice = number_format($OrderRequest->UnitPrice);
                            }
                            else{
                                $UnitPrice = '';
                            }
                            ?>
                            <span class="spnOrderInputNumber">{{ $UnitPrice }}</span>
                            <input type="text" class="inpOrderInputNumber inpOrderUnitPrice" pattern="[0-9]*" title="数字のみ" value="{{ $OrderRequest->UnitPrice }}" >
                        </td>
                        <td class="align-right tdOrderInputNumber">
                            <span class="spnOrderInputNumber">{{ $OrderRequest->RequestNumber }}</span>
                            <input type="number" class="inpOrderInputNumber inpOrderRequestNumber" pattern="[0-9]*"  title="数字のみ" min="1" value="{{ $OrderRequest->RequestNumber }}" >
                        </td>
                        <td class="align-right tdOrderTotalFee">
                            <?php
                                $TotalFee = number_format($OrderRequest->UnitPrice * $OrderRequest->RequestNumber);
                            ?>
                            {{$TotalFee}}
                        </td>
                        <td>{{ $OrderRequest->RequestUserNameJp }}</td>
                        <td style="display:none;">
                            {{$OrderRequest->id}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
        <div class="divNoData">
            <p>データがありません</p>
        </div>
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

<div id="divOrderList" class="wrapper" style="padding-bottom: 0px;display:none;">
    <div class="flexmain" >
        <h6 class="h6-title">発注リスト</h6>
        <div class="divOrderHeaderButton">
            <input type="button" id="btnHowToOrder" class="btn btn-primary" value="発注" >
            <input type="button" id="btnReturnOrderRequestList" class="btn btn-secondary" value="戻る">
            <input type="button" id="btnHash" class="btn btn-primary" value="Hash">
        </div>
        @if(!$orderRequest_Orders->isEmpty())
        <div class="pagenationStyle">
        @if(!$orderRequest_Orders->isEmpty())
            {{$orderRequest_Orders->appends(request()->query())->links()}}    
        @endif
        </div>
            <table id="table-orderProcessingFixed" class="table table-fixed table-orderProcessingFixed table-striped">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th class="align-center"><input type="checkbox" name="chkTargetAll" checked ></th>
                        <th>@sortablelink('SupplierNameJp','発注先')</th>
                        <th>@sortablelink('ItemNameJp','商品名・容量・規格・カタログコード・メーカー')</th>
                        <th class="align-center ">@sortablelink('OrderPrice','発注額')</th>
                        <th>@sortablelink('BudgetNameJp','予算科目')</th>
                        <th class="align-center ">@sortablelink('OrderRemark','備考')</th>
                        <th>@sortablelink('RequestUserNameJp','依頼者')</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($orderRequest_Orders as $orderRequest_Order)
                    <tr class="table-orderProcessingFixed-tr">
                        <td>
                            <form action="{{ route('Order.destroy', $orderRequest_Order->id) }}" method='post'>
                                @csrf
                                @method('DELETE')
                                <input type="submit" value="&#xf1f8;" 
                                    onClick="if (!confirm('削除しますか？')){ return false;} return true;" class="fa btn-delete-icon">
                                <input type="hidden" name="orderreqId" value="{{$orderRequest_Order->id}}">
                           </form>
                        </td>
                        <td class="align-center"><input type="checkbox" name="chkTarget[]" checked ></td>
                        <td class="tdOrderSelectSupplier">
                            <span class="spnOrderSelectSupplier">{{ $orderRequest_Order->supplier->SupplierNameJp }}</span>
                            <select class="selOrderSelectSupplier">
                                @foreach ($Suppliers as $supplier)
                                <option value="{{$supplier->id}}">{{$supplier->SupplierNameJp}}</option>
                                @endforeach
                            </select>
                            <span class="spnSupplierId">{{ $orderRequest_Order->SupplierId }}</span>
                        </td>
                        <td><p>{{ $orderRequest_Order->item->ItemNameJp }}</p><p>容量：{{$orderRequest_Order->item->AmountUnit}} 規格：{{$orderRequest_Order->item->Standard}} カタログコード：{{$orderRequest_Order->item->CatalogCode}} メーカー：{{$orderRequest_Order->item->MakerNameJp}}</p></td>
                        <?php
                            $ordersum = number_format($orderRequest_Order->UnitPrice * $orderRequest_Order->RequestNumber)
                        ?>
                        <td class="align-right">{{ $ordersum }}</td>
                        <td>{{ $orderRequest_Order->BudgetNameJp }}</td>
                        <td>{{ $orderRequest_Order->OrderRemark }}</td>
                        <td>{{ $orderRequest_Order->user->UserNameJp }}</td>
                        <td style="display:none;">
                            {{$orderRequest_Order->id}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
        <div class="divNoData">
            <p>データがありません</p>
        </div>
        @endif
    </div>
    <div id="modal-howto-order" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
            <!--以下modal-dialogのCSSの部分で modal-lgやmodal-smを追加するとモーダルのサイズを変更することができる-->
            <div  class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="padding: 5px 10px;">
                        <h5 class="modal-title" id="Modal">発注方法を選択してください</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="divCircleWrapper" >
                            <div id="btnCircle_Email" class="btnCircle" title="e-Mailで発注"><span class="fa fa-envelope-o"></span><span>e-mail</span></div>
                            <div id="btnCircle_PDF" class="btnCircle" title="注文書PDFを作成"  data-dismiss="modal"><span class="fa fa-file-pdf-o"></span><span>pdf</span></div>
                            <form id="frmPdfOutput" action="{{action('OrderController@createPDF')}}" method="get">
                                <input type="hidden" name="arrayOrderRequestIds" >
                            </form>
                            <div id="btnCircle_Other" class="btnCircle" title="その他の方法"><span class="fa fa-pencil-square-o"></span><span>other</span></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
                    </div>
                </div>
            </div>
        </div>
</div>

<div class="toggle-fixed-40">
    <div id="toggle-button-order"></div>
</div>
<div class="bottom-fixed-200" style="display:none;">
    <table class="table table-fixed table-yosan-under-list table-striped">
        <thead>
            <tr>
                <th>予算科目</th>
                <th>予算額</th>
                <th>執行額</th>
                <th>執行済残高</th>
                <th>執行予定額</th>
                <th>執行予定込み残高</th>
            </tr>
        </thead>
        <tbody>
            @foreach($arrayBudgetList as $BudgetItem)
            <tr>
                <td>{{$BudgetItem['BudgetNameJp']}}</td>
                <td>{{$BudgetItem['BudgetAmount']}}</td>
                <td>{{$BudgetItem['BudgetUsed']}}</td>
                <td>{{$BudgetItem['BudgetScheduled']}}</td>
                <td>{{$BudgetItem['BudgetRemainBal']}}</td>
                <td>{{$BudgetItem['BudgetScheduledRemain']}}</td>
            </tr>
            @endforeach

        </tbody>
    </table>
</div>
<div id="popmenu-budgetlist" role='popmenu-layer'>
    <ul role='popmenu'>
        <li class='popmenu-header'>
            <span class='fa fa-hand-o-left'></span>予算リストへ
        </li>
        @foreach($arrayBudgetForContext as $budgetitem)
        <li id="BudgetId-{{ $budgetitem['BudgetId'] }}" name="{{$budgetitem['BudgetNameJp']}}" ><span class="fa fa-angle-double-left"></span>{{$budgetitem['BudgetNameJp']}}</li>
        @endforeach
    </ul>
</div>
<div id="popmenu-orderrequestlist" role='popmenu-layer'>
    <ul role='popmenu'>
        <li id="toOrderrequestlist" name="toOrderrequestlist" >発注依頼リストへ<span class="fa fa-hand-o-right"></span></li>
    </ul>
</div>

</div>

@endsection