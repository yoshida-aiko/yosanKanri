@extends('layouts.app')

@section('content')
<script src="{{ asset('js/orderScript.js') }}" defer></script>


<div class="container">
<div class="loading"><div class="loadingwrapper"><div class="ball-grid-pulse"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><div class="loadingtext">loading...</div></div></div>
<div id="divOrderRequestList" class="wrapper" style="padding-bottom: 0px;display:none;">

    <div class="leftside-fixed-400">
        <h6 class="h6-title">{{ __('screenwords.budgetList') }}</h6>
        <div class="divOrderHeaderButton">
            <input type="button" id="btnToOrderList" class="btn btn-primary" value="{{ __('screenwords.orderList') }}" style="margin-left:300px;" >
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
                <span>
                    @if(App::getLocale()=='en'&&$arrBudget['BudgetNameEn']!=null) {{$arrBudget['BudgetNameEn']}}
                    @else {{$arrBudget['BudgetNameJp']}}
                    @endif
                </span>
                <span class="smalllabel" title="{{ __('screenwords.orderAmount') }}">{{ __('screenwords.orderAmount') }}:</span>
                <span>\{{$arrBudget['orderFee']}}</span>
                <span class="smalllabel" title="{{ __('screenwords.remain') }}">{{ __('screenwords.remain') }}:</span>
                <span>\{{$arrBudget['remainFee']}}</span>
            </div>
            @if ($arrBudget['children'] <> null)
            <ul class="childBudget">
            @foreach ($arrBudget['children'] as $arrChild)
                <?php
                    $tooltip = (App::getLocale()=='en' ? $arrChild['ProductNameEn'] : $arrChild['ProductNameJp']);
                    $tooltip .= '【'.__('screenwords.capacity').'】';$arrChild['AmountUnit'];
                    $tooltip .= '【'.__('screenwords.standard').'】'.$arrChild['Standard'];
                    $tooltip .= '【'.__('screenwords.maker').'】'.(App::getLocale()=='en' ? $arrChild['MakerNameEn'] : $arrChild['MakerNameJp']);
                    $tooltip .= '【'.__('screenwords.catalogCode').'】'.$arrChild['CatalogCode'];
                    $tooltip .= '【'.__('screenwords.client').'】'.(App::getLocale()=='en' ? $arrChild['RequestUserNameEn'] : $arrChild['RequestUserNameJp']);
                ?>
                <li>
                    <span data-toggle="tooltip" title="{{$tooltip}}">
                        @if(App::getLocale()=='en') {{$arrChild['ProductNameEn']}}
                        @else {{$arrChild['ProductNameJp']}}
                        @endif
                    </span>
                    <span>\{{$arrChild['UnitPrice']}}</span>
                    <span>{{$arrChild['RequestNumber']}}</span>
                    <span>\{{$arrChild['SummaryPrice']}}</span>
                    <span style="display:none;">{{$arrChild['OrderId']}}</span>
                </li>
            @endforeach
            </ul>
            @endif
            @endforeach
        </div>
    </div>
    <div class="flexmain" >
        <h6 class="h6-title">{{ __('screenwords.orderRequestList') }}</h6>
        <div class="divOrderHeaderButton">
            <form action="{{action('OrderController@index')}}" method="get">
                <input type="radio" id="rdoBoth" name="rdoItemClass" value="-1" @if($itemclass=='-1') checked='checked' @endif onchange="submit(this.form)"><label for="rdoBoth">{{ __('screenwords.both') }}</label>
                <input type="radio" id="rdoReagent" name="rdoItemClass" value="{{config('const.ItemClass.reagent')}}"  @if($itemclass==config('const.ItemClass.reagent')) checked='checked' @endif onchange="submit(this.form)"><label for="rdoReagent">{{ __('screenwords.reagent') }}</label>
                <input type="radio" id="rdoArticle" name="rdoItemClass" value="{{config('const.ItemClass.article')}}" @if($itemclass==config('const.ItemClass.article')) checked='checked' @endif onchange="submit(this.form)"><label for="rdoArticle">{{ __('screenwords.article') }}</label>
            </form>
        </div>
        <div class="pagenationStyle">
        @if(!$OrderRequests->isEmpty())
            {{$OrderRequests->appends(request()->query())->links()}}    
        @endif
        </div>
            <table id="table-orderFixed" class="table table-fixed table-orderFixed table-striped">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th class="align-center ">@sortablelink('ItemClass',__('screenwords.type'))</th>
                        <th>@sortablelink(__('screenwords.sortItemName'),__('screenwords.itemName'))</th>
                        <th class="align-center ">@sortablelink('AmountUnit',__('screenwords.capacity'))</th>
                        <th class="align-center ">@sortablelink('Standard',__('screenwords.standard'))</th>
                        <th class="align-center ">@sortablelink('CatalogCode',__('screenwords.catalogCode'))</th>
                        <th>@sortablelink(__('screenwords.sortMakerName'),__('screenwords.makerName'))</th>
                        <th class="align-center ">@sortablelink('UnitPrice',__('screenwords.unitPrice'))</th>
                        <th class="align-center ">@sortablelink('RequestNumber',__('screenwords.quantity'))</th>
                        <th class="align-center ">{{__('screenwords.amount')}}</th>
                        <th>@sortablelink(__('screenwords.sortRequestUserName'),__('screenwords.client'))</th>
                    </tr>
                </thead>
                <tbody>
                @if(!$OrderRequests->isEmpty())
                @foreach ($OrderRequests as $OrderRequest)
                    <tr class="table-orderFixed-tr">
                        <td>
                            <form action="{{ route('Order.destroy', $OrderRequest->id) }}" method='post'>
                                @csrf
                                @method('DELETE')
                                <input type="submit" value="&#xf1f8;" 
                                    onClick="if (!confirm({{ __('messages.confirmDelete') }})){ return false;} return true;" class="fa btn-delete-icon">
                                <input type="hidden" name="orderreqId" value="{{$OrderRequest->id}}">
                            </form>
                        </td>
                        <td class="align-center">
                            @if($OrderRequest->ItemClass == '1')
                            {{ __('screenwords.reagent') }}
                            @elseif($OrderRequest->ItemClass == '2')
                            {{ __('screenwords.article') }}
                            @endif
                        </td>
                        <td>
                            @if(App::getLocale()=='en') {{$OrderRequest->ItemNameEn}}
                            @else {{$OrderRequest->ItemNameJp}}
                            @endif
                        </td>
                        <td class="align-center">{{ $OrderRequest->AmountUnit }}</td>
                        <td class="align-center">{{ $OrderRequest->Standard }}</td>
                        <td class="align-center">{{ $OrderRequest->CatalogCode }}</td>
                        <td>
                            @if(App::getLocale()=='en') {{$OrderRequest->item->MakerNameEn}}
                            @else {{$OrderRequest->item->MakerNameJp}}
                            @endif
                        </td>
                        <td class="align-right tdOrderInputNumber">
                        <?php
                            if ($OrderRequest->UnitPrice > 0) {
                                $UnitPrice = number_format($OrderRequest->UnitPrice);
                            }
                            else{
                                $UnitPrice = '';
                            }
                            ?>
                            <span class="spnOrderInputNumber">\{{ $UnitPrice }}</span>
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
                            \{{$TotalFee}}
                        </td>
                        <td>
                            @if(App::getLocale()=='en') {{$OrderRequest->RequestUserNameEn}}
                            @else {{$OrderRequest->RequestUserNameJp}}
                            @endif
                        </td>
                        <td style="display:none;">
                            {{$OrderRequest->id}}
                        </td>
                        <td style="display:none;">
                            @if(App::getLocale()=='en') {{$OrderRequest->SupplierNameEn}}
                            @else {{$OrderRequest->SupplierNameJp}}
                            @endif
                        </td>
                        <td style="display:none;">
                            {{$OrderRequest->OrderRemark}}
                        </td>
                    </tr>
                @endforeach
                @endif
                </tbody>
            </table>
            @component('components.productDetail')
            @endcomponent
    </div>
