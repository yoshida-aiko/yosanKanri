{{$arrayOrderInformation['SupplierNameJp']}}　{{$arrayOrderInformation['SupplierChargeUserJp']}}様<br/><br/>

平素は格別のお引き立てをいただき、ありがとうございます。<br/><br/>

さて、早速ですが、貴社取扱の商品について、下記の内容で発注をお願い致します。<br/><br/>

◆発注内容◆<br/>
@foreach($arrayOrderInformation['OrderRequests'] as $order)
－－－－－－－－－－－－－－－－－－－－－－－－－－－－－－－－－－－<br/>
商品名　　：{{$order['OrderItemNameJp']}}<br/>
規格　　　：{{$order['OrderStandard']}}<br/>
容量　　　：{{$order['OrderAmountUnit']}}<br/>
ｶﾀﾛｸﾞｺｰﾄﾞ ：{{$order['OrderCatalogCode']}}<br/>
メーカー　：{{$order['OrderMakerNameJp']}}<br/>
数量　　　：{{$order['OrderNumber']}}<br/>
発注依頼者：{{$order['OrderRequestUserNameJp']}}<br/>
備考　　　：{{$order['OrderRemark']}}<br/>
－－－－－－－－－－－－－－－－－－－－－－－－－－－－－－－－－－－<br/>
@endforeach

@if($arrayOrderInformation['FromUserSignature'] != null)
    <?php $arraySignature = explode("\n", $arrayOrderInformation['FromUserSignature']);?>
    @foreach($arraySignature as $item)
        {{$item}}<br/>
    @endforeach
@endif