@extends('layouts.app')

@section('content')
<script src="{{ asset('js/userScript.js') }}" defer></script>
<div class="container">
<h5 class="master-title">{{__('screenwords.master_user')}}</h5>
<div class="wrapper">
    <div class="divMasterList">
        <table id="tblUserMasterList" class="table table-fixed table-masterFixed table-striped">
        <thead>
            <th>&nbsp;</th>
            <th style="min-width:200px;">{{__('screenwords.userName')}}</th>
        </thead>
        <tbody>
        @if (strpos(Auth::user()->UserAuthString,'Master') !== false) 
        @foreach($Users as $User)
        <tr>
            <td>
                @if($User->BuiltinUser <> 1)
                <form id="frmUserDelete" action="{{ route('User.destroy', $User->id) }}" method='post'>
                    @csrf
                    @method('DELETE')
                    <input type="submit" value="&#xf1f8;" 
                        onClick="if (!confirm('{{ __('messages.confirmDelete') }}')){ return false;} return true;" class="fa btn-delete-icon">
                </form>
                @endif
            </td>
            <td><a href="{{ route('User.edit', $User->id) }}">{{ App::getLocale()=='en' ? $User->UserNameEn : $User->UserNameJp}}</a></td>
        </tr>
        @endforeach
        @endif
        </tbody>

        </table>
    </div>
    <div class="divMasterInput">
        <form id="frmUserMaster" class="frmMasterInput" action="{{action('UserController@store')}}" method="POST">
            
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
                $editUser->LoginAccount = old('LoginAccount');
                $editUser->UserNameJp = old('UserNameJp');
                $editUser->UserNameEn = old('UserNameEn');
                $editUser->Tel = old('Tel');
                $editUser->email = old('email');
                $editUser->Signature = old('Signature');
                $editUser->UseAuth = old('UserAuth');
                ?>
            @endif
            @if (session('exclusiveError'))
            <div class="alert alert-danger">
                <ul>
                    <li>{{ session('exclusiveError') }}</li>
                </ul>
            </div>
            @endif
            @if (session('deleteError'))
            <div class="alert alert-danger">
                <ul>
                    <li>{{ session('deleteError') }}</li>
                </ul>
            </div>
            @endif
            @isset ($status)
            <div class="alert alert-success">
                <ul>
                    <li>{{__('screenwords.registered')}}</li>
                </ul>
            </div>
            @endisset
            
            @csrf
            <div class="form-group">
                <label for="LoginAccount" class="required">{{__('screenwords.loginAccount')}}</label>
                <input type="text" id="LoginAccount" name="LoginAccount" value="{{ $editUser->LoginAccount }}"
                    @if (strpos(Auth::user()->UserAuthString,'Master') === false) readonly="readonly" @endif  >
           </div>
            <div class="form-group">
                @if ($editUser->password == 'resetLink')
                <label id="resetLinkLabel" class="resetLinkOn">&nbsp;</label>
                <a id="resetLinkAnchor" class="btn btn-primary resetLinkOn" href="{{ route('password.request') }}">{{__('screenwords.passwordReset')}}</a>
                <label id="passwordLabel" for="password" class="required resetLinkOff">{{__('screenwords.password')}}</label>
                <input id="resetLinkInput" type="text" id="password" name="password" value="" class="resetLinkOff">
                @else
                <label class="resetLinkOff">&nbsp;</label>
                <a class="btn btn-primary resetLinkOff" href="{{ route('password.request') }}">{{__('screenwords.passwordReset')}}</a>
                <label for="password" class="required resetLinkOn">{{__('screenwords.password')}}</label>
                <input type="text" id="password" name="password" value="" class="resetLinkOn" autocomplete="off">
                @endif               
            </div>
            <div class="form-group">
                <label for="UserNameJp" class="required">{{__('screenwords.userNameJp')}}</label>
                <input type="text" id="UserNameJp" name="UserNameJp" value="{{ $editUser->UserNameJp }}" 
                    @if (strpos(Auth::user()->UserAuthString,'Master') === false) readonly="readonly" @endif  >
            </div>
            <div class="form-group">
                <label for="UserNameEn" id="lblUserNameEn">{{__('screenwords.userNameEn')}}</label>
                <input type="tel" id="UserNameEn" name="UserNameEn" value="{{ $editUser->UserNameEn }}" 
                    @if ((strpos(Auth::user()->UserAuthString,'Master') === false) || (session('bilingual')  == 0)) readonly="readonly" @endif  >
            </div>
            <div class="form-group">
                <label for="Tel">{{__('screenwords.contactInfomation')}}</label>
                <input type="tel" id="Tel" name="Tel" value="{{ $editUser->Tel }}"
                    @if (strpos(Auth::user()->UserAuthString,'Master') === false) readonly="readonly" @endif  >
            </div>
            <div class="form-group">
                <label for="email" class="required">{{__('screenwords.eMail')}}</label>
                <input type="email" id="email" name="email" value="{{ $editUser->email }}" >
            </div>
            <div class="form-group">
                <label for="author">{{__('screenwords.authority')}}</label>
                <fieldset id="author" >
                <div class="userAuthorOverlay" @if (strpos(Auth::user()->UserAuthString,'Master') === false) style="display:block;" @endif></div>
                    <input type="checkbox" id="chkOrder" name="chkAuthor[]" value="Order" @if(strpos($editUser->UserAuthString,'Order') !== false) checked='checked' @endif >
                    <label for="chkOrder">{{__('screenwords.order')}}</label>

                    <input type="checkbox" id="chkDelivery" name="chkAuthor[]" value="Delivery" @if (strpos($editUser->UserAuthString,'Delivery') !== false) checked='checked' @endif >
                    <label for="chkDelivery">{{__('screenwords.delivery')}}</label>

                    <input type="checkbox" id="chkBudget" name="chkAuthor[]" value="Budget" @if (strpos($editUser->UserAuthString,'Budget') !== false) checked='checked' @endif >
                    <label for="chkBudget">{{__('screenwords.budget')}}</label><br />
                    
                    <input type="checkbox" id="chkPurchase" name="chkAuthor[]" value="Purchase" @if (strpos($editUser->UserAuthString,'Purchase') !== false) checked='checked' @endif >
                    <label for="chkPurchase">{{__('screenwords.purchase')}}</label>
                    
                    <input type="checkbox" id="chkMaster" name="chkAuthor[]" value="Master" @if (strpos($editUser->UserAuthString,'Master') !== false) checked='checked' @endif >
                    <label for="chkMaster">{{__('screenwords.master')}}</label>

                    <!--<input type="checkbox" id="chkPayment" name="chkAuthor[]" value="Payment" @if (strpos($editUser->UserAuthString,'Payment') !== false) checked='checked' @endif >
                    <label for="chkPayment">{{__('screenwords.payment')}}</label>-->
                    
                </fieldset>
            </div>
            <div class="form-group">
                <label for="Signature">{{__('screenwords.signature')}}</label>
                <textarea id="Signature" name="Signature" 
                    @if (strpos(Auth::user()->UserAuthString,'Master') === false) readonly="readonly" @endif  >{{ $editUser->Signature }}</textarea>
                <div class="alert-string" style="margin-left:150px;">{{__('screenwords.signatureAnnotation')}}</div>
            </div>
            <div class="form-group text-center">
                <button id="submit_user_regist" type="submit" name="submit_user_regist" class="btn btn-primary" onClick="if (!confirm('{{ __('messages.confirmRegist') }}')){ return false;} return true;" >{{__('screenwords.register')}}</button>
                <input id="btn_user_clear" type="button" class="btn btn-secondary" value="{{__('screenwords.clear')}}" @if (strpos(Auth::user()->UserAuthString,'Master') === false) disabled="disabled" @endif>
                <input type="hidden" id="id" name="id" value="{{ $editUser->id }}" >
                <input type="hidden" id="bilingual" name="bilingual" value="{{ session('bilingual') }}" >
                <input type="hidden" id="editpass" name="editpass" value="{{ $editUser->password }}" >
            </div>

        </form>
    </div>
</div></div>
@endsection