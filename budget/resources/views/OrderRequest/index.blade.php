@extends('layouts.app')

@section('content')
<script src="{{ asset('js/orderRequestScript.js') }}" defer></script>

<div class="container">
<div class="wrapper">

    <div class="leftside-fixed-240">
        @component('components.favorite')
            @slot('url','OrderRequest.update')
            @slot('jsonFavoriteTreeReagent',$jsonFavoriteTreeReagent)
            @slot('jsonFavoriteTreeArticle',$jsonFavoriteTreeArticle)
            @slot('itemClass',1)
        @endcomponent
    </div>
    <div class="toggle-fixed-37">
        <div id="toggle-button-favorite"></div>
    </div>
    <div class="flexmain" >
        <h6 class="h6-title">発注依頼リスト</h6>
        @if(!$Carts->isEmpty())
        <div class="divOrderRequestHeaderButton">
            <input type="button" id="btnNewProduct" value="新規商品入力" title="カタログにない商品を登録します" class="btn btn-secondary" >
            <label for="selTooRequest">依頼先選択：</label>
                <select id="selOrderRequestUser">
                    <option value="-1">すべて</option>
                    @foreach ($Users as $User)
                        <option value="{{ $User->id}}">{{ $User->UserNameJp }}</option>
                    @endforeach
                </select>
            <input type="button" id="btnOrderRequest" value="発注依頼" title="発注を依頼します" class="btn btn-primary" >
        </div>
        <div class="pagenationStyle">
        @if(!$Carts->isEmpty())
            {{$Carts->appends(request()->query())->links()}}    
        @endif
        </div>
        
            <table id="table-orderRequestFixed" class="table table-fixed table-orderRequestFixed table-striped">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th><input type="checkbox" name="chkTargetAll" checked ></th>
                        <th class="align-center ">@sortablelink('ItemClass','種類')</th>
                        <th>@sortablelink('ItemNameJp','商品名')</th>
                        <th class="align-center ">@sortablelink('AmountUnit','容量')</th>
                        <th class="align-center ">@sortablelink('CatalogCode','カタログコード')</th>
                        <th>@sortablelink('MakerNameJp','メーカー名')</th>
                        <th class="align-center ">@sortablelink('UnitPrice','単価')</th>
                        <th class="align-center ">@sortablelink('RequestNumber','数量')</th>
                        <th class="align-center ">金額</th>
                        <th>備考</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($Carts as $Cart)
                    <tr class="table-orderRequestFixed-tr">
                        <td>
                            <form action="{{ route('OrderRequest.destroy', $Cart->id) }}" method='post'>
                                @csrf
                                @method('DELETE')
                                <input type="submit" value="&#xf1f8;" 
                                    onClick="if (!confirm('削除しますか？')){ return false;} return true;" class="fa btn-delete-icon">
                                <input type="hidden" name="cartId" value="{{$Cart->id}}">
                            </form>
                        </td>
                        <td><input type="checkbox" name="chkTarget[]" checked ></td>
                        <td class="align-center">
                            @if($Cart->ItemClass == '1')
                                試薬
                            @elseif($Cart->ItemClass == '2')
                                物品
                            @endif
                        </td>
                        <td>{{ $Cart->ItemNameJp }}</td>
                        <td class="align-center">{{ $Cart->AmountUnit }}</td>
                        <td class="align-center">{{ $Cart->CatalogCode }}</td>
                        <td>{{ $Cart->MakerNameJp }}</td>
                        <td class="align-right tdOrderInputNumber">
                            <?php
                            if ($Cart->UnitPrice > 0) {
                                $UnitPrice = number_format($Cart->UnitPrice);
                            }
                            else{
                                $UnitPrice = '';
                            }
                            ?>
                            <span class="spnOrderInputNumber">{{ $UnitPrice }}</span>
                            <input type="text" class="inpOrderInputNumber inpOrderUnitPrice" pattern="[0-9]*" title="数字のみ" value="{{ $Cart->UnitPrice }}" >
                        </td>
                        <td class="align-right tdOrderInputNumber">
                            <span class="spnOrderInputNumber">{{ $Cart->OrderRequestNumber }}</span>
                            <input type="number" class="inpOrderInputNumber inpOrderRequestNumber" pattern="[0-9]*"  title="数字のみ" min="1" value="{{ $Cart->OrderRequestNumber }}" >
                        </td>
                        <td class="align-right tdOrderTotalFee">
                            <?php
                                $TotalFee = number_format($Cart->UnitPrice * $Cart->OrderRequestNumber);
                            ?>
                            {{$TotalFee}}
                        </td>
                        <td class="tdOrderRemark">
                            <p class="pOrderRemark" title="{{ $Cart->OrderRemark }}">{{ $Cart->OrderRemark }}</p>
                            <input type="text" class="inpOrderRemark" value="{{ $Cart->OrderRemark }}" >
                        </td>
                        <td style="display:none;">
                            {{$Cart->Standard}}
                        </td>
                        <td style="display:none;">
                            {{$Cart->SupplierName}}
                        </td>
                        <td style="display:none;">
                            {{$Cart->id}}
                        </td>
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
                                    <td>商品名：</td><td id="detailProductName" colspan="3"></td>
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

    <!---->
    <div id="modal-newProduct" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
        <!--以下modal-dialogのCSSの部分で modal-lgやmodal-smを追加するとモーダルのサイズを変更することができる-->
        <div  class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="Modal">新規商品入力</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{action('OrderRequestController@newProductStore')}}">
                <div class="modal-body">
                    <section class="sectionNewProduct">
                        @csrf
                        <div class="form-group" >
                            <div class="divNewProductItemClass">
                            <label for="ItemClass_Reagent">
                                <input type="radio" id="ItemClass_Reagent" name="newItemClass" value="1" required="required">試薬</label>
                            <label for="ItemClass_Article">
                                <input type="radio" id="ItemClass_Article" name="newItemClass" value="2">物品</label>
                            </div>
                        </div>
                        <div class="form-group" >
                            <label for="newProductName" class="required">商品名</label>
                            <input type="text" id="newProductName" name="newProductName" value="{{old('newProductName')}}" required="required" size="50">
                        </div>
                        <div class="form-group" >
                            <label for="newStandard">規格</label>
                            <input type="text" id="newStandard" name="newStandard" value="{{old('newStandard')}}" size="50">
                        </div>
                        <div class="form-group" >
                            <label for="newAmountUnit">容量単位</label>
                            <input type="text" id="newAmountUnit" name="newAmountUnit" value="{{old('newAmountUnit')}}" size="50">
                        </div>
                        <div class="form-group" >
                            <label for="newCatalogCode">カタログコード</label>
                            <input type="text" id="newCatalogCode" name="newCatalogCode" value="{{old('newCatalogCode')}}" size="50">
                        </div>
                        <div class="form-group" >
                            <label for="newMaker" class="required">メーカー</label>
                            <select id="newMaker" name="newMaker" required>
                                @foreach($Makers as $Maker)
                                <option value="{{ $Maker->id }}">{{$Maker->MakerNameJp}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" >
                            <label for="newUnitPrice" class="required">単価</label>
                            <input type="text" id="newUnitPrice" name="newUnitPrice" value="{{old('newUnitPrice')}}" required="required" style="width:100px;">&emsp;円
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
                    <input type="submit" id="submit_newProduct_save" name="submit_newProduct" class="btn btn-primary" value="保存" />
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
                </div>
                </form>
            </div>
        </div>
    </div>


</div>
</div>

@endsection