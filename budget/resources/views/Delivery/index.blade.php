@extends('layouts.app')

@section('content')
<script src="{{ asset('js/deliveryScript.js') }}" defer></script>

<div class="container">
<div class="loading"><div class="loadingwrapper"><div class="ball-grid-pulse"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><div class="loadingtext">loading...</div></div></div>
<div class="wrapper">

    <div class="flexmain" >
        <h6 class="h6-title">{{ __('screenwords.deliveryList') }}</h6>
        <div class="divOrderRequestHeaderButton">
            <input type="button" id="btnDelivery" value="{{ __('screenwords.delivery') }}" class="btn btn-width70 btn-primary" >
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
                        <th>@sortablelink(__('screenwords.sortItemName'),__('screenwords.items'))</th>
                        <th>@sortablelink(__('screenwords.sortSupplierName'),__('screenwords.supplierName'))</th>
                        <th class="align-center ">@sortablelink('OrderSlipNo',__('screenwords.orderSlipNo'))</th>
                        <th class="align-center ">{{__('screenwords.deliveryDate')}}</th>
                        <th class="align-center ">@sortablelink('DeliveryNumber',__('screenwords.completedNumber'))</th>
                        <th class="align-center ">@sortablelink('OrderNumber',__('screenwords.orderNumber'))</th>
                        <th class="align-center ">{{__('screenwords.deliveryNumber')}}</th>
                        <th class="align-center ">{{__('screenwords.excutionAmount')}}</th>
                        <th>@sortablelink(__('screenwords.sortRequestUserName'),__('screenwords.client'))</th>
                        <th>@sortablelink(__('screenwords.sortBudgetName'),__('screenwords.budgetSubject'))</th>
                        <th>@sortablelink(__('screenwords.sortRecieveUserName'),__('screenwords.orderUser'))</th>
                    </tr>
                </thead>
                <tbody>
                @if(!$Orders->isEmpty())
                @foreach ($Orders as $order)
                    <tr class="table-deliveryFixed-tr">
                        <td>
                            <form action="{{ route('Delivery.destroy', $order->id) }}" method='post'>
                                @csrf
                                @method('DELETE')
                                <input type="submit" value="&#xf1f8;" 
                                    onClick="if (!confirm({{ __('messages.confirmDelete') }})){ return false;} return true;" class="fa btn-delete-icon">
                                <input type="hidden" name="orderId" value="{{$order->id}}">
                            </form>
                        </td>
                        <td><input type="checkbox" name="chkTarget[]" ></td>
                        <td>
                            <p>
                                @if(App::getLocale()=='en') {{ $order->item->ItemNameEn }}
                                @else {{ $order->item->ItemNameJp }}
                                @endif   
                            </p>
                            <p>{{ __('screenwords.capacity') }}：{{$order->item->AmountUnit}} {{ __('screenwords.standard') }}：{{$order->item->Standard}} {{ __('screenwords.catalogCode') }}：{{$order->item->CatalogCode}} {{ __('screenwords.maker') }}：
                                @if(App::getLocale()=='en') {{$order->item->MakerNameEn}}
                                @else {{$order->item->MakerNameJp}}
                                @endif
                            </p>
                        </td>
                        <td>
                            @if(App::getLocale()=='en') {{ $order->SupplierNameEn }}
                            @else {{ $order->SupplierNameJp }}
                            @endif   
                        </td>
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
                            <input type="text" name="deliveryDate" class="inpDeliveryDate" value="{{$DeliveryDate}}" title="{{__('screenwords.deliveryDateRange')}}">
                            <input type="hidden" name="hidUseEndDate" value="{{$order->UseEndDate}}">
                            <input type="hidden" name="hidOrderDate" value="{{$order->OrderDate}}">
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
                            <span class="spnOrderInputNumber">\{{ $SummaryFormat }}</span>
                            <input type="text" class="inpOrderInputNumber inpSummaryPrice" pattern="[0-9]*"  title="数字のみ" min="1" value="{{ $Summary }}" >
                        </td>
                        <td>
                            @if(App::getLocale()=='en') {{ $order->RequestUserNameEn }}
                            @else {{ $order->RequestUserNameJp }}
                            @endif   
                        </td>
                        <td class="tdBudget">
                            <span class="spnSelectBudget">
                                @if(App::getLocale()=='en') {{ $order->BudgetNameEn }}
                                @else {{ $order->BudgetNameJp }}
                                @endif   
                            </span>
                            <select class="selSelectBudget">
                                @foreach($Budgets as $Budget)
                                <option value="{{$Budget->id}}">
                                    @if(App::getLocale()=='en') {{ $Budget->budgetNameEn }}
                                    @else {{ $Budget->budgetNameJp }}
                                    @endif   
                                </option>
                                @endforeach
                            </select>
                            <span class="spnBudgetId" style="display:none;">{{ $order->BudgetId }}</span>
                        </td>
                        <td>
                            @if(App::getLocale()=='en') {{ $order->RecieveUserNameEn }}
                            @else {{ $order->RecieveUserNameJp }}
                            @endif   
                        </td>
                        <td class="tdAmountUnit" style="display:none;">{{$order->item->AmountUnit}}</td>
                        <td class="tdStandard" style="display:none;">{{$order->item->Standard}}</td>
                        <td class="tdCatalogCode" style="display:none;">{{$order->item->CatalogCode}}</td>
                        <td class="tdUnitPrice" style="display:none;">{{$order->UnitPrice}}</td>
                        <td class="tdMakerName" style="display:none;">
                            @if(App::getLocale()=='en') {{ $order->item->MakerNameEn }}
                            @else {{ $order->item->MakerNameJp }}
                            @endif   
                        </td>
                        <td class="tdSupplierName" style="display:none;">
                            @if(App::getLocale()=='en') {{ $order->MainSupplierNameEn }}
                            @else {{ $order->MainSupplierNameJp }}
                            @endif   
                        </td>
                        <td class="tdItemName" style="display:none;">
                            @if(App::getLocale()=='en') {{ $order->item->ItemNameEn }}
                            @else {{ $order->item->ItemNameJp }}
                            @endif   
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
</div>

@endsection