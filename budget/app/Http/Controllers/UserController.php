<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\User;// as Authenticatable;;
use Auth;
use App\Rules\Exists;
use App\Exceptions\ExclusiveLockException;

class UserController extends Controller
{
    public function index()
    {
        /*
        if (strpos(Auth::user()->UserAuthString,'Master') === false){
            $Users = User::where('id','=',Auth::id())->get();
            $editUser = $Users->first();
            $editUser->password = 'resetLink';
        }
        else{
            $Users = User::all();
            $editUser = new User();
        }*/
        
        [$Users,$editUser] = $this->getUserData();

        return view('User/index',compact('Users','editUser'));
    }

    private function getUserData(){

        /*マスタ権限のないユーザーだった場合、自分のデータのみ返す */
        if (strpos(Auth::user()->UserAuthString,'Master') === false){
            $Users = User::where('id','=',Auth::id())->get();
            $editUser = $Users->first();
            $editUser->password = 'resetLink';
        }
        else{
            $Users = User::all();
            $editUser = new User();
        }
        
        return [$Users,$editUser];
    }

    public function edit($id)
    {
        try {
            $Users = User::all();
            $exists  = User::where('id',$id)->exists();
            if (!$exists) {
                throw new ExclusiveLockException;
            }
            $editUser = User::findOrFail($id);
            $editUser->password = 'resetLink';
            return view('User/index',compact('Users','editUser'));
        } catch (ExclusiveLockException $e) {
            throw $e;
        }       
    }

    public function create()
    {
        $User = new User();

        return view('User\create',compact('User'));
    }

    public function store(Request $request)
    {

        if($request->action === 'back') {
            return redirect()->route('User.index');
        }

        $isUpdate = false;

        if ($request->id != "")
        {
            $isUpdate = true;
        }

        $auth = '';
        if (is_array($request->chkAuthor)) {
            foreach($request->chkAuthor as $chkAuth)
            {
                if ($auth <> '') {
                    $auth = $auth.',';
                }
                $auth = $auth.$chkAuth;
            }
        }
        if ($isUpdate){
            $account = $request->LoginAccount;
            $rules = [
                'LoginAccount' => ['required', 'string', 'max:50', Rule::unique('users', 'LoginAccount')->whereNull('deleted_at')->ignore($request->id)],
                'UserNameJp' => ['required', 'string', 'max:100'],
                'Tel' => ['nullable','string', 'max:20'],
                'email' => ['required', 'string', 'email', 'max:255',Rule::unique('users', 'email')->whereNull('deleted_at')->ignore($request->id)],
                'Signature' => ['nullable','max:1000']
            ];
        }
        else {
            $rules = [
                'LoginAccount' => ['required', 'string', 'max:50', Rule::unique('users', 'LoginAccount')->whereNull('deleted_at')],
                'UserNameJp' => ['required', 'string', 'max:100'],
                'password' => ['required', 'string', 'min:8'],
                'Tel' => ['nullable','string', 'max:20'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->whereNull('deleted_at')],
                'Signature' => ['nullable','max:1000']
            ];
        }
        $this->validate($request, $rules);

        if ($isUpdate){
            $User = User::findOrFail($request->id);
        }
        else {
            $User = new User();
            $User->password = Hash::make($request->password);
        }
        $User->LoginAccount = $request->LoginAccount;
        $User->UserNameJp = $request->UserNameJp;
        $User->Tel = $request->Tel;
        $User->email = $request->email;
        $User->UserAuthString = $auth;
        $User->BuiltinUser = false;
        $User->Signature = $request->Signature;
        $User->save();

        /*$Users = User::all();
        $editUser = new User();*/

        [$Users,$editUser] = $this->getUserData();

        return view('User/index',compact('Users','editUser'));
    }


    public function destroy($id)
    {
        $User = User::lockForUpdate()->withTrashed()->find($id);
        $User->delete();

        return redirect()->route('User.index');
    }

    public function getLogout(){
        Auth::logout();
        return redirect()->route('login');
    }

}
