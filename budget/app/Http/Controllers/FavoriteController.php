<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Library\BaseClass;
use App\Favorite;
use Response;

class FavoriteController extends Controller
{
    //

    public function createFavoriteFolder(Request $request){
        $response = array();
        $response['status'] = 'OK';
        try{
            $ItemClass = $request->ItemClass;
            $FolderName = $request->FolderName;
            BaseClass::folderAdd($ItemClass,$FolderName);
        }
        catch(Exception $e){
            $response['status'] = $e->getMessage();
        }
        return Response::json($response);
    }

    public function createFavoriteFolder2(Request $request){
        $response = array();
        $response['status'] = 'OK';
        try{
            $ItemClass = $request->ItemClass;
            $FolderName = $request->FolderName;
            BaseClass::folderAdd($ItemClass,$FolderName);
        }
        catch(Exception $e){
            $response['status'] = $e->getMessage();
        }
        return Response::json($response);
    }

    /*移動したお気に入りを更新する*/
    public function updateFavorite(Request $request) {

        $response = array();
        $response['status'] = 'OK';
        //$response['message'] = $request->update_id.'  '.$request->parent_id;
        try{
            $isUpdate = true;
            $id = $request->delete_id;
            if ($request->delete_id > 0) {
                $isUpdate = false;
            }
            if ($isUpdate){
                $id = $request->update_id;
            }
            $Favorite = Favorite::findOrFail($id);
            if ($isUpdate){
                $Favorite->ParentId = $request->parent_id;
                $Favorite->save();
            }
            else {
                $Favorite->delete();
            }
        }
        catch(Exception $e){
            $response['status'] = $e->getMessage();
        }
        return Response::json($response);
    }
}
