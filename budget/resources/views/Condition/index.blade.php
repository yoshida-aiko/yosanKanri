@extends('layouts.app')

@section('content')
<script src="{{ asset('js/conditionScript.js') }}" defer></script>
<div class="container">
<h5 class="master-title">{{__('screenwords.master_setting')}}</h5>
<div class="wrapper">
    <div class="divConditionInput">
    <form id="frmCondition"  action="{{route('Condition.index')}}" method="POST">
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
                $Condition->bilingual = old('bilingual');
                $Condition->SystemNameJp = old('SystemNameJp');
                $Condition->SystemNameEn = old('SystemNameEn');
                $Condition->FiscalStartMonth = old('FiscalStartMonth');
                $Condition->BulletinTerm = old('BulletinTerm');
                $Condition->NewBulletinTerm = old('NewBulletinTerm');
                // $Condition->EMail = old('email');               
                /* SMTP項目　コメント化
                $Condition->SMTPServerId = old('SMTPServerId');
                $Condition->SMTPServerPort = old('SMTPServerPort');
                $Condition->SMTPAccount = old('SMTPAccount');
                $Condition->SMTPPassword = old('SMTPPassword');
                $Condition->SMTPAuthFlag = old('SMTPAuthFlag'); */
                ?>
            @endif
            @isset ($status)
            <div class="alert alert-success">
                <ul>
                    <li>{{__('screenwords.registered')}}</li>
                </ul>
            </div>
            @endisset


            @csrf
            <fieldset>
            <legend>{{__('screenwords.environmentalSetting')}}</legend>
                <div class="form-group">
                    <label for="bilingual" class="required">{{__('screenwords.bilingual')}}</label>
                    <input type="radio" id="use" name="bilingual" value="1"  {{ $Condition->bilingual == 1 ? 'checked' : '' }}>
                    <label for="use" class="radio1">{{__('screenwords.uses')}}</label>
                    <input type="radio" id="notUse" name="bilingual" value="0" {{ $Condition->bilingual == 0 ? 'checked' : '' }}>
                    <label for="notUse" class="radio1">{{__('screenwords.dontUse')}}</label>              
                </div>
                <div class="form-group">
                    <label for="SystemNameJp" class="required">{{__('screenwords.systemNameJp')}}</label>
                    <input type="text" id="SystemNameJp" name="SystemNameJp" value="{{ $Condition->SystemNameJp }}" >
                </div>
                <div class="form-group">
                    <label for="SystemNameEn" id="lblSystemNameEn">{{__('screenwords.systemNameEn')}}</label>
                    <input type="text" id="SystemNameEn" name="SystemNameEn" value="{{ $Condition->SystemNameEn }}" {{ $Condition->bilingual == 0 ? 'readonly' : '' }}>
                </div>
                <div class="form-group">
                    <label for="FiscalStartMonth" class="required">{{__('screenwords.fiscalStartMonth')}}</label>
                    <input type="number" id="FiscalStartMonth" name="FiscalStartMonth" class="text-right" min="1" max="12" value="{{ $Condition->FiscalStartMonth }}">
                    <span>{{__('screenwords.month')}}</span>
                </div>              
           </fieldset>

           <fieldset>
            <legend>{{__('screenwords.bulletinBoard')}}</legend>
                <div class="form-group">
                    <label for="BulletinTerm"  class="required">{{__('screenwords.bulletinTerm')}}</label>
                    <input type="number" id="BulletinTerm" name="BulletinTerm" class="text-right" min="1" max="999" value="{{ $Condition->BulletinTerm }}">
                    <span>{{__('screenwords.betweenDays')}}</span>
                </div>
                <div class="form-group">
                    <label for="NewBulletinTerm"  class="required">{{__('screenwords.newBulletinTerm')}}</label>
                    <input type="number" id="NewBulletinTerm" name="NewBulletinTerm" class="text-right" min="0" max="999" value="{{ $Condition->NewBulletinTerm }}">
                    <span>{{__('screenwords.betweenDays')}}</span>
                </div>              
            </fieldset>

            {{-- 　SMTP項目、発注　コメント化
            <fieldset>
            <legend>{{__('screenwords.order')}}</legend>
                <div class="form-group">
                    <label for="email" class="required">{{__('screenwords.eMail')}}</label>
                    <input type="text" id="EMail" name="email" value="{{ $Condition->EMail }}" >
                </div>              
           </fieldset>

           <fieldset>
            <legend>SMTPサーバー</legend>
                <div class="form-group">
                    <label for="SMTPServerId"  class="required">サーバー名</label>
                    <input type="text" id="SMTPServerId" name="SMTPServerId" value="{{ $Condition->SMTPServerId }}" >
                </div>
                <div class="form-group">
                    <label for="SMTPServerPort"  class="required">ポート番号</label>
                    <input type="number" id="SMTPServerPort" name="SMTPServerPort" class="text-right"  min="1" max="99999" value="{{ $Condition->SMTPServerPort }}">
                    <span>規定値:25</span>
                </div>              
            </fieldset>

            <fieldset>
            <legend>SMTP認証</legend>
                <div class="form-group">
                    <label for="SMTPAccount" id="lblSMTPAccount"  {{ $Condition->SMTPAuthFlag == 1 ? 'class=required' : '' }}>メールアカウント</label>
                    <input type="text" id="SMTPAccount" name="SMTPAccount" value="{{ $Condition->SMTPAccount }}" >
                </div>
                <div class="form-group">
                    <label for="SMTPPassword"  id="lblSMTPPassword" {{ $Condition->SMTPAuthFlag == 1 ? 'class=required' : '' }}>パスワード</label>
                    <input type="password" id="SMTPPassword" name="SMTPPassword"  value="{{ $Condition->SMTPPassword }}">
                </div>
                <div class="form-group">
                    <input type="checkbox" id="SMTPAuthFlag" name="SMTPAuthFlag" value="{{ $Condition->SMTPAuthFlag }}" {{ $Condition->SMTPAuthFlag == 1 ? 'checked' : '' }}>
                    <label for="SMTPAuthFlag" class="chk1">メールアカウントとパスワードを使用する</label>
                </div>
            </fieldset>
            --}}

            <fieldset>
            <legend>{{__('screenwords.executionBasis')}}</legend>
                <div class="form-group text-center" id="ExecutionBasisArea">
                    <input type="radio" id="deliveryBasis" name="ExecutionBasis" value="1"  {{ $Condition->ExecutionBasis == 1 ? 'checked' : '' }}>
                    <label for="deliveryBasis" class="radio1">{{__('screenwords.deliveryBasis')}}</label>
                    <input type="radio" id="paymentBasis" name="ExecutionBasis" value="2"  {{ $Condition->ExecutionBasis == 2 ? 'checked' : '' }}>
                    <label for="paymentBasis"  class="radio1">{{__('screenwords.paymentBasis')}}</label>              
                </div>           
           </fieldset>

            <div class="form-group text-center">
                <button id="submit_condition_regist" name="send" class="btn btn-primary" type="submit" onClick="if (!confirm('{{ __('messages.confirmRegist') }}')){ return false;} return true;">{{__('screenwords.register')}}</button>
                <input id="btn_condition_clear" type="submit" name="delete" class="btn btn-secondary" value="{{__('screenwords.clear')}}">
                <input type="hidden" id="id" name="id" value="{{ $Condition->id }}" >
                <input type="hidden" id="mode" name="mode" value="{{ $mode }}" >
            </div>

        </form>
    </div>
</div>
</div>
@endsection