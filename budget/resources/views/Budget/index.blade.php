@extends('layouts.app')

@section('content')

<script src="{{ asset('js/budgetScript.js') }}" defer></script>
<div class="container">
<h5 class="master-title">{{__('screenwords.master_budget')}}</h5>
<div class="wrapper">
    <div class="divMasterList master5columnList">
        <form id="frmBudgetNendo" action="{{action('BudgetController@index')}}" method="get">
        @csrf
        <div class="form-group text-right prev-masterTable">
            <label for="fiscalYear">年度</label>
            <input type="number" id="fiscalYear" name="fiscalYear" min="2001" max="2100" value="{{$Nendo}}" onchange="submit(this.form)">
        </div>
        </form>
        <table id="tblBudgetMasterList" class="table table-fixed table-masterFixed master5column table-striped">
            <thead>
                <th>&nbsp;</th>
                <th>{{__('screenwords2.budgetSubject')}}</th>
                <th>{{__('screenwords2.budgetAmount')}}</th>
                <th>{{__('screenwords2.excutionPeriod')}}</th>
                <th>{{__('screenwords2.displayOrder')}}</th>
            </thead>
            <tbody>
            @foreach($Budgets as $Budget)
            <tr>
                <td>
                    <form id="frmBudgetDelete" action="{{ route('Budget.destroy', $Budget->id) }}" method='post'>
                        @csrf
                        @method('DELETE')
                        <input type="submit" value="&#xf1f8;" 
                            onClick="if (!confirm('{{ __('messages.confirmDelete') }}')){ return false;} return true;" class="fa btn-delete-icon">
                    </form>
                </td>
                <td><a href="{{ route('Budget.edit', $Budget->id ) }}">{{ App::getLocale()=='en' ? $Budget->budgetNameEn : $Budget->budgetNameJp}}</a></td>
                <td id="td-budgetAmount" class="text-right">{{$Budget->budgetAmount}}</td>
                <td id="td-useStartDate">{{$Budget->useStartDate}}~{{$Budget->useEndDate}}</td>
                <td class="text-right">{{$Budget->displayOrder}}</td>
            </tr>
            @endforeach
            </tbody>

        </table>
    </div>
    <div class="divMasterInput">
    <form id="frmBudgetMaster" action="{{action('BudgetController@store')}}" method="POST">
            
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
                $editBudget->budgetNameEn = old('budgetNameEn');
                $editBudget->budgetAmount = old('budgetAmount');
                $editBudget->useStartDate = old('useStartDate');
                $editBudget->useEndDate = old('useEndDate');
                $editBudget->displayOrder = old('displayOrder');
                ?>
            @endif
            @if (session('exclusiveError'))
            <div class="alert alert-danger">
                <ul>
                    <li>{{ session('exclusiveError') }}</li>
                </ul>
            </div>
            @endif
            
            @csrf
            <div class="form-group">
                <label for="budgetNameJp" class="required">{{__('screenwords2.budgetNameJp')}}</label>
                <input type="text" id="budgetNameJp" name="budgetNameJp" value="{{ $editBudget->budgetNameJp }}" >
           </div>
           <div class="form-group">
                <label for="budgetNameEn" id="lblBudgetNameEn">{{__('screenwords2.budgetNameEn')}}</label>
                <input type="text" id="budgetNameEn" name="budgetNameEn" value="{{ $editBudget->budgetNameEn }}"  {{ session('bilingual') == 0 ? 'readonly' : '' }} >
           </div>
            <div class="form-group">
                <label for="budgetAmount" class="required">{{__('screenwords2.budgetAmount')}}</label>
                <input type="number" id="budgetAmount" name="budgetAmount" class="text-right" min="0" max="999999999" value="{{ $editBudget->budgetAmount }}" > {{__('screenwords2.yen')}}
            </div>
            <div class="form-group">
                <label for="useDate" class="required">{{__('screenwords2.excutionPeriod')}}</label>
                <input type="text" id="useStartDate" name="useStartDate" readonly="readonly" value="{{ $editBudget->useStartDate }}"> ~
                <input type="text" id="useEndDate" name="useEndDate"  readonly="readonly" value="{{ $editBudget->useEndDate }}">
            </div>
            <div class="form-group">
                <label for="Fax">{{__('screenwords2.displayOrder')}}</label>
                <input type="number" id="displayOrder" name="displayOrder" class="text-right" min="0" max="999" value="{{ $editBudget->displayOrder }}">
            </div>
            <div class="form-group text-center">
                <button id="submit_Budget_regist" name="submit_Budget_regist" class="btn btn-primary" >{{__('screenwords2.register')}}</button>
                <input id="btn_Budget_clear" type="button" class="btn btn-secondary" value="{{__('screenwords2.clear')}}">
                <input type="hidden" id="id" name="id" value="{{ $editBudget->id }}" >
                <input type="hidden" id="year" name="year" value="{{$Nendo}}" >
                <input type="hidden" id="hidStatDt" name="hidStatDt" value="{{ $editBudget->useStartDate }}">
                <input type="hidden" id="hidEndDt" name="hidEndDt" value="{{ $editBudget->useEndDate }}">
                <input type="hidden" id="bilingual" name="bilingual" value="{{ session('bilingual') }}" >
            </div>

        </form>

    </div>
</div>
</div>
@endsection