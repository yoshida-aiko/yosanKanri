@extends('layouts.app')

@section('content')
<script src="{{ asset('js/budgetStatusScript.js') }}" defer></script>

<div class="container">
<div class="loading"><div class="loadingwrapper"><div class="ball-grid-pulse"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><div class="loadingtext">loading...</div></div></div>
<div class="wrapper" style="flex-direction:column;">

        <div class="flexmain">
            <h6 class="h6-title">予算使用状況</h6>
            <div class="divBudgetStatusHeaderButton">
                <span>執行期間：</span>
                <input type="text" id="txtStartDate" name="startDate" class="inpExecDate" >
                <span>～</span>
                <input type="text" id="txtEndDate" name="endDate" class="inpExecDate"  >
                <input type="button" name="btnExec" value="表示" class="btn btn-primary">
                <input type="button" name="btnCSV" value="CSV" class="btn btn-secondary">
            </div>
            @if(count($BudgetLists) > 0)
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
                @foreach ($BudgetLists as $BudgetList)
                    <tr class="table-budgetFixed-tr">
                        <td>
                            <input type="button" value="その他執行" class="btn btn-secondary">
                        </td>
                        <td>{{ $BudgetList['BudgetNameJp'] }}</td>
                        <td class="align-right">{{ $BudgetList['Budget'] }}</td>
                        <td class="align-right">{{ $BudgetList['BudgetUsed'] }}</td>
                        <td class="align-right">{{ $BudgetList['BudgetRemainBal'] }}</td>
                        <td class="align-right">{{ $BudgetList['BudgetScheduled'] }}</td>
                        <td class="align-right">{{ $BudgetList['BudgetScheduledRemain'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @else
            <p>データがありません</p>
            @endif
        </div>
        <div class="flexmain">
            
            <table id="table-budgetDetailFixed" class="table table-fixed table-budgetDetailFixed table-striped">
                <thead>
                    <tr>
                        <th class="align-center ">@sortablelink('','執行日')</th>
                        <th>@sortablelink('','商品名')</th>
                        <th class="align-center ">@sortablelink('','単価')</th>
                        <th class="align-center ">@sortablelink('','数量')</th>
                        <th class="align-center ">@sortablelink('','執行額')</th>
                    </tr>
                </thead>
                <tbody>                
                @foreach ($BudgetDetails as $BudgetDetail)
                    <tr class="table-budgetDetailFixed-tr">
                        <td class="align-center">{{ $BudgetDetail['ExecDate'] }}</td>
                        <td>{{ $BudgetDetail['ItemNameJp'] }}</td>
                        <td class="align-right">{{ $BudgetDetail['UnitPrice'] }}</td>
                        <td class="align-right">{{ $BudgetDetail['ExecNumber'] }}</td>
                        <td class="align-right">{{ $BudgetDetail['ExecPrice'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            
        </div>
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

@endsection