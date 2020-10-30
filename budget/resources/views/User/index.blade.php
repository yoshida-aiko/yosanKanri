@extends('layouts.app')

@section('content')
<script src="{{ asset('js/userScript.js') }}" defer></script>
<div class="container">
<h5 class="master-title">ユーザーマスタ</h5>
<div class="wrapper">
    <div class="divMasterList">
        <table id="tblUserMasterList" class="table table-fixed table-masterFixed table-striped">
        <thead>
            <th>&nbsp;</th>
            <th style="min-width:200px;">ユーザー名</th>
        </thead>
        <tbody>
        @if (strpos(Auth::user()->UserAuthString,'Master') !== false) 
        @foreach($Users as $User)
        <tr>
            <td>
                <form id="frmUserDelete" action="{{ route('User.destroy', $User->id) }}" method='post'>
                    @csrf
                    @method('DELETE')
                    <input type="submit" value="&#xf1f8;" 
                        onClick="if (!confirm('削除しますか？')){ return false;} return true;" class="fa btn-delete-icon">
                </form>
            </td>
            <td><a href="{{ route('User.edit', $User->id) }}">{{$User->UserNameJp}}</a></td>
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
            
            @csrf
            <div class="form-group">
                <label for="LoginAccount" class="required">ユーザーID</label>
                <input type="text" id="LoginAccount" name="LoginAccount" value="{{ $editUser->LoginAccount }}"
                    @if (strpos(Auth::user()->UserAuthString,'Master') === false) readonly="readonly" @endif  >
           </div>
            <div class="form-group">
                @if ($editUser->password == 'resetLink')
                <label id="resetLinkLabel" class="resetLinkOn">&nbsp;</label>
                <a id="resetLinkAnchor" class="btn btn-primary resetLinkOn" href="{{ route('password.request') }}">パスワード再設定</a>
                <label id="passwordLabel" for="password" class="required resetLinkOff">パスワード</label>
                <input id="resetLinkInput" type="text" id="password" name="password" value="" class="resetLinkOff">
                @else
                <label class="resetLinkOff">&nbsp;</label>
                <a class="btn btn-primary resetLinkOff" href="{{ route('password.request') }}">パスワード再設定</a>
                <label for="password" class="required resetLinkOn">パスワード</label>
                <input type="text" id="password" name="password" value="" class="resetLinkOn" autocomplete="off">
                @endif               
            </div>
            <div class="form-group">
                <label for="UserNameJp" class="required">ユーザー名</label>
                <input type="text" id="UserNameJp" name="UserNameJp" value="{{ $editUser->UserNameJp }}" 
                    @if (strpos(Auth::user()->UserAuthString,'Master') === false) readonly="readonly" @endif  >
            </div>
            <div class="form-group">
                <label for="Tel">連絡先（携帯等）</label>
                <input type="tel" id="Tel" name="Tel" value="{{ $editUser->Tel }}"
                    @if (strpos(Auth::user()->UserAuthString,'Master') === false) readonly="readonly" @endif  >
            </div>
            <div class="form-group">
                <label for="email" class="required">メールアドレス</label>
                <input type="email" id="email" name="email" value="{{ $editUser->email }}" >
            </div>
            <div class="form-group">
                <label for="author">権限</label>
                <fieldset id="author" >
                <div class="userAuthorOverlay" @if (strpos(Auth::user()->UserAuthString,'Master') === false) style="display:block;" @endif></div>
                    <input type="checkbox" id="chkOrder" name="chkAuthor[]" value="Order" @if(strpos($editUser->UserAuthString,'Order') !== false) checked='checked' @endif >
                    <label for="chkOrder">発注</label>

                    <input type="checkbox" id="chkDelivery" name="chkAuthor[]" value="Delivery" @if (strpos($editUser->UserAuthString,'Delivery') !== false) checked='checked' @endif >
                    <label for="chkDelivery">納品</label>

                    <input type="checkbox" id="chkBudget" name="chkAuthor[]" value="Budget" @if (strpos($editUser->UserAuthString,'Budget') !== false) checked='checked' @endif >
                    <label for="chkBudget">予算状況</label><br />
                    
                    <input type="checkbox" id="chkPurchase" name="chkAuthor[]" value="Purchase" @if (strpos($editUser->UserAuthString,'Purchase') !== false) checked='checked' @endif >
                    <label for="chkPurchase">購入履歴</label>
                    
                    <input type="checkbox" id="chkMaster" name="chkAuthor[]" value="Master" @if (strpos($editUser->UserAuthString,'Master') !== false) checked='checked' @endif >
                    <label for="chkMaster">マスタ</label>

                    <input type="checkbox" id="chkPayment" name="chkAuthor[]" value="Payment" @if (strpos($editUser->UserAuthString,'Payment') !== false) checked='checked' @endif >
                    <label for="chkPayment">支払</label>
                    
                </fieldset>
            </div>
            <div class="form-group">
                <label for="Signature">署名</label>
                <textarea id="Signature" name="Signature" 
                    @if (strpos(Auth::user()->UserAuthString,'Master') === false) readonly="readonly" @endif  >{{ $editUser->Signature }}</textarea>
                <div class="alert-string" style="margin-left:150px;">※この署名は注文時にメールやPDFに使用されます。</div>
            </div>
            <div class="form-group text-center">
                <button id="submit_user_regist" type="submit" name="submit_user_regist" class="btn btn-primary" >保存</button>
                <input id="btn_user_clear" type="button" class="btn btn-secondary" value="クリア" @if (strpos(Auth::user()->UserAuthString,'Master') === false) disabled="disabled" @endif>
                <input type="hidden" id="id" name="id" value="{{ $editUser->id }}" >
            </div>

        </form>
    </div>
</div></div>
@endsection