@extends('layouts.app')

@section('content')
<script type="application/javascript" src="{{ asset('js/homeScript.js') }}" defer></script>

<div class="container">
<div class="wrapper">

    <div class="leftside-fixed-280">
        <h6 class="h6-title">掲示板</h6>
        
        <button type="button" id="btnModalBulletinBoad" class="btn btn-primary" >新規登録</button>
        <section class="info">
        @foreach($BulletinBoards as $BulletinBoard)
            <article class="bulletinArticle">
                <time>{{$BulletinBoard->RegistDate}}</time><span>{{$BulletinBoard->user->UserNameJp}}</span>
                <h6>{{$BulletinBoard->Title}}</h6>
                <p>{{$BulletinBoard->Contents}}</p>
                <input type="hidden" name="BulletinBoadIdlist" value="{{$BulletinBoard->id}}" >
                <input type="hidden" name="RegistUserIdlist" value="{{$BulletinBoard->user->id}}" >
                <input type="hidden" name="LimitDatelist" value="{{$BulletinBoard->LimitDate}}" >
            </article>
        @endforeach
        </section>
        
        <div id="modal-bulletinboad" class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
        <!--以下modal-dialogのCSSの部分で modal-lgやmodal-smを追加するとモーダルのサイズを変更することができる-->
        <div  class="modal-dialog modal-sm100" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="Modal">掲示板</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{action('HomeController@bulletinBoadStore')}}">   
                <div class="modal-body">
                    <section class="update">
                        @csrf
                        <input type="hidden" id="BulletinBoadId" name="BulletinBoadId" >
                        <input type="hidden" id="RegistUserId" name="RegistUserId" >
                        <input type="hidden" id="DeleteFlag" name="DeleteFlag" >
                        <div class="form-group" style="margin-top:10px;">
                            <label for="RegistDate">登録日</label>
                            <input type="text" id="RegistDate" name="RegistDate" value="{{old('RegistDate')}}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="Title" class="required">タイトル</label>
                            <input type="text" id="Title" name="Title" value="{{old('Title')}}" required="required" size="50">
                        </div>
                        <div class="form-group">
                            <label for="Contents" class="required">内容</label>
                            <textarea id="Contents" name="Contents" value="{{old('Contents')}}" rows="10" required="required" size="500"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="LimitDate" class="required">表示期限</label>
                            <input type="date" id="LimitDate" name="LimitDate" value="{{old('LimitDate')}}"  required="required">
                        </div>
                        {{-- エラーメッセージ --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            </div>
                        @endif
                    </section>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_bulletinboad_save" name="submit_bulletinboad" class="btn btn-primary" value="保存" />
                    <input type="submit" id="submit_bulletinboad_delete" name="submit_bulletinboad" class="btn btn-primary" value="削除" 
                            onClick="if (!confirm('削除しますか？')){ return false;} document.getElementById('DeleteFlag').value = '1'; return true;"  />
                    <input type="button" id="btnBulletinBoadClear" class="btn btn-secondary" value="クリア">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
               </div>
               </form>
            </div>
        </div>
        </div>

    </div>
    <div class="flexmain">
    <h6 class="h6-title">進捗状況</h6>
    <div class="pagenationStyle">{{$OrderRequests->links()}}</div>
    <div class="table-responsive">
    <table class="table table-fixed table-progressFixed table-striped" ><!--table-sm table-hover table-striped table-bordered progressTable-->
        <thead>
            <th class="align-center ">@sortablelink('ItemClass','種類')</th>
            <th >&nbsp;</th>
            <th class="align-center ">@sortablelink('OrderDate','依頼/発注日')</th>
            <th >@sortablelink('item.ItemNameJp','商品名')</th>
            <th class="align-center ">@sortablelink('item.AmountUnit','容量')</th>
            <th class="align-center ">@sortablelink('item.Standard','規格')</th>
            <th class="align-center ">@sortablelink('user.UserNameJp','依頼者')</th>
            <th class="align-center ">@sortablelink('item.CatalogCode','ｶﾀﾛｸﾞｺｰﾄﾞ')</th>
            <th >@sortablelink('item.MakerNameJp','ﾒｰｶｰ')</th>
            <th class="align-center ">@sortablelink('UnitPrice','単価')</th>
            <th class="align-center ">@sortablelink('RequestNumber','数量')</th>
            <th class="align-center ">@sortablelink('RequestProgress','進捗')</th>
        </thead>
        <tbody>
    @foreach($OrderRequests as $OrderRequest)
        <tr>
            
            <td class="align-center ">
                @if($OrderRequest->ItemClass==1)
                    試薬
                @elseif($OrderRequest->ItemClass==2)
                    物品
                @endif
            </td>
            <td class="align-center ">
                @if($OrderRequest->RequestProgress==1)
                <span class="deliveryWaitIcon"></span>
                @else
                <span class="requestingIcon"></span>
                @endif
            </td>
            <td class="align-center " >
                @if($OrderRequest->OrderDate=="")
                    {{$OrderRequest->RequestDate}}
                @else
                    {{$OrderRequest->OrderDate}}
                @endif
            </td>
            <td class="tdReagentName">{{$OrderRequest->item->ItemNameJp}}</td>
            <td class="align-center " >{{$OrderRequest->item->AmountUnit}}</td>
            <td class="align-center">{{$OrderRequest->item->Standard}}</td>
            <td class="align-center">{{$OrderRequest->user->UserNameJp}}</td>
            <td class="align-center">{{$OrderRequest->item->CatalogCode}}</td>
            <td >{{$OrderRequest->item->MakerNameJp}}</td>
            <td class="align-right">{{$OrderRequest->UnitPrice}}</td>
            <td class="align-right">{{$OrderRequest->RequestNumber}}</td>
            <td class="align-center">
                @if($OrderRequest->RequestProgress==1)
                    納品待ち
                @else
                    依頼中
                @endif
            </td>
        </tr>
    @endforeach
        </tbody>
    </table>
    </div>
    </div>

</div>
</div>
@endsection
