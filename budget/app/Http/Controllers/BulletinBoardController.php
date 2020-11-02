<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BulletinBoard;
use Auth;
use Response;

class BulletinBoardController extends Controller
{
    public function bulletinBoardStore(Request $request)
    {
        $response = array();
        $response['status'] = 'OK';

        try{
            if ($request->has('BulletinBoadId') && $request->BulletinBoadId != ""){
                $BulletinBoard = BulletinBoard::findOrFail($request->BulletinBoadId);
            }
            else {
                $BulletinBoard = new BulletinBoard();
            }
            $BulletinBoard->RegistUserId = Auth::id();
            $BulletinBoard->RegistDate = $request->RegistDate;
            $BulletinBoard->LimitDate = $request->LimitDate;
            $BulletinBoard->Title = $request->Title;
            $BulletinBoard->Contents = $request->Contents;
            $BulletinBoard->save();
        }
        catch(Exception $e){
            $response['status'] = 'NG';
            $response['errorMsg'] = $e->getMessage();
        }
        return Response::json($response);
 
    }


    public function bulletinBoardDestroy(Request $request)
    {
        $response = array();
        $response['status'] = 'OK';

        try{
            if ($request->has('BulletinBoadId') && $request->BulletinBoadId != ""){
                $BulletinBoard = BulletinBoard::findOrFail($request->BulletinBoadId);
            }
            $BulletinBoard->delete();
        }
        catch(Exception $e){
            $response['status'] = 'NG';
            $response['errorMsg'] = $e->getMessage();
        }
        return Response::json($response);
 
    }



}
