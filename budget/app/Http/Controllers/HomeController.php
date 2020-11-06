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
        $BulletinBoards = BulletinBoard::where('LimitDate','>=', $today)->orderBy('RegistDate','desc')->orderBy('id','desc')->get();
        $arrBulletin = array();

        //設定
        $Condition = Condition::first();
        $newBulletinTerm = 0;
        if($Condition!=null){
            $newBulletinTerm = $Condition->NewBulletinTerm;
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
                'UserNameJp' => $BulletinBoard->user->UserNameJp,
                'UserNameEn' => $BulletinBoard->user->UserNameEn,
                'RegistDate' => $BulletinBoard->RegistDate,
                'LimitDate' => $BulletinBoard->LimitDate,
                'UserId' => $BulletinBoard->user->id,
                'newicon' => $isNew
            ];
            array_push($arrBulletin,$item);
        }

        $OrderRequests = OrderRequest::select([
            DB::raw('order_requests.*,
                    (CASE WHEN IFNULL(order_requests.OrderDate,"")="" 
                        THEN order_requests.RequestDate 
                        ELSE order_requests.OrderDate
                    END) AS OrderReqDate')
        ])->sortable()->orderBy('order_requests.id','asc')->paginate(25);

        return view('home',compact('arrBulletin','OrderRequests'));
    }



}
