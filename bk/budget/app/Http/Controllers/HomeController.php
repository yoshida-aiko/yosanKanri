<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BulletinBoard;
use App\OrderRequest;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //掲示板
        $today = date("Y/m/d");
        $BulletinBoards = BulletinBoard::where('LimitDate','>=', $today)->get();

        //進捗状況
        $OrderRequests = OrderRequest::sortable()->paginate(25);

        return view('home',compact('BulletinBoards','OrderRequests'));
    }


    public function bulletinBoadStore(Request $request) {
    
        $today = date("Y/m/d");

        if ($request->DeleteFlag != "1")
        {

            $rules = [
                'Title' => ['required', 'max:50'],
                'Contents' => ['max:500'],
                'LimitDate' => ['required']
            ];
            $validator = $this->validate($request, $rules);

            /*判定*/
            if ($request->BulletinBoadId == "") {
                $BulletinBoard = new BulletinBoard();
            }
            else{
                $BulletinBoard = BulletinBoard::findOrFail($request->BulletinBoadId);
            }
            $BulletinBoard->VersionNo =0;
            $BulletinBoard->RegistUserId = Auth::id();
            $BulletinBoard->RegistDate = str_replace("-","/",$request->RegistDate);
            $BulletinBoard->LimitDate = str_replace("-","/",$request->LimitDate);
            $BulletinBoard->Title = $request->Title;
            $BulletinBoard->Contents = $request->Contents;
            $BulletinBoard->save();

        }
        else
        {
            $BulletinBoard = BulletinBoard::findOrFail($request->BulletinBoadId);
            $BulletinBoard->delete();
        }

        $OrderRequests = OrderRequest::sortable()->paginate(25);
        $BulletinBoards = BulletinBoard::where('LimitDate','>=', $today)->get();

        //ビューの表示
        return view('home', compact('BulletinBoards','OrderRequests'));

    }

}
