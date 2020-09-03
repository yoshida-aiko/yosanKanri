<?php

use Illuminate\Database\Seeder;
use App\Cart;

class CartsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        
		$ary = [
			[1,3,-1,0,0,'','','','','','','','',0,2,-1,'','',''],
			[0,3,-1,1,1,'644-13991','和光純薬工業','Wako','ローラーボトル標準型850cm2イージーグリップキャップ、無処理、目盛あり','ROLLER BOTTLE, NT, 850CM','40個','Corning International, Inc.','',47600,1,-1,'','保存：室温',''],
			[0,3,-1,1,1,'648-06821','和光純薬工業','Wako','96ウェルハーフエリアプレート白クリアボトム, 細胞培養表面処理, フタあり, 滅菌済','CorningR 96 Half Area Well Flat Clear Bottom White Polystyrene TC-Treated Microplates, 25 per Bag, w','100枚','Corning International, Inc.','',168800,1,-1,'','保存：室温',''],
			[0,2,-1,1,1,'016-23943','和光純薬工業','Wako','抗ウサギIgG(Fc), モノクローナル抗体, ペルオキシダーゼ結合','Anti Rabbit IgG(Fc), Monoclonal Antibody, Peroxidase Conjugated','1ml','免疫化学用','',40000,1,-1,'','保存：冷蔵 (氷冷輸送)',''],
			[0,6,-1,1,1,'','和光純薬工業','Wako','TESTスポット商品','TESTスポット商品','','','',111,1,1,'','',''],
			[0,4,-1,1,1,'014-10592','和光純薬工業','Wako','12-アミノドデカン酸','12-Aminododecanoic Acid','25g','和光特級','693-57-2',2400,1,-1,'濃度・純度：99+%(Titration) 分子式：H2N(CH2)11COOH 分子量：215.33','保存：室温',''],
			[2,4,-1,1,1,'013-24891','和光純薬工業','Wako','4-アミノベンゼンスルホン酸標準品','4-Aminobenzenesulfonic Acid Standard','100mg','食品添加物試験用','121-57-3',10000,3,-1,'濃度・純度：95+%(HPLC) 分子式：C6H7NO3S 分子量：173.19','保存：冷蔵 (氷冷輸送)',''],
			[0,4,-1,1,1,'016-10591','和光純薬工業','Wako','12-アミノドデカン酸','12-Aminododecanoic Acid','100g','和光特級','693-57-2',4000,1,-1,'濃度・純度：99+%(Titration) 分子式：H2N(CH2)11COOH 分子量：215.33','保存：室温',''],
			[0,1,-1,1,8,'A0157-25G','東京化成工業','','アデノシン5-三りん酸二ナトリウム水和物','Adenosine 5-Triphosphate Disodium Salt Hydrate','25G','GR','34369-07-8',19400,1,-1,'　','',''],
			[0,1,-1,1,8,'A0149-250G','東京化成工業','','アデニン','Adenine','250G','EP','73-24-5',35500,1,-1,'','',''],
			[0,1,-1,1,8,'A0001-500G','東京化成工業','','アビエチン酸','Abietic Acid','500G','','514-10-3',30500,1,-1,'','',''],
			[0,1,-1,1,1,'018-04382','和光純薬工業','Wako','アンチモン, 粉末','Antimony, Powder','25g','','7440-36-0',1900,1,-1,'濃度・純度：95+%(Titration) 分子式：Sb 分子量：121.760','危険・有害区分：可燃性, 有害性 法規：危2-II, 安衛法57条・有害物表示対象物質, 労57-2, PRTR-1 保存：室温','']
		];

		for($i = 0; $i < 12; $i++){
	        Cart::create([
	        	'VersionNo' => $ary[$i][0],
	        	'UserId' => $ary[$i][1],
	        	'CatalogItemId' => $ary[$i][2],
	        	'ItemClass' => $ary[$i][3],
	        	'MakerId' => $ary[$i][4],
	        	'CatalogCode' => $ary[$i][5],
	        	'MakerNameJp' => $ary[$i][6],
	        	'MakerNameEn' => $ary[$i][7],
	        	'ItemNameJp' => $ary[$i][8],
	        	'ItemNameEn' => $ary[$i][9],
	        	'AmountUnit' => $ary[$i][10],
	        	'Standard' => $ary[$i][11],
	        	'CASNo' => $ary[$i][12],
	        	'UnitPrice' => $ary[$i][13],
	        	'OrderRequestNumber' => $ary[$i][14],
	        	'SupplierId' => $ary[$i][15],
	        	'Remark1' => $ary[$i][16],
	        	'Remark2' => $ary[$i][17],
	        	'OrderRemark' => $ary[$i][18]
	        ]);
	    }



        
    }
}
