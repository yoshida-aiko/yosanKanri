<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">

    <style>
    	body{
            font-size:14px;
            font-family:'IPAexMincho';
            -webkit-print-color-adjust: exact;
    	}
    	.wrapper {
    		padding:35px;
    	}
    	h1 {
    		border-top:5px double #666;
    		border-bottom:5px double #666;
    		width:100%;
    		text-align:center;
            letter-spacing:10px;
            padding:3px;
    	}
        .top {
            margin-bottom:50px;
            position:relative;
            height:50px;
        }
        .tblTop tr td:first-child{
        	width:90px;
        	background-color:#d3d3d3;
        	padding:2px 3px;
        	border:1px solid #666;
        }
        .tblTop tr td:last-child{
        	text-align:right;
        	padding:2px 3px;
        	border:1px solid #666;
        }
        #divSupplier {
        	width:100%;
        	position:relative;
        	border-bottom:1px solid #000;
        }
        #spnSupplier {
        	font-size:18px;
        }
        #spnSupplier2 {
        	display:inline-block;
        	position:absolute;
        	right:0;
        	bottom:1px;
        }
        .tblProduct {
            width:100%;
        }
        .page{
            page-break-after: always;
            page-break-inside: avoid;
        }
        .page:last-child{
            page-break-after: auto;
        }
        .tblProduct > thead > tr > th{
        	background-color:#d3d3d3;
        	border:1px solid #666;
        }
        .tblProduct > tbody> tr:nth-child(odd) > td:nth-child(1) {
        	width:45%;
        }
        .tblProduct > tbody> tr:nth-child(odd) > td:nth-child(2) {
        	width:15%;
        }
        .tblProduct > tbody> tr:nth-child(odd) > td:nth-child(3) {
        	width:18%;
        }
        .tblProduct > tbody> tr:nth-child(odd) > td:nth-child(4) {
        	width:5%;
        	text-align:right;
        }
        .tblProduct > tbody> tr:nth-child(odd) > td:nth-child(5) {
        	width:17%;
        }
        .tblProduct > tbody > tr:nth-child(even) > td {
        	width:100%;
			border-bottom:1px solid #666;
        }
		.tblProduct > tbody> tr > td {
			padding:3px;
        }
        .tblProduct > tbody> tr{
            page-break-inside: avoid;
        }

    </style>
</head>

<body>


<div class="wrapper">
    @foreach($arrayOrderInformation as $arrayOrderInfo)
    <div class="page">
	<h1>発注書</h1>
	<div class="top">
        <div style="width:400px;position:absolute;left:0;bottom:0;">
	        <div id="divSupplier"><span id="spnSupplier">{{$arrayOrderInfo['SupplierNameJp']}}&emsp;{{$arrayOrderInfo['SupplierChargeUserJp']}}</span><span id="spnSupplier2">御中</span></div>
	    </div>
	    <div style="width:200px;position:absolute;right:0;bottom:0;">
	        <table class="tblTop" cellpadding="0" cellspacing="0">
	            <tr>
	                <td style="">発注年月日</td><td style="text-align:right;">{{$arrayOrderInfo['OrderDate']}}</td>
	            </tr>
	            <tr>
	                <td>発注書No.</td><td>{{$arrayOrderInfo['OrderSlipNo']}}</td>
	            </tr>
	        </table>
	    </div>
	</div>
	<div class="content">
		<div>平素は格別のお引き立てをいただき、ありがとうございます。</div>
		<div>貴社取扱の商品について、下記のとおり発注をお願いいたします。</div>
		<br>
		<table class="tblProduct" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th>商品・規格・容量</th><th>カタログコード</th><th>メーカー</th><th>数量</th><th>発注依頼者</th>
				</tr>
				<tr>
					<th colspan="5">備考</th>
				</tr>
			</thead>
			<tbody>
                @foreach($arrayOrderInfo['OrderRequests'] as $order)
				<tr>
					<td>{{$order['OrderItemNameJp']}}&emsp;{{$order['OrderStandard']}}&emsp;{{$order['OrderAmountUnit']}}</td><td>{{$order['OrderCatalogCode']}}</td><td>{{$order['OrderMakerNameJp']}}</td><td>{{$order['OrderNumber']}}</td><td>{{$order['OrderRequestUserNameJp']}}</td>
				</tr>
				<tr>
					<td colspan="5">{{$order['OrderRemark']}}&nbsp;</td>
                </tr>
                @endforeach
			</tbody>
		</table>
    </div>
    </div>
    @endforeach
</div>

</body>

