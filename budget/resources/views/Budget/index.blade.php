@extends('layouts.app')

@section('content')
<script src="{{ asset('js/budgetScript.js') }}" defer></script>
<div class="container">
<h5 class="master-title">予算マスタ</h5>
<div class="wrapper">
    <div class="divMasterList master5columnList">
        <table id="tblBudgetMasterList" class="table table-fixed table-masterFixed master5column table-striped">
            <thead>
                <th></th>
                <th>予算科目</th>
                <th>予算額</th>
                <th>執行期間</th>
                <th>表示順</th>
            </thead>
            <tbody>
            @foreach($Budgets as $Budget)
            <tr>
                <td>
                    <form id="frmBudgetDelete" action="{{ route('Budget.destroy', $Budget->id) }}" method='post'>
                        @csrf
                        @method('DELETE')
                        <input type="submit" value="&#xf1f8;" 
                            onClick="if (!confirm('削除しますか？')){ return false;} return true;" class="fa btn-delete-icon">
                    </form>
                </td>
                <td><a href="{{ route('Budget.edit', $Budget->id) }}">{{$Budget->budgetNameJp}}</a></td>
                <td>{{$Budget->budgetAmount}}</td>
                <td>{{$Budget->useStartDate}}~{{$Budget->useEndDate}}</td>
                <td>{{$Budget->displayOrder}}</td>
            </tr>
            @endforeach
            </tbody>

        </table>
    </div>
    <div class="divMasterInput">
    <form id="frmBudgetMaster" class="frmBudgetMasterInput" action="{{action('BudgetController@store')}}" method="POST">
            
            {{-- エラーメッセージ --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                </div>
                <?php
                $editBudget->budgetNameJp = old('budgetNameJp');
                $editBudget->budgetAmount = old('budgetAmount');
                $editBudget->useStartDate = old('useStartDate');
                $editBudget->useEndDate = old('useEndDate');
                $editBudget->displayOrder = old('displayOrder');
                ?>
            @endif

            @csrf
            <div class="form-group">
                <label for="budgetNameJp" class="required">予算名</label>
                <input type="text" id="budgetNameJp" name="budgetNameJp" value="{{ $editBudget->budgetNameJp }}" >
           </div>
            <div class="form-group">
                <label for="budgetAmount" class="required">予算額</label>
                <input type="text" id="budgetAmount" name="ChargeUserJp" value="{{ $editBudget->budgetAmount }}" >
            </div>
            <div class="form-group">
                <label for="Tel" class="required">執行期間</label>
                <input type="date" id="useStartDate" name="useStartDate" value="{{ $editBudget->useStartDate }}">~
                <input type="date" id="useEndDate" name="useEndDate" value="{{ $editBudget->useEndDate }}">
            </div>
            <div class="form-group">
                <label for="Fax">表示順</label>
                <input type="text" id="displayOrder" name="displayOrder" value="{{ $editBudget->displayOrder }}">
            </div>
            <div class="form-group text-center">
                <button id="submit_Budget_regist" name="submit_Budget_regist" class="btn btn-primary" >保存</button>
                <input id="btn_Budget_clear" type="button" class="btn btn-secondary" value="クリア">
                <input type="hidden" id="id" name="id" value="{{ $editBudget->id }}" >
            </div>

        </form>

    </div>
</div>
</div>
@endsection