@extends('layouts.app')

@section('content')
<script src="{{ asset('js/budgetStatusScript.js') }}" defer></script>

<div class="container">
<div class="loading"><div class="loadingwrapper"><div class="ball-grid-pulse"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><div class="loadingtext">loading...</div></div></div>
<div class="wrapper" style="flex-direction:column;">
        <div class="flexmain">
            <h6 class="h6-title">{{ __('screenwords.budgetUsageCondition') }}</h6>
            <div class="divBudgetStatusHeaderButton">
                <form class="budgetStatusConditionForm" action="{{route('BudgetStatus.index')}}" method="GET">
                    <span>{{ __('screenwords.excutionPeriod') }}：</span>
                    <input type="text" id="txtStartDate" name="startDate" class="inpExecDate" readonly="readonly" value="{{$startDate}}" >
                    <span>～</span>
                    <input type="text" id="txtEndDate" name="endDate" class="inpExecDate" readonly="readonly"  value="{{$endDate}}" >
                    <input type="button" name="btnExec" value="{{ __('screenwords.display') }}" class="btn btn-width70 btn-primary">
                    <input type="button" name="btnCSV" value="CSV" class="btn btn-width70 btn-secondary" @if(count($BudgetLists) < 1) disabled='disabled' @endif>
                    <input type="hidden" id="hidSelectedBudgetId" name="hidSelectedBudgetId" value="{{$hidSelectedBudgetId}}" >
                </form>
            </div>
            
            <table id="table-budgetFixed" class="table table-fixed table-budgetFixed table-striped">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>@sortablelink(__('screenwords.sortBudgetName'),__('screenwords.budgetSubject'))</th>
                        <th class="align-center ">{{__('screenwords.budgetAmount')}}</th>
                        <th class="align-center ">{{__('screenwords.excutionAmount')}}</th>
                        <th class="align-center ">{{__('screenwords.excutedBalance')}}</th>
                        <th class="align-center ">{{__('screenwords.excutionScheduledAmount')}}</th>
                        <th class="align-center ">{{__('screenwords.IncludingExecutionScheduledBalance')}}</th>
                    </tr>
                </thead>
                <tbody>
                @if(count($BudgetLists) > 0)             
                @foreach ($BudgetLists as $BudgetList)
                    <tr class="table-budgetFixed-tr">
                        <td>
                            <input type="button" name="btnOtherExec" value="{{ __('screenwords.adjust') }}" class="btn btn-secondary">
                        </td>
                        <td>
                            <p class="lnkDetail">
                            @if(App::getLocale()=='en') {{$BudgetList['BudgetNameEn']}}
                            @else {{$BudgetList['BudgetNameJp']}}
                            @endif
                            </p>
                            <input type="hidden" name="hidBudgetId" class="hidBudgetId" value="{{$BudgetList['BudgetId']}}" >
                        </td>
                        <td class="align-right">\{{ $BudgetList['Budget'] }}</td>
                        <td class="align-right">\{{ $BudgetList['BudgetUsed'] }}</td>
                        <td class="align-right">\{{ $BudgetList['BudgetRemainBal'] }}</td>
                        <td class="align-right">\{{ $BudgetList['BudgetScheduled'] }}</td>
                        <td class="align-right">\{{ $BudgetList['BudgetScheduledRemain'] }}</td>
                    </tr>
                @endforeach
                @endif
                </tbody>
            </table>

        </div>
        <div class="flexmain">
            
            <table id="table-budgetDetailFixed" class="table table-fixed table-budgetDetailFixed table-striped">
                <thead>
                    <tr>
                        <th class="align-center ">{{__('screenwords.excutionDate')}}</th>
                        <th>{{__('screenwords.itemName')}}</th>
                        <th class="align-center ">{{__('screenwords.unitPrice')}}</th>
                        <th class="align-center ">{{__('screenwords.quantity')}}</th>
                        <th class="align-center ">{{__('screenwords.excutionAmount')}}</th>
                    </tr>
                </thead>
                <tbody id="table-budgetDetailFixed-tbody">
                @if ($BudgetDetails <> null)                
                @foreach ($BudgetDetails as $BudgetDetail)
                    <tr class="table-budgetDetailFixed-tr">
                        <td class="align-center">{{ $BudgetDetail['ExecDate'] }}</td>
                        <td>
                            @if(App::getLocale()=='en') {{$BudgetDetail['ItemNameEn']}}
                            @else {{$BudgetDetail['ItemNameJp']}}
                            @endif
                        </td>
                        <td class="align-right">{{ $BudgetDetail['UnitPrice'] }}</td>
                        <td class="align-right">{{ $BudgetDetail['ExecNumber'] }}</td>
                        <td class="align-right">\{{ $BudgetDetail['ExecPrice'] }}</td>
                    </tr>
                @endforeach
                @endif
                </tbody>
            </table>
            
        </div>
        <div id="modal-oherExec" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
            <!--以下modal-dialogのCSSの部分で modal-lgやmodal-smを追加するとモーダルのサイズを変更することができる-->
            <div  class="modal-dialog modal-sm100" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="Modal">{{ __('screenwords.adjust') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('screenwords.close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <section class="update adjust">
                        @csrf
                            <div class="form-group" style="margin-top:10px;">
                                <label for="txtExecDate" class="required">{{__('screenwords.excutionDate')}}</label>
                                <input type="text" id="txtExecDate" name="ExecDate" value="{{old('ExecDate')}}">
                            </div>
                            <div class="form-group" style="margin-top:10px;">
                                <label for="txtExecRemark" class="required">{{__('screenwords.remark')}}</label>
                                <input type="text" id="txtExecRemark" name="ExecRemark" value="{{old('ExecRemark')}}">
                            </div>
                            <div class="form-group" style="margin-top:10px;">
                                <label for="txtExecPrice" class="required">{{__('screenwords.excutionAmount')}}</label>
                                <input type="text" id="txtExecPrice" class="align-right" name="ExecPrice" value="{{old('ExecPrice')}}">
                            </div>
                            {{-- エラーメッセージ --}}
                            <div id="divError" class="alert alert-danger" style="display:none;" >
                            <ul></ul>
                            </div>
                        </section>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnBalanceExec" class="btn btn-width70 btn-primary">{{__('screenwords.excution')}}</button>
                        <button type="button" id="btnClear" class="btn btn-width70 btn-primary">{{__('screenwords.clear')}}</button>
                        <button type="button" class="btn btn-width70 btn-secondary" data-dismiss="modal">{{__('screenwords.close')}}</button>
                    </div>
                </div>
            </div>
        </div>
</div>
</div>

@endsection