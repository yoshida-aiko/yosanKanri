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
                            <input type="checkbox" name="makerCheckboxR[]" id="Maker{{ $loop->iteration }}R" value="{{ $Maker->id }}" />
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
                            @if (in_array( $Maker->id ,$makerCheckboxA))
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
                <tr>
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
                    <td class="align-right">{{ $CatalogItem->UnitPrice }}</td>
                    <td>
                        <form style="display:inline-block;" action="{{ route('SearchPage.update', $CatalogItem->id) }}" method="POST" >
                            @csrf
                            @method('PUT')
                            <input type="submit" value="&#xf217;" name="btnCart" class="fa btn-cart-icon">
                            <input type="submit" value="&#xf005;" name="btnFavorite" class="fa btn-favorite-icon">
                            <input type="hidden" value="" name="cartFavorite_submit_key">
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
    <p>データがありません</p>
    @endif
    </div>
    <div class="leftside-fixed-280">
        <h6 class="h6-title">発注依頼リスト</h6>
        <div id="wrapperOrderRequestList">
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
                            <input type="number" value="{{$Cart->OrderRequestNumber}}" class="numCartOrderRequestNumber">
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
                            <input type="number" value="{{$Cart->OrderRequestNumber}}" class="numCartOrderRequestNumber">
                        </div>             
                    </article>
                    @endif
                @endforeach
                </section>
            </div>
        </div>
        <h6 class="h6-title" style="margin-top:20px;">お気に入り</h6>
        <div id="wrapperFavoriteList">
        <input type="button" value="&#xf07b;" id="btnFolderAdd" name="btnFolderAdd" class="fa btn-folderadd-icon" title="フォルダ作成" >
        <label for="chkShared" ><input type="checkbox" id="chkShared" name="chkShared" value="1">共用</label>
        <input type="radio" name="tabFavorite" id="tabFavoriteReagent" value="1" >
            <label class="tabLabel" for="tabFavoriteReagent">試薬</label>
            <div class="tabContent">
                <section class="sectionOrderRequest">
                @foreach ($Favorites as $Favorite)
                    @if ($Favorite->ItemClass==1)
                    <article>
                        <form action="{{ route('SearchPage.destroy', $Favorite->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="submit" value="&#xf1f8;" name="btnFavoriteDelete" class="fa btn-cart-favorite-delete-icon"
                                    onClick="if (!confirm('削除しますか？')){ return false;} return true;">
                            <input type="hidden" name="deleteType" value="delFavoriteReagent" >
                        </form>
                        <div>
                            <h6>{{$Favorite->item->ItemNameJp}}</h6>                   
                        </div>
                    </article>
                    @endif
                @endforeach
                </section>
            </div>

            <input type="radio" name="tabFavorite" id="tabFavoriteArticle" value="2" >
            <label class="tabLabel" for="tabFavoriteArticle">物品</label>
            <div class="tabContent">
                <section class="sectionOrderRequest">
                @foreach ($Favorites as $Favorite)
                    @if ($Favorite->ItemClass==2)
                    <article>
                        <form action="{{ route('SearchPage.destroy', $Favorite->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="submit" value="&#xf1f8;" name="btnFavoriteDelete" class="fa btn-cart-favorite-delete-icon"
                                    onClick="if (!confirm('削除しますか？')){ return false;} return true;">
                            <input type="hidden" name="deleteType" value="delFavoriteArticle" >
                        </form>
                        <div>
                            <h6>{{$Favorite->item->ItemNameJp}}</h6>                   
                        </div>
                    </article>
                    @endif
                @endforeach
                </section>
            </div>
        </div>

        <div id="modal-folderadd" class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
        <!--以下modal-dialogのCSSの部分で modal-lgやmodal-smを追加するとモーダルのサイズを変更することができる-->
        <div  class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="Modal">フォルダの作成</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{action('SearchPageController@store')}}">   
                <div class="modal-body">
                    <section>
                        @csrf
                        <div class="form-group" style="margin-top:10px;">
                            <label for="RegistDate" style="margin-left:5px !important;">フォルダ名</label>
                            <input type="text" id="FolderName" name="FolderName" value="{{old('FolderName')}}" required="required">
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
                            <?php
                            $favoriteFolder->FolderName = old('FolderName');
                            $favoriteFolder->UseAuth = old('UserAuth');
                            ?>
                        @endif
                    </section>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_folder_save" name="submit_bulletinboad" class="btn btn-primary" value="保存" />
                    <input type="button" id="btnFolderClear" class="btn btn-secondary" value="クリア">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
               </div>
               </form>
            </div>
        </div>
        </div>


    </div>
</div>
</div>
@endsection