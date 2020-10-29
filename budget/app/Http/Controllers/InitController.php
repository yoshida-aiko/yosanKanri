<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query;
use Illuminate\Http\Request;
use Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Library\BaseClass;
use App\User;

class InitController extends Controller
{
    public function passwordHash(Request $request){

        $Users = DB::connection('mysql_kobe')->select('select * from users where id=2');

        dd($Users[0]->LoginAccount.' '.Hash::check($Users[0]->name,$Users[0]->password));


        //$User = User::findOrFail(1);;
        //dd(Hash::check('info1180',$User->password));

        /*$Users = DB::connection('mysql_kobe')->select('select * from users');
        foreach($Users as $item){
            
            $param = [
                'id' => $item->id,
                'password' => Hash::make($item->name)
            ];
            DB::connection('mysql_kobe')->update('UPDATE users set password = :password where id = :id',$param);
            
        }*/


    }
}