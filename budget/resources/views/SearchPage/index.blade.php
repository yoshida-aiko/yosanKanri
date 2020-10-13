@extends('layouts.app')

@section('content')
<script src="{{ asset('js/searchPageScript.js') }}" defer></script>
<div class="container">
<div class="wrapper">

    <div class="leftside-fixed-240">
    <h6 class="h6-title">{{ __('screenwords.refineItem') }}</h6>
        <form class="searchConditionForm" action="{{url('/SearchPage')}}" method="GET">
        @csrf
        <div class="searchFormArea">
            <input type="radio" name="searchFormTab" id="tabReagent" value="1" >
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
                    <label for="makerCheckboxRAll"><input type="checkbox" id="makerCheckboxRAll">{{ __('screenwords.all') }}</label>
                    <div class="maker-checkbox-group">
                    @foreach ($Makers as $Maker)
                        <label for="Maker{{ $loop->iteration }}R" >
                        @if (!empty($makerCheckboxR))
                            <input type="checkbox" name="makerCheckboxR[]" id="Maker{{ $loop->iteration }}R" value="{{ $Maker->id }}" {{ in_array( $Maker->id ,$makerCheckboxR) ? 'checked' : ''}} />
                        @else
                            <input type="checkbox" name="makerCheckboxR[]" id="Maker{{ $loop->iteration }}R" value="{{ $Maker->id }}"  />
                        @endif
                        {{ $Maker->MakerNameJp }}</label><br />
                    @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <input id="submitReagent" type="submit" value="{{ __('screenwords.search') }}" class="btn btn-primary">
                    <input type="button" value="{{ __('screenwords.clear') }}" id="btnClearReagent" class="btn btn-secondary" />
                </div>
            </div>
            <input type="radio" name="searchFormTab" id="tabArticle" value="2" >
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
                    <p>メーカー</p>
                    <label for="makerCheckboxAAll"><input type="checkbox" id="makerCheckboxAAll">{{ __('screenwords.all') }}</label>
                    <div class="maker-checkbox-group">
                    @foreach ($Makers as $Maker)
                        <label for="Maker{{ $loop->iteration }}A" >
                        @if (!empty($makerCheckboxA))
                            <input type="checkbox" name="makerCheckboxA[]" id="Maker{{ $loop->iteration }}A" value="{{ $Maker->id }}" {{ in_array( $Maker->id ,$makerCheckboxA) ? 'checked' : ''}} />
                        @else
                            <input type="checkbox" name="makerCheckboxA[]" id="Maker{{ $loop->iteration }}A" value="{{ $Maker->id }}" />
                        @endif
                        {{ $Maker->MakerNameJp }}</label><br />
                    @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <input id="submitArticle" type="submit" value="{{ __('screenwords.search') }}" class="btn btn-primary">
                    <input type="button" value="{{ __('screenwords.clear') }}" id="btnClearArticle" class="btn btn-secondary" />
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
    @if(!$CatalogItems->isEmpty())
        <table id="table-searchFixed" class="table table-fixed table-searchFixed table-striped">
            <thead>
                <tr>
                    <th class="align-center ">@sortablelink('ItemClass','　　')</th>
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
                        {{ __('screenwords.reagent') }}
                        @elseif($CatalogItem->ItemClass == '2')
                        {{ __('screenwords.article') }}
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

                        <input type="button" value="&#xf217;" name="btnCart" class="fa btn-cart-icon">
                        <input type="button" value="&#xf005;" name="btnFavorite" class="fa btn-favorite-icon">
                        
                        <input type="hidden" value="{{$CatalogItem->id}}" name="update_id" class="hidUpdateId">
                    
                    </td>
                    <td style="display:none;">{{$CatalogItem->maker->supplier->SupplierNameJp}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
    <p>{{ __('screenwords.noData') }}</p>
    @endif

    <div id="modal-detail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
        <!--以下modal-dialogのCSSの部分で modal-lgやmodal-smを追加するとモーダルのサイズを変更することができる-->
        <div  class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="Modal">{{ __('screenwords.itemDetails') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('screenwords.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="modal-detail-table">
                        <tbody>
                            <tr>
                                <td>{{ __('screenwords.itemName') }}：</td><td id="detailProductName" colspan="3"></td>
                            </tr>
                            <tr>
                                <td>{{ __('screenwords.capacity') }}：</td><td id="detailAmount"></td><td>{{ __('screenwords.standard') }}：</td><td id="detailStandard"></td>
                            </tr>
                            <tr>
                                <td>{{ __('screenwords.catalogCode') }}：</td><td id="detailCatalogCode"></td><td>{{ __('screenwords.unitPrice') }}：</td><td id="detailUnitPrice"></td>
                            </tr>
                            <tr>
                                <td>{{ __('screenwords.maker') }}：</td><td id="detailMakerName"></td><td>{{ __('screenwords.prioritySupplier') }}：</td><td id="detailSupplierName"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('screenwords.close') }}</button>
               </div>
            </div>
        </div>
    </div>

    
    </div>
    <div class="leftside-fixed-280">
        <h6 class="h6-title">{{ __('screenwords.orderRequestList') }}</h6>
        <div id="wrapperOrderRequestList" style="margin-bottom:20px;">
            <input type="radio" name="tabCart" id="tabCartReagent" value="1" >
            <label class="tabLabel" for="tabCartReagent">{{ __('screenwords.reagent') }}</label>
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
                                    onClick="if (!confirm({{ __('messages.confirmDelete') }})){ return false;} return true;">
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
            <label class="tabLabel" for="tabCartArticle">{{ __('screenwords.article') }}</label>
            <div class="tabContent">
                <section class="sectionOrderRequest">
                @foreach ($Carts as $Cart)
                    @if ($Cart->ItemClass==2)
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