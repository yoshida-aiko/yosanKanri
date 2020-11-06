@extends('layouts.app')

@section('content')
<script type="application/javascript" src="{{ asset('js/homeScript.js') }}" defer></script>

<div class="container">
<div class="loading"><div class="loadingwrapper"><div class="ball-grid-pulse"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><div class="loadingtext">loading...</div></div></div>
<div class="wrapper">

    <div class="leftside-fixed-280">
        <h6 class="h6-title">{{ __('screenwords.bulletinBoard') }}</h6>
        
        <button type="button" id="btnModalBulletinBoard" class="btn btn-primary" >{{ __('screenwords.newRegister') }}</button>
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
                <h6 title="{{$Bulletin['Title']}}">{{$Bulletin['Title']}}</h6>
                @if ($Bulletin['UserId']==Auth::id())
                    <div class="editicon" title="編集画面を表示します"></div>
                @endif
                <p>{{$Bulletin['Contents']}}</p>
                <input type="hidden" name="BulletinBoardIdlist" value="{{$Bulletin['id']}}" >
                <input type="hidden" name="RegistUserIdlist" value="{{$Bulletin['UserId']}}" >
                <input type="hidden" name="LimitDatelist" value="{{$Bulletin['LimitDate']}}" >
            </article>
        @endforeach
        </section>
        
        <div id="modal-bulletinboard" class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
        <!--以下modal-dialogのCSSの部分で modal-lgやmodal-smを追加するとモーダルのサイズを変更することができる-->
        <div  class="modal-dialog modal-sm100" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="Modal">{{ __('screenwords.bulletinBoard') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <section class="update">
                        @csrf
                        <input type="hidden" id="BulletinBoardId" name="BulletinBoardId" >
                        <input type="hidden" id="RegistUserId" name="RegistUserId" >
                        <div class="form-group" style="margin-top:10px;">
                            <label for="RegistDate">{{ __('screenwords.registerDate') }}</label>
                            <input type="text" id="RegistDate" name="RegistDate" value="{{old('RegistDate')}}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="Title" class="required">{{ __('screenwords.title') }}</label>
                            <input type="text" id="Title" name="Title" value="{{old('Title')}}">
                        </div>
                        <div class="form-group">
                            <label for="Contents" class="required">{{ __('screenwords.content') }}</label>
                            <textarea id="Contents" name="Contents" value="{{old('Contents')}}" rows="10"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="LimitDate" class="required">{{ __('screenwords.displayLimit') }}</label>
                            <input type="text" id="LimitDate" name="LimitDate" value="{{old('LimitDate')}}">
                        </div>
                        {{-- エラーメッセージ --}}
                        <div id="divError" class="alert alert-danger" style="display:none;" >
                        <ul></ul>
                        </div>
                    </section>
                </div>
                <div class="modal-footer">
                    <input type="button" id="btnBulletinboardSave" class="btn btn-width70 btn-primary" value="{{ __('screenwords.save') }}"/>
                    <input type="button" id="btnBulletinboardDelete" class="btn btn-width70 btn-primary" value="{{ __('screenwords.delete') }}"/>
                    <input type="button" id="btnBulletinBoardClear" class="btn btn-secondary" value="{{ __('screenwords.clear') }}">
                    <button type="button" id="btnBulletinBoardClose" class="btn btn-secondary">{{ __('screenwords.close') }}</button>
               </div>
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
            <td class="align-right">\{{number_format($OrderRequest->UnitPrice)}}</td>
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
