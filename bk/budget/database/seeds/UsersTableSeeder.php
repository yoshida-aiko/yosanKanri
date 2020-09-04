<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$ary = [
			[1,1,1,0,'manager','manager','ビルトインユーザー','Built-in User','','aaa@infogram.co.jp','',1,63],
			[2,17,1,0,'1138','1138','木本　朋秀','Tomohide Kimoto','','t_kimoto@infogram.co.jp','',0,63],
			[3,2,1,0,'1023','1023','松元　健吾','Kengo Matsumoto','','matsumoto.kengo@infogram.co.jp','',0,63],
			[4,15,1,0,'id002','id002','一般研究者','student1','','matsumoto.kengo@infogram.co.jp','',0,1],
			[7,2,1,0,'id001','id001','研究室管理者','','','matsumoto.kengo@infogram.co.jp','────────────────────── 管理者：matsumoto.kengo@infogram.co.jp',0,63],
			[8,0,1,0,'alluser','alluser','スーパーユーザー','','','matsumoto.kengo@infogram.co.jp','テスト',0,63],
			[9,0,1,0,'1180','1180','中道','','','nakamichi.yuki@infogram.co.jp','',0,63]
		];
		
		for($i = 0; $i < 7; $i++){
	        User::create([
	        	'VersionNo' => $ary[$i][1],
	        	'Status' => $ary[$i][2],
	        	'GroupId' => $ary[$i][3],
	        	'LoginAccount' => $ary[$i][4],
	        	'LoginPassword' => $ary[$i][5],
	        	'UserNameJp' => $ary[$i][6],
	        	'UserNameEn' => $ary[$i][7],
	        	'Tel' => $ary[$i][8],
	        	'Email' => $ary[$i][9],
	        	'Signature' => $ary[$i][10],
	        	'BuiltinUser' => $ary[$i][11],
	        	'UserAuth' => $ary[$i][2]
	        ]);
	    }
    }
}
