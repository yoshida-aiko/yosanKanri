<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
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
            if ($request->has('BulletinBoardId') && $request->BulletinBoardId != ""){
                $BulletinBoard = BulletinBoard::findOrFail($request->BulletinBoardId);
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

        } catch (QueryException $e) {
            logger()->error("掲示板　QueryException");
            logger()->error($e->getMessage()); 
            $response['status'] = 'NG';        

        } catch(Exception $e){
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
            if ($request->has('BulletinBoardId') && $request->BulletinBoardId != ""){
                $BulletinBoard = BulletinBoard::findOrFail($request->BulletinBoardId);
                $BulletinBoard->delete();
            }
            else {
                $response['status'] = 'NG';
                $response['errorMsg'] = "no data";    
            }
        }
        catch(Exception $e){
            $response['status'] = 'NG';
            $response['errorMsg'] = $e->getMessage();
        }
        return Response::json($response);
 
    }



}
