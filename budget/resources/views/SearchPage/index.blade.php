@extends('layouts.app')

@section('content')
<script src="{{ asset('js/searchPageScript.js') }}" defer></script>

<div class="container">
<div class="wrapper">

    <div class="leftside-fixed-240">
    <h6 class="h6-title">絞込検索</h6>
        <form class="searchConditionForm" action="{{url('/SearchPage')}}" method="GET">
        @csrf
        <div class="searchFormArea">
            @if (!empty($searchFormTab))
                @if ($searchFormTab == 1)
                <input type="radio" name="searchFormTab" id="tabReagent" value="1" checked >
                @else
                <input type="radio" name="searchFormTab" id="tabReagent" value="1" >
                @endif
            @else
                <input type="radio" name="searchFormTab" id="tabReagent" value="1" checked>
            @endif
            <label class="tabLabel" for="tabReagent">試薬</label>
            <div class="tabContent">

                <div class="form-group">
                    <p>試薬名</p>
                    <input type="text" name="searchReagentNameR" value="{{ $searchReagentNameR ?? '' }}">
                    
                </div>
                <div class="form-group">
                    <p>規格</p>
                    <input type="text" name="searchStandardR" value="{{ $searchStandardR ?? '' }}">
                </div>
                <div class="form-group">
                    <p>CAS No</p>
                    <input type="text" name="searchCasNoR" value="{{ $searchCasNoR ?? '' }}">
                </div>
                <div class="form-group">
                    <p>カタログコード</p>
                    <input type="text" name="searchCatalogCodeR" value="{{ $searchCatalogCodeR ?? '' }}">
                </div>
                <div class="form-group">
                    <p>メーカー</p>
                    <label for="makerCheckboxRAll"><input type="checkbox" id="makerCheckboxRAll">すべて</label>
                    <div class="maker-checkbox-group">
                    @foreach ($Makers as $Maker)
                        <label for="Maker{{ $loop->iteration }}R" >
                        @if (!empty($makerCheckboxR))
                            @if (in_array( $Maker->id ,$makerCheckboxR))
                                <input type="checkbox" name="makerCheckboxR[]" id="Maker{{ $loop->iteration }}R" value="{{ $Maker->id }}" checked />
                            @else
                                <input type="checkbox" name="makerCheckboxR[]" id="Maker{{ $loop->iteration }}R" value="{{ $Maker->id }}" />
                            @endif
                        @else
                            <input type="checkbox" name="makerCheckboxR[]" id="Maker{{ $loop->iteration }}R" value="{{ $Maker->id }}"  />
                        @endif
                        {{ $Maker->MakerNameJp }}</label><br />
                    @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <input id="submitReagent" type="submit" value="検索" class="btn btn-primary">
                    <input type="button" value="クリア" id="btnClearReagent" class="btn btn-secondary" />
                </div>
            </div>
            @if (!empty($searchFormTab))
                @if ($searchFormTab == 2)
                <input type="radio" name="searchFormTab" id="tabArticle" value="2" checked >
                @else
                <input type="radio" name="searchFormTab" id="tabArticle" value="2" >
                @endif
            @else
                <input type="radio" name="searchFormTab" id="tabArticle" value="2" >
            @endif
            <label class="tabLabel" for="tabArticle">物品</label>
            <div class="tabContent">
                <div class="form-group">
                    <p>試薬名</p>
                    <input type="text" name="searchReagentNameA" value="{{ $searchReagentNameA ?? '' }}">
                    
                </div>
                <div class="form-group">
                    <p>規格</p>
                    <input type="text" name="searchStandardA" value="{{ $searchStandardA ?? '' }}">
                </div>
                <div class="form-group">
                    <p>カタログコード</p>
                    <input type="text" name="searchCatalogCodeA" value="{{ $searchCatalogCodeA ?? '' }}">
                </div>
                <div class="form-group">
                    <p>メーカー</p>
                    <label for="makerCheckboxAAll"><input type="checkbox" id="makerCheckboxAAll">すべて</label>
                    <div class="maker-checkbox-group">
                    @foreach ($Makers as $Maker)
                        <label for="Maker{{ $loop->iteration }}A" >
                        @if (!empty($makerCheckboxA))
                            @if (in_array( $Maker->id ,$makerCheckboxA, true))
                                <input type="checkbox" name="makerCheckboxA[]" id="Maker{{ $loop->iteration }}A" value="{{ $Maker->id }}" checked />
                            @else
                                <input type="checkbox" name="makerCheckboxA[]" id="Maker{{ $loop->iteration }}A" value="{{ $Maker->id }}" />
                            @endif
                        @else
                            <input type="checkbox" name="makerCheckboxA[]" id="Maker{{ $loop->iteration }}A" value="{{ $Maker->id }}" />
                        @endif
                        {{ $Maker->MakerNameJp }}</label><br />
                    @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <input id="submitArticle" type="submit" value="検索" class="btn btn-primary">
                    <input type="button" value="クリア" id="btnClearArticle" class="btn btn-secondary" />
                </div>
            </div>
        </div>
        <input type="hidden" name="submit_key" id="submit_key" value="" >
        <input type="hidden" name="submit_chkSharedKey" id="submit_chkSharedKey" value="" >
        </form>
    </div>
    <div class="toggle-fixed-37">
        <div id="toggle-button-searchpage"></div>
    </div>
    <div class="flexmain" >
    <h6 class="h6-title">検索結果</h6>
    <div class="pagenationStyle">
    @if(!$CatalogItems->isEmpty())
        {{$CatalogItems->appends(request()->query())->links()}}    
    @endif
    </div>
    @if(!$CatalogItems->isEmpty())
        <table id="table-searchFixed" class="table table-fixed table-searchFixed table-striped">
            <thead>
                <tr>
                    <th class="align-center ">@sortablelink('ItemClass','種類')</th>
                    <th class="align-center ">@sortablelink('AmountUnit','容量')</th>
                    <th>@sortablelink('ItemNameJp','商品名')</th>
                    <th class="align-center ">@sortablelink('Standard','規格')</th>
                    <th class="align-center ">@sortablelink('CatalogCode','カタログコード')</th>
                    <th>@sortablelink('maker.MakerNameJp','メーカー名')</th>
                    <th class="align-center ">@sortablelink('UnitPrice','単価')</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($CatalogItems as $CatalogItem)
                <tr class="table-searchFixed-tr">
                    <td class="align-center">
                        @if($CatalogItem->ItemClass == '1')
                            試薬
                        @elseif($CatalogItem->ItemClass == '2')
                            物品
                        @endif
                    </td>
                    <td class="align-center">{{ $CatalogItem->AmountUnit }}</td>
                    <td>{{ $CatalogItem->ItemNameJp }}</td>
                    <td class="align-center">{{ $CatalogItem->Standard }}</td>
                    <td class="align-center">{{ $CatalogItem->CatalogCode }}</td>
                    <td>{{ $CatalogItem->maker->MakerNameJp }}</td>
                    <td class="align-right">
                    <?php
                        if ($CatalogItem->UnitPrice > 0) {
                            $CatalogItem->UnitPrice = number_format($CatalogItem->UnitPrice);
                        }
                        else{
                            $CatalogItem->UnitPrice = '';
                        }
                    ?>
                        {{ $CatalogItem->UnitPrice }}
                    </td>
                    <td>
                        <form style="display:inline-block;" action="{{ route('SearchPage.update', $CatalogItem->id) }}" method="POST" >
                            @csrf
                            @method('PUT')
                            <input type="submit" value="&#xf217;" name="btnCart" class="fa btn-cart-icon">
                            <input type="submit" value="&#xf005;" name="btnFavorite" class="fa btn-favorite-icon">
                            <input type="hidden" value="" name="cartFavorite_submit_key">
                        </form>
                    </td>
                    <td style="display:none;">{{$CatalogItem->maker->supllier->SupplierNameJp}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
    <p>データがありません</p>
    @endif

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
                                <td>商品名：</td><td id="detailProductName"></td><td></td><td></td>
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
    <div class="leftside-fixed-280">
        <h6 class="h6-title">発注依頼リスト</h6>
        <div id="wrapperOrderRequestList" style="margin-bottom:20px;">
            <input type="radio" name="tabCart" id="tabCartReagent" value="1" >
            <label class="tabLabel" for="tabCartReagent">試薬</label>
            <div class="tabContent">
                <section id="sectionOrderRequest" class="sectionOrderRequest">
                @foreach ($Carts as $Cart)
                    @if ($Cart->ItemClass==1)
                    <article>
                        <div>
                            <form action="{{ route('SearchPage.destroy', $Cart->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="submit" value="&#xf1f8;" name="btnCartDelete" class="fa btn-cart-favorite-delete-icon"
                                    onClick="if (!confirm('削除しますか？')){ return false;} return true;">
                                <input type="hidden" name="deleteType" value="delCartReagent" >
                            </form>
                        </div>
                        <div>
                            <h6>{{$Cart->ItemNameJp}}</h6>                   
                            <div class="standardetc"><span>{{$Cart->Standard}}</span><span>{{$Cart->CatalogCode}}</span><span>{{$Cart->AmountUnit}}</span></div>
                        </div>
                        <div>
                            <input type="number" value="{{$Cart->OrderRequestNumber}}" min="1" class="numCartOrderRequestNumber">
                            <input type="hidden" name="CartId" value="{{$Cart->id}}" >             
                        </div>
                    </article>
                    @endif
                @endforeach

                </section>
            </div>

            <input type="radio" name="tabCart" id="tabCartArticle" value="2" >
            <label class="tabLabel" for="tabCartArticle">物品</label>
            <div class="tabContent">
                <section class="sectionOrderRequest">
                @foreach ($Carts as $Cart)
                    @if ($Cart->ItemClass==2)
                    <article>
                        <form action="{{ route('SearchPage.destroy', $Cart->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="submit" value="&#xf1f8;" name="btnCartDelete" class="fa btn-cart-favorite-delete-icon"
                                    onClick="if (!confirm('削除しますか？')){ return false;} return true;">
                            <input type="hidden" name="deleteType" value="delCartArticle" >
                        </form>
                        <div>
                            <h6>{{$Cart->ItemNameJp}}</h6>                   
                            <div><span>{{$Cart->Standard}}</span><span>{{$Cart->CatalogCode}}</span><span>{{$Cart->AmountUnit}}</span></div>
                        </div>
                        <div>
                            <input type="number" value="{{$Cart->OrderRequestNumber}}" min="1" class="numCartOrderRequestNumber">
                            <input type="hidden" name="CartId" value="{{$Cart->id}}" >             
                        </div>
                    </article>
                    @endif
                @endforeach
                </section>
            </div>
        </div>
        @component('components.favorite')
            @slot('url','SearchPage.update')
            @slot('jsonFavoriteTreeReagent',$jsonFavoriteTreeReagent)
            @slot('jsonFavoriteTreeArticle',$jsonFavoriteTreeArticle)
            @slot('itemClass',$searchFormTab)
        @endcomponent






    </div>
</div>
</div>

@endsection