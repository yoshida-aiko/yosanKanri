<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\BulletinBoard;
use App\OrderRequest;
use App\Condition;
use Illuminate\Support\Facades\Config;
use Auth;
use Carbon\Carbon;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //掲示板
        $today = date("Y/m/d");
        $BulletinBoards = BulletinBoard::select([
            'bulletin_boards.*',
            'users.UserNameJp AS RegistUserNameJp',
            'users.UserNameEn AS RegistUserNameEn',
        ])
        ->leftjoin('users', function($join) {
            $join->on('bulletin_boards.RegistUserId','=','users.id');
        })->where('LimitDate','>=', $today)->orderBy('RegistDate','desc')->orderBy('bulletin_boards.id','desc')->get();
        $arrBulletin = array();

        //設定
        $Condition = Condition::first();
        $newBulletinTerm = 0;
        $BulletinTerm = 0;
        if($Condition!=null){
            $newBulletinTerm = $Condition->NewBulletinTerm;
            $BulletinTerm = $Condition->BulletinTerm;
        }
        foreach($BulletinBoards as $BulletinBoard){
            $isNew = false;
            $today = new Carbon();
            $regdate = new Carbon(str_replace('/','-',$BulletinBoard->RegistDate));
            $newdate = $regdate->addDay($newBulletinTerm);
            if ($today <= $newdate){
                $isNew = true;
            }
           
            $item = [
                'id' => $BulletinBoard->id,
                'Title' => $BulletinBoard->Title,
                'Contents' => $BulletinBoard->Contents,
                'UserNameJp' => $BulletinBoard->RegistUserNameJp,
                'UserNameEn' => $BulletinBoard->RegistUserNameEn,
                'RegistDate' => $BulletinBoard->RegistDate,
                'LimitDate' => $BulletinBoard->LimitDate,
                'UserId' => $BulletinBoard->RegistUserId,
                'newicon' => $isNew
            ];
            array_push($arrBulletin,$item);
        }

        $OrderRequests = OrderRequest::select([
            DB::raw('order_requests.*,
                    requser.UserNameJp AS RequestUserNameJp,
                    requser.UserNameEn AS RequestUserNameEn,
                    (CASE WHEN IFNULL(order_requests.OrderDate,"")="" 
                        THEN order_requests.RequestDate 
                        ELSE order_requests.OrderDate
                    END) AS OrderReqDate')
        ])
        ->leftjoin('users as requser', function($join) {
            $join->on('order_requests.RequestUserId','=','requser.id');
        })->sortable()->orderBy('order_requests.id','asc')->paginate(25);

        return view('home',compact('arrBulletin','OrderRequests','BulletinTerm'));
    }



}
