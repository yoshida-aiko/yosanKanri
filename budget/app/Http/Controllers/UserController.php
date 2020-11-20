<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\User;// as Authenticatable;;
use Auth;
use App\Rules\Exists;
use App\Exceptions\ExclusiveLockException;
use App\Condition;

class UserController extends Controller
{
    public function index(Request $request)
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
        //設定マスタよりバイリンガル取得
        $Condition = Condition::first();
        $bilingual = 0;
        if ($Condition != null) {
            $bilingual = $Condition->bilingual;
        }
        $request->session()->put('bilingual', $bilingual);

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
            $rules['LoginAccount'] =  ['required', 'string', 'max:50', Rule::unique('users', 'LoginAccount')->whereNull('deleted_at')->ignore($request->id)];
            $rules['UserNameJp'] = ['required', 'string', 'max:100'];
             // 設定画面のバイリンガルが使用するの場合、ユーザ(英名)必須              
            if ($request->session()->get('bilingual') == "1") {
                $rules['UserNameEn'] = ['required', 'string', 'max:100'];
            }
            $rules['Tel'] = ['nullable','string', 'max:20'];
            $rules['email'] = ['required', 'string', 'email', 'max:255',Rule::unique('users', 'email')->whereNull('deleted_at')->ignore($request->id)];
            $rules['Signature'] = ['nullable','max:1000'];
        }
        else {
            $rules['LoginAccount'] =  ['required', 'string', 'max:50', Rule::unique('users', 'LoginAccount')->whereNull('deleted_at')];
            $rules['UserNameJp'] = ['required', 'string', 'max:100'];
             // 設定画面のバイリンガルが使用するの場合、ユーザ(英名)必須              
            if ($request->session()->get('bilingual') == "1") {
                $rules['UserNameEn'] = ['required', 'string', 'max:100'];
            }
            $rules['password'] =  ['required','string','min:8','regex:/^[0-9a-zA-Z@$!%*#?&]+$/'];
            $rules['Tel'] = ['nullable','string', 'max:20'];
            $rules['email'] = ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->whereNull('deleted_at')];
            $rules['Signature'] = ['nullable','max:1000'];
        }

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            if ($request->editpass == "resetLink") {
                return redirect()->back()->withErrors($validator)->withInput();
            }else{
                return redirect()->route('User.index')->withErrors($validator)->withInput();
            }               
        }
        try {
            if ($isUpdate){
                $User = User::findOrFail($request->id);
            }
            else {
                $User = new User();
                $User->password = Hash::make($request->password);
            }
            $User->LoginAccount = $request->LoginAccount;
            $User->UserNameJp = $request->UserNameJp;
            $User->UserNameEn = $request->UserNameEn;
            $User->Tel = $request->Tel;
            $User->email = $request->email;
            $User->UserAuthString = $auth;
            $User->BuiltinUser = false;
            $User->Signature = $request->Signature;
            $User->save();
    
            [$Users,$editUser] = $this->getUserData();
            $status = true;
           
            return view('User/index',compact('Users','editUser','status'));
        } catch (QueryException $e) {
            logger()->error("ユーザーマスタ保存処理　QueryException"); 
            throw $e;
        }
        
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
