@extends('layouts.app')

@section('content')
<script src="{{ asset('js/budgetStatusScript.js') }}" defer></script>

<div class="container">
<div class="loading"><div class="loadingwrapper"><div class="ball-grid-pulse"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><div class="loadingtext">loading...</div></div></div>
<div class="wrapper" style="flex-direction:column;">
        <div class="flexmain">
            <h6 class="h6-title">予算使用状況</h6>
            <div class="divBudgetStatusHeaderButton">
                <form class="budgetStatusConditionForm" action="{{route('BudgetStatus.index')}}" method="GET">
                    <span>執行期間：</span>
                    <input type="text" id="txtStartDate" name="startDate" class="inpExecDate" readonly="readonly" value="{{$startDate}}" >
                    <span>～</span>
                    <input type="text" id="txtEndDate" name="endDate" class="inpExecDate" readonly="readonly"  value="{{$endDate}}" >
                    <input type="button" name="btnExec" value="表示" class="btn btn-width70 btn-primary">
                    <input type="button" name="btnCSV" value="CSV" class="btn btn-width70 btn-secondary" @if(count($BudgetLists) < 1) disabled='disabled' @endif>
                    <input type="hidden" id="hidSelectedBudgetId" name="hidSelectedBudgetId" value="{{$hidSelectedBudgetId}}" >
                </form>
            </div>
            
            <table id="table-budgetFixed" class="table table-fixed table-budgetFixed table-striped">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>@sortablelink('','予算科目')</th>
                        <th class="align-center ">@sortablelink('','予算額')</th>
                        <th class="align-center ">@sortablelink('','執行額')</th>
                        <th class="align-center ">@sortablelink('','執行済残高')</th>
                        <th class="align-center ">@sortablelink('','執行予定額')</th>
                        <th class="align-center ">@sortablelink('','執行予定込残高')</th>
                    </tr>
                </thead>
                <tbody>
                @if(count($BudgetLists) > 0)             
                @foreach ($BudgetLists as $BudgetList)
                    <tr class="table-budgetFixed-tr">
                        <td>
                            <input type="button" name="btnOtherExec" value="その他執行" class="btn btn-secondary">
                        </td>
                        <td>
                            <p class="lnkDetail">{{ $BudgetList['BudgetNameJp'] }}</p>
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
                        <th class="align-center ">執行日</th>
                        <th>商品名</th>
                        <th class="align-center ">単価</th>
                        <th class="align-center ">数量</th>
                        <th class="align-center ">執行額</th>
                    </tr>
                </thead>
                <tbody id="table-budgetDetailFixed-tbody">
                @if ($BudgetDetails <> null)                
                @foreach ($BudgetDetails as $BudgetDetail)
                    <tr class="table-budgetDetailFixed-tr">
                        <td class="align-center">{{ $BudgetDetail['ExecDate'] }}</td>
                        <td>{{ $BudgetDetail['ItemNameJp'] }}</td>
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
                        <h5 class="modal-title" id="Modal">その他の執行</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <section class="update">
                        @csrf
                            <div class="form-group" style="margin-top:10px;">
                                <label for="txtExecDate" class="required">執行日</label>
                                <input type="text" id="txtExecDate" name="ExecDate" value="{{old('ExecDate')}}">
                            </div>
                            <div class="form-group" style="margin-top:10px;">
                                <label for="txtExecRemark" class="required">備考</label>
                                <input type="text" id="txtExecRemark" name="ExecRemark" value="{{old('ExecRemark')}}">
                            </div>
                            <div class="form-group" style="margin-top:10px;">
                                <label for="txtExecPrice" class="required">執行額</label>
                                <input type="text" id="txtExecPrice" class="align-right" name="ExecPrice" value="{{old('ExecPrice')}}">
                            </div>
                            {{-- エラーメッセージ --}}
                            <div id="divError" class="alert alert-danger" style="display:none;" >
                            <ul></ul>
                            </div>
                        </section>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnBalanceExec" class="btn btn-width70 btn-primary">執行</button>
                        <button type="button" id="btnClear" class="btn btn-width70 btn-primary">クリア</button>
                        <button type="button" class="btn btn-width70 btn-secondary" data-dismiss="modal">閉じる</button>
                    </div>
                </div>
            </div>
        </div>




</div>
</div>

@endsection