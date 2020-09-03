<?php

use Illuminate\Database\Seeder;
use App\Condition;

class ConditionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		
        Condition::create([
        	'VersionNo' => 0,
        	'SystemNameJp' => '予算管理システム',
        	'SystemNameEn' => 'Order Management System',
        	'FiscalStartMonth' => 4,
        	'NewBulletinTerm' => 5,
        	'BulletinTerm' => 30,
        	'bilingual' => 0,
        	'SMTPServerId' => 'smtp.gmail.com',
        	'SMTPServerPort' => 587,
        	'SMTPAccount' => 'xxx@gmail.com',
        	'SMTPPassword' => 'xxx',
        	'SMTPAuthFlag' => 1,
        	'SMTPConnectMethod' => 2,
        	'Organization' => '甲南大学　フロンティアサイエンス学部生命科学科',
        	'Department' => 'インフォグラム',
        	'ExecutionBasis' => 2,
        	'EMail' => 'xxx@gmail.com'
	        ]);
    }
}
