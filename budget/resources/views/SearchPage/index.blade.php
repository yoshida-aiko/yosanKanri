@extends('layouts.app')

@section('content')
<script src="{{ asset('js/favoriteScript.js') }}" defer></script>
<script src="{{ asset('js/searchPageScript.js') }}" defer></script>
<div class="container">
<div class="loading"><div class="loadingwrapper"><div class="ball-grid-pulse"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><div class="loadingtext">loading...</div></div></div>
<div class="wrapper">

    <div class="leftside-fixed-240">
    <h6 class="h6-title">{{ __('screenwords.refineItem') }}</h6>
        <form class="searchConditionForm" action="{{url('/SearchPage')}}" method="GET">
        @csrf
        <div class="searchFormArea">
            <input type="radio" name="searchFormTab" id="tabReagent" value="{{config('const.ItemClass.reagent')}}" >
            <label class="tabLabel" for="tabReagent">{{ __('screenwords.reagent') }}</label>
            <div class="tabContent">

                <div class="form-group">
                    <p>{{ __('screenwords.reagentName') }}</p>
                    <input type="text" name="searchReagentNameR" value="{{ $searchReagentNameR ?? '' }}">
                    
                </div>
                <div class="form-group">
                    <p>{{ __('screenwords.standard') }}</p>
                    <input type="text" name="searchStandardR" value="{{ $searchStandardR ?? '' }}">
                </div>
                <div class="form-group">
                    <p>{{ __('screenwords.casNo') }}</p>
                    <input type="text" name="searchCasNoR" value="{{ $searchCasNoR ?? '' }}">
                </div>
                <div class="form-group">
                    <p>{{ __('screenwords.catalogCode') }}</p>
                    <input type="text" name="searchCatalogCodeR" value="{{ $searchCatalogCodeR ?? '' }}">
                </div>
                <div class="form-group">
                    <p>{{ __('screenwords.maker') }}</p>
                    <label for="makerCheckboxRAll"><input type="checkbox" id="makerCheckboxRAll" style="margin-right:3px;">{{ __('screenwords.all') }}</label>
                    <div class="maker-checkbox-group">
                    @foreach ($Makers as $Maker)
                        <label for="Maker{{ $loop->iteration }}R" >
                        @if (!empty($makerCheckboxR))
                            <input type="checkbox" name="makerCheckboxR[]" id="Maker{{ $loop->iteration }}R" value="{{ $Maker->id }}" {{ in_array( $Maker->id ,$makerCheckboxR) ? 'checked' : ''}} />
                        @else
                            <input type="checkbox" name="makerCheckboxR[]" id="Maker{{ $loop->iteration }}R" value="{{ $Maker->id }}"  />
                        @endif
                        @if(App::getLocale()=='en'&&$Maker->MakerNameEn!=null) {{ $Maker->MakerNameEn }}
                        @else {{ $Maker->MakerNameJp }}
                        @endif
                        </label><br />
                    @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <input id="submitReagent" type="submit" value="{{ __('screenwords.search') }}" class="btn btn-width70 btn-primary">
                    <input type="button" value="{{ __('screenwords.clear') }}" id="btnClearReagent" class="btn btn-width70 btn-secondary" />
                </div>
            </div>
            <input type="radio" name="searchFormTab" id="tabArticle" value="{{config('const.ItemClass.article')}}" >
            <label class="tabLabel" for="tabArticle">{{ __('screenwords.article') }}</label>
            <div class="tabContent">
                <div class="form-group">
                    <p>{{ __('screenwords.reagentName') }}</p>
                    <input type="text" name="searchReagentNameA" value="{{ $searchReagentNameA ?? '' }}">
                    
                </div>
                <div class="form-group">
                    <p>{{ __('screenwords.standard') }}</p>
                    <input type="text" name="searchStandardA" value="{{ $searchStandardA ?? '' }}">
                </div>
                <div class="form-group">
                    <p>{{ __('screenwords.catalogCode') }}</p>
                    <input type="text" name="searchCatalogCodeA" value="{{ $searchCatalogCodeA ?? '' }}">
                </div>
                <div class="form-group">
                    <p>{{ __('screenwords.maker') }}</p>
                    <label for="makerCheckboxAAll"><input type="checkbox" id="makerCheckboxAAll" style="margin-right:3px;">{{ __('screenwords.all') }}</label>
                    <div class="maker-checkbox-group">
                    @foreach ($Makers as $Maker)
                        <label for="Maker{{ $loop->iteration }}A" >
                        @if (!empty($makerCheckboxA))
                            <input type="checkbox" name="makerCheckboxA[]" id="Maker{{ $loop->iteration }}A" value="{{ $Maker->id }}" {{ in_array( $Maker->id ,$makerCheckboxA) ? 'checked' : ''}} />
                        @else
                            <input type="checkbox" name="makerCheckboxA[]" id="Maker{{ $loop->iteration }}A" value="{{ $Maker->id }}" />
                        @endif
                        @if(App::getLocale()=='en') {{$Maker->MakerNameEn}}
                        @else {{$Maker->MakerNameJp}}
                        @endif
                        </label><br />
                    @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <input id="submitArticle" type="submit" value="{{ __('screenwords.search') }}" class="btn btn-width70 btn-primary">
                    <input type="button" value="{{ __('screenwords.clear') }}" id="btnClearArticle" class="btn btn-width70 btn-secondary" />
                </div>
            </div>
        </div>
        <!--<input type="hidden" name="submit_key" id="submit_key" value="" >
        <input type="hidden" name="submit_chkSharedKey" id="submit_chkSharedKey" value="" >-->
    </form>
    </div>
    <div class="toggle-fixed-37">
        <div id="toggle-button-searchpage"></div>
    </div>
    <div class="flexmain" >
    <h6 class="h6-title">{{ __('screenwords.searchResult') }}</h6>
    <div class="pagenationStyle">
    @if(!$CatalogItems->isEmpty())
        {{$CatalogItems->appends(request()->query())->links()}}    
    @endif
    </div>
    
    <table id="table-searchFixed" class="table table-fixed table-searchFixed table-striped">
        <thead>
            <tr>
                <th class="align-center ">@sortablelink('ItemClass',__('screenwords.type'))</th>
                <th class="align-center ">@sortablelink('AmountUnit',__('screenwords.capacity'))</th>
                <th>@sortablelink(__('screenwords.sortItemName'),__('screenwords.itemName'))</th>
                <th class="align-center ">@sortablelink('Standard',__('screenwords.standard'))</th>
                <th class="align-center ">@sortablelink('CatalogCode',__('screenwords.catalogCode'))</th>
                <th>@sortablelink(__('screenwords.sortMakerNameFromMaker'),__('screenwords.maker'))</th>
                <th class="align-center ">@sortablelink('UnitPrice',__('screenwords.unitPrice'))</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        @if(!$CatalogItems->isEmpty())
        @foreach ($CatalogItems as $CatalogItem)
            <tr class="table-searchFixed-tr">
                <td class="align-center">
                    @if($CatalogItem->ItemClass == config('const.ItemClass.reagent'))
                    {{ __('screenwords.reagent') }}
                    @elseif($CatalogItem->ItemClass == config('const.ItemClass.article'))
                    {{ __('screenwords.article') }}
                    @endif
                </td>
                <td class="align-center">{{ $CatalogItem->AmountUnit }}</td>
                <td>
                    @if(App::getLocale()=='en') {{$CatalogItem->ItemNameEn}}
                    @else {{$CatalogItem->ItemNameJp}}
                    @endif                    
                </td>
                <td class="align-center">{{ $CatalogItem->Standard }}</td>
                <td class="align-center">{{ $CatalogItem->CatalogCode }}</td>
                <td>
                    @if(App::getLocale()=='en') {{$CatalogItem->maker->MakerNameEn}}
                    @else {{$CatalogItem->maker->MakerNameJp}}
                    @endif                    
                </td>
                <td class="align-right">
                <?php
                    if ($CatalogItem->UnitPrice > 0) {
                        $CatalogItem->UnitPrice = \number_format($CatalogItem->UnitPrice);
                    }
                    else{
                        $CatalogItem->UnitPrice = '';
                    }
                ?>
                    {{ $CatalogItem->UnitPrice }}
                </td>
                <td>
                    <input type="button" value="&#xf217;" name="btnCart" class="fa btn-cart-icon" title="{{ __('screenwords.toOrderRequestList') }}">
                    <input type="button" value="&#xf005;" name="btnFavorite" class="fa btn-favorite-icon" title="{{ __('screenwords.toFavoriteList') }}">
                    <input type="hidden" value="{{$CatalogItem->id}}" name="update_id" class="hidUpdateId">
                </td>
                <td style="display:none;">{{$CatalogItem->maker->supplier->SupplierNameJp}}</td>
            </tr>
        @endforeach
        @endif
        </tbody>
    </table>

    @component('components.productDetail')
    @endcomponent
    </div>
    <div class="leftside-fixed-280">
        <h6 class="h6-title">{{ __('screenwords.orderRequestList') }}</h6>
        <div id="wrapperOrderRequestList" style="margin-bottom:20px;">
            <input type="radio" name="tabCart" id="tabCartReagent" value="{{config('const.ItemClass.reagent')}}" >
            <label class="tabLabel" for="tabCartReagent">{{ __('screenwords.reagent') }}</label>
            <div class="tabContent">
                <section id="sectionOrderRequest" class="sectionOrderRequest">
                @foreach ($Carts as $Cart)
                    @if ($Cart->ItemClass==config('const.ItemClass.reagent'))
                    <article>
                        <div>
                            <form action="{{ route('SearchPage.destroy', $Cart->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="submit" value="&#xf1f8;" name="btnCartDelete" class="fa btn-cart-favorite-delete-icon"
                                    onClick="if (!confirm({{ __('messages.confirmDelete') }})){ return false;} return true;">
                                <input type="hidden" name="deleteType" value="delCartReagent" >
                            </form>
                        </div>
                        <div>
                            <h6>
                            @if(App::getLocale()=='en') {{$Cart->ItemNameEn}}
                            @else {{$Cart->ItemNameJp}}
                            @endif
                            </h6>                   
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

            <input type="radio" name="tabCart" id="tabCartArticle" value="{{config('const.ItemClass.article')}}" >
            <label class="tabLabel" for="tabCartArticle">{{ __('screenwords.article') }}</label>
            <div class="tabContent">
                <section class="sectionOrderRequest">
                @foreach ($Carts as $Cart)
                    @if ($Cart->ItemClass==config('const.ItemClass.article'))
                    <article>
                        <form action="{{ route('SearchPage.destroy', $Cart->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="submit" value="&#xf1f8;" name="btnCartDelete" class="fa btn-cart-favorite-delete-icon"
                                    onClick="if (!confirm({{ __('messages.confirmDelete') }})){ return false;} return true;">
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
            @slot('itemClass',$searchFormTab)
        @endcomponent
    </div>
</div>
</div>

@endsection