</div>

<div id="divOrderList" class="wrapper" style="padding-bottom: 0px;display:none;">
    <div class="flexmain" >
        <h6 class="h6-title">{{ __('screenwords.orderList') }}</h6>
        <div class="divOrderHeaderButton">
            <input type="button" id="btnHowToOrder" class="btn btn-primary" value="{{ __('screenwords.order') }}" >
            <input type="button" id="btnReturnOrderRequestList" class="btn btn-secondary" value="{{ __('screenwords.back') }}">
        </div>
        <div class="pagenationStyle">
        @if(!$orderRequest_Orders->isEmpty())
            {{$orderRequest_Orders->appends(request()->query())->links()}}    
        @endif
        </div>
            <table id="table-orderProcessingFixed" class="table table-fixed table-orderProcessingFixed table-striped">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th class="align-center"><input type="checkbox" name="chkTargetAll" @if(!$orderRequest_Orders->isEmpty()) checked @endif ></th>
                        <th>@sortablelink(__('screenwords.sortSupplierName'),__('screenwords.supplierName'))</th>
                        <th>@sortablelink(__('screenwords.sortItemName'),__('screenwords.items'))</th>
                        <th class="align-center ">@sortablelink('OrderPrice',__('screenwords.orderAmount'))</th>
                        <th>@sortablelink(__('screenwords.sortBudgetName'),__('screenwords.budgetSubject'))</th>
                        <th class="align-center ">@sortablelink('OrderRemark',__('screenwords.remark'))</th>
                        <th>@sortablelink(__('screenwords.sortRequestUserName'),__('screenwords.client'))</th>
                    </tr>
                </thead>
                <tbody>
                @if(!$orderRequest_Orders->isEmpty())
                @foreach ($orderRequest_Orders as $orderRequest_Order)
                    <tr class="table-orderProcessingFixed-tr">
                        <td>
                            <form action="{{ route('Order.destroy', $orderRequest_Order->id) }}" method='post'>
                                @csrf
                                @method('DELETE')
                                <input type="submit" value="&#xf1f8;" 
                                    onClick="if (!confirm({{ __('messages.confirmDelete') }})){ return false;} return true;" class="fa btn-delete-icon">
                                <input type="hidden" name="orderreqId" value="{{$orderRequest_Order->id}}">
                           </form>
                        </td>
                        <td class="align-center"><input type="checkbox" name="chkTarget[]" checked ></td>
                        <td class="tdOrderSelectSupplier">
                            <span class="spnOrderSelectSupplier">
                                @if(App::getLocale()=='en') {{$orderRequest_Order->supplier->SupplierNameEn}}
                                @else {{$orderRequest_Order->supplier->SupplierNameJp}}
                                @endif
                            </span>
                            <select class="selOrderSelectSupplier">
                                @foreach ($Suppliers as $supplier)
                                <option value="{{$supplier->id}}">
                                    @if(App::getLocale()=='en') {{$supplier->SupplierNameEn}}
                                    @else {{$supplier->SupplierNameJp}}
                                    @endif
                                </option>
                                @endforeach
                            </select>
                            <span class="spnSupplierId">{{ $orderRequest_Order->SupplierId }}</span>
                        </td>
                        <td>
                            <p>
                                @if(App::getLocale()=='en') {{ $orderRequest_Order->item->ItemNameEn }}
                                @else {{ $orderRequest_Order->item->ItemNameJp }}
                                @endif   
                            </p>
                            <p>{{ __('screenwords.capacity') }}：{{$orderRequest_Order->item->AmountUnit}} {{ __('screenwords.standard') }}：{{$orderRequest_Order->item->Standard}} {{ __('screenwords.catalogCode') }}：{{$orderRequest_Order->item->CatalogCode}} {{ __('screenwords.maker') }}：
                                @if(App::getLocale()=='en') {{$orderRequest_Order->item->MakerNameEn}}
                                @else {{$orderRequest_Order->item->MakerNameJp}}
                                @endif
                            </p>
                        </td>
                        <?php
                            $ordersum = number_format($orderRequest_Order->UnitPrice * $orderRequest_Order->RequestNumber)
                        ?>
                        <td class="align-right">\{{ $ordersum }}</td>
                        <td>
                            @if(App::getLocale()=='en') {{ $orderRequest_Order->BudgetNameEn }}
                            @else {{ $orderRequest_Order->BudgetNameJp }}
                            @endif   
                        </td>
                        <td>{{ $orderRequest_Order->OrderRemark }}</td>
                        <td>
                            @if(App::getLocale()=='en') {{ $orderRequest_Order->user->UserNameEn }}
                            @else {{ $orderRequest_Order->user->UserNameJp }}
                            @endif   
                        </td>
                        <td style="display:none;">
                            {{$orderRequest_Order->id}}
                        </td>
                    </tr>
                @endforeach
                @endif
                </tbody>
            </table>
    </div>
    <div id="modal-howto-order" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
            <!--以下modal-dialogのCSSの部分で modal-lgやmodal-smを追加するとモーダルのサイズを変更することができる-->
            <div  class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="padding: 5px 10px;">
                        <h5 class="modal-title" id="Modal">{{ __('screenwords.orderingMethod') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('screenwords.close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="divCircleWrapper" >
                            <div id="btnCircle_Email" class="btnCircle" title="{{ __('screenwords.sendEmail') }}"><span class="fa fa-envelope-o"></span><span>e-mail</span></div>
                            <div id="btnCircle_PDF" class="btnCircle" title="{{ __('screenwords.outputPdf') }}"  data-dismiss="modal"><span class="fa fa-file-pdf-o"></span><span>pdf</span></div>
                            <form id="frmPdfOutput" action="{{action('OrderController@createPDF')}}" method="get">
                                <input type="hidden" name="arrayOrderRequestIds" >
                            </form>
                            <div id="btnCircle_Other" class="btnCircle" title="{{ __('screenwords.otherWay') }}"><span class="fa fa-pencil-square-o"></span><span>other</span></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('screenwords.close') }}</button>
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
                <th>{{ __('screenwords.budgetSubject') }}</th>
                <th>{{ __('screenwords.budgetAmount') }}</th>
                <th>{{ __('screenwords.excutionAmount') }}</th>
                <th>{{ __('screenwords.excutedBalance') }}</th>
                <th>{{ __('screenwords.excutionScheduledAmount') }}</th>
                <th>{{ __('screenwords.IncludingExecutionScheduledBalance') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($arrayBudgetList as $BudgetItem)
            <tr>
                <td>
                    @if(App::getLocale()=='en') {{ $BudgetItem['BudgetNameEn'] }}
                    @else {{ $BudgetItem['BudgetNameJp'] }}
                    @endif   
                
                </td>
                <td>\{{$BudgetItem['BudgetAmount']}}</td>
                <td>\{{$BudgetItem['BudgetUsed']}}</td>
                <td>\{{$BudgetItem['BudgetScheduled']}}</td>
                <td>\{{$BudgetItem['BudgetRemainBal']}}</td>
                <td>\{{$BudgetItem['BudgetScheduledRemain']}}</td>
            </tr>
            @endforeach

        </tbody>
    </table>
</div>
<div id="popmenu-budgetlist" role='popmenu-layer'>
    <ul role='popmenu'>
        <li class='popmenu-header'>
            <span class='fa fa-hand-o-left'></span>{{ __('screenwords.toBudgetList') }}
        </li>
        @foreach($arrayBudgetForContext as $budgetitem)
        <li id="BudgetId-{{ $budgetitem['BudgetId'] }}" name="{{$budgetitem['BudgetNameJp']}}" ><span class="fa fa-folder"></span>{{$budgetitem['BudgetNameJp']}}</li>
        @endforeach
    </ul>
</div>
<div id="popmenu-orderrequestlist" role='popmenu-layer'>
    <ul role='popmenu'>
        <li id="toOrderrequestlist" name="toOrderrequestlist" >{{ __('screenwords.toOrderRequestList') }}<span class="fa fa-hand-o-right"></span></li>
    </ul>
</div>

</div>

@endsection