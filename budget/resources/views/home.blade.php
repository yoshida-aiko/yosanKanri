@extends('layouts.app')

@section('content')
<script type="application/javascript" src="{{ asset('js/homeScript.js') }}" defer></script>

<div class="container">
<div class="loading"><div class="loadingwrapper"><div class="ball-grid-pulse"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><div class="loadingtext">loading...</div></div></div>
<div class="wrapper">

    <div class="leftside-fixed-280">
        <h6 class="h6-title">{{ __('screenwords.bulletinBoard') }}</h6>
        
        <button type="button" id="btnModalBulletinBoad" class="btn btn-primary" >{{ __('screenwords.newRegister') }}</button>
        <section class="info">
        @foreach($arrBulletin as $Bulletin)
            <article class="bulletinArticle">
                <time>{{$Bulletin['RegistDate']}}</time>
                <span>
                    @if(App::getLocale()=='en') {{$Bulletin['UserNameEn']}}
                    @else {{$Bulletin['UserNameJp']}}
                    @endif
                </span>
                @if ($Bulletin['newicon'])
                    <div class="newicon"></div>
                @endif
                <h6>{{$Bulletin['Title']}}</h6>
                <p>{{$Bulletin['Contents']}}</p>
                <input type="hidden" name="BulletinBoadIdlist" value="{{$Bulletin['id']}}" >
                <input type="hidden" name="RegistUserIdlist" value="{{$Bulletin['UserId']}}" >
                <input type="hidden" name="LimitDatelist" value="{{$Bulletin['LimitDate']}}" >
            </article>
        @endforeach
        </section>
        
        <div id="modal-bulletinboad" class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
        <!--以下modal-dialogのCSSの部分で modal-lgやmodal-smを追加するとモーダルのサイズを変更することができる-->
        <div  class="modal-dialog modal-sm100" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="Modal">{{ __('screenwords.bulletinBoard') }}</h5>
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
                            <label for="RegistDate">{{ __('screenwords.registerDate') }}</label>
                            <input type="text" id="RegistDate" name="RegistDate" value="{{old('RegistDate')}}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="Title" class="required">{{ __('screenwords.title') }}</label>
                            <input type="text" id="Title" name="Title" value="{{old('Title')}}" required="required" size="50">
                        </div>
                        <div class="form-group">
                            <label for="Contents" class="required">{{ __('screenwords.content') }}</label>
                            <textarea id="Contents" name="Contents" value="{{old('Contents')}}" rows="10" required="required" size="500"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="LimitDate" class="required">{{ __('screenwords.displayLimit') }}</label>
                            <input type="text" id="LimitDate" name="LimitDate" value="{{old('LimitDate')}}"  required="required">
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
                    <input type="submit" id="submit_bulletinboad_save" name="submit_bulletinboad" class="btn btn-width70 btn-primary" value="{{ __('screenwords.save') }}" />
                    <input type="submit" id="submit_bulletinboad_delete" name="submit_bulletinboad" class="btn btn-width70 btn-primary" value="{{ __('screenwords.delete') }}" 
                            onClick="if (!confirm('{{ __('messages.confirmDelete') }}')){ return false;} document.getElementById('DeleteFlag').value = '1'; return true;"  />
                    <input type="button" id="btnBulletinBoadClear" class="btn btn-secondary" value="{{ __('screenwords.clear') }}">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('screenwords.close') }}</button>
               </div>
               </form>
            </div>
        </div>
        </div>

    </div>
    <div class="flexmain">
    <h6 class="h6-title">{{ __('screenwords.progressReport') }}</h6>
    <div class="pagenationStyle">{{$OrderRequests->links()}}</div>
    <div class="table-responsive">
    <table class="table table-fixed table-progressFixed table-striped" ><!--table-sm table-hover table-striped table-bordered progressTable-->
        <thead>
            <?php $type=__('screenwords.type') ?>
            <th class="align-center ">@sortablelink('ItemClass',__('screenwords.type'))</th>
            <th >&nbsp;</th>
            <th class="align-center ">@sortablelink('OrderReqDate',__('screenwords.requestOrderDate'))</th>
            <th >@sortablelink(__('screenwords.sortItemNameFromItem'),__('screenwords.itemName'))</th>
            <th class="align-center ">@sortablelink('item.AmountUnit',__('screenwords.capacity'))</th>
            <th class="align-center ">@sortablelink('item.Standard',__('screenwords.standard'))</th>
            <th class="align-center ">@sortablelink(__('screenwords.sortUserNameFromUser'),__('screenwords.requester'))</th>
            <th class="align-center ">@sortablelink('item.CatalogCode',__('screenwords.catalogCode'))</th>
            <th >@sortablelink(__('screenwords.sortMakerNameFromItem'),__('screenwords.maker'))</th>
            <th class="align-center ">@sortablelink('UnitPrice',__('screenwords.unitPrice'))</th>
            <th class="align-center ">@sortablelink('RequestNumber',__('screenwords.quantity'))</th>
            <th class="align-center ">@sortablelink('RequestProgress',__('screenwords.progressReport'))</th>
        </thead>
        <tbody>
    @foreach($OrderRequests as $OrderRequest)
        <tr>
            
            <td class="align-center ">
                @if($OrderRequest->ItemClass==config('const.ItemClass.reagent'))
                    {{ __('screenwords.reagent') }}
                @elseif($OrderRequest->ItemClass==config('const.ItemClass.article'))
                    {{ __('screenwords.article') }}
                @endif
            </td>
            <td class="align-center ">
                @if($OrderRequest->RequestProgress==config('const.RequestProgress.ordered'))
                <span class="deliveryWaitIcon"></span>
                @else
                <span class="requestingIcon"></span>
                @endif
            </td>
            <td class="align-center " >
                
                {{$OrderRequest->OrderReqDate}}
            </td>
            <td class="tdReagentName">
                @if(App::getLocale()=='en') {{$OrderRequest->item->ItemNameEn}}
                @else {{$OrderRequest->item->ItemNameJp}}
                @endif
            </td>
            <td class="align-center " >{{$OrderRequest->item->AmountUnit}}</td>
            <td class="align-center">{{$OrderRequest->item->Standard}}</td>
            <td class="align-center">{{$OrderRequest->user->UserNameJp}}</td>
            <td class="align-center">{{$OrderRequest->item->CatalogCode}}</td>
            <td >
                @if(App::getLocale()=='en') {{$OrderRequest->item->MakerNameEn}}
                @else {{$OrderRequest->item->MakerNameJp}}
                @endif
            </td>
            <td class="align-right">{{$OrderRequest->UnitPrice}}</td>
            <td class="align-right">{{$OrderRequest->RequestNumber}}</td>
            <td class="align-center">
                @if($OrderRequest->RequestProgress==config('const.RequestProgress.ordered'))
                    {{ __('screenwords.waitingForDelivery') }}
                @else
                    {{ __('screenwords.requesting') }}
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
