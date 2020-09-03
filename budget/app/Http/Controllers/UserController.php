<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
//use Illuminate\Foundation\Auth\User as Authenticatable;
use App\User;// as Authenticatable;;

class UserController extends Controller
{
    /*public const USER_AUTHORITY_NONE = 0;
    public const USER_AUTHORITY_ORDER = 1;
    public const USER_AUTHORITY_DELIVERY = 2;
    public const USER_AUTHORITY_BUDGET = 4;
    public const USER_AUTHORITY_BUYING = 8;
    public const USER_AUTHORITY_MASTER = 16;
    public const USER_AUTHORITY_PAYMANT = 32;*/

    public function index()
    {
        $Users = User::where('Status','=', 1)->get();
        $editUser = new User();

        return view('User/index',compact('Users','editUser'));
    }
    public function edit($id)
    {
        $Users = User::where('Status','=', 1)->get();
        $editUser = User::findOrFail($id);
        $editUser->password = 'resetLink';
        return view('User/index',compact('Users','editUser'));
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

        $calc = 0;
        if (is_array($request->chkAuthor)) {
            foreach($request->chkAuthor as $chkAuth)
            {
                $calc = $calc + $chkAuth;
            }
        }
        if ($isUpdate){
            $rules = [
                'LoginAccount' => ['required', 'string', 'max:50', Rule::unique('users', 'LoginAccount')->whereNull('deleted_at')->whereNot('LoginAccount', $request->LoginAccount)],
                'UserNameJp' => ['required', 'string', 'max:100'],
                'Tel' => ['nullable','string', 'max:20'],
                'email' => ['required', 'string', 'email', 'max:255',Rule::unique('users', 'email')->whereNull('deleted_at')->whereNot('email', $request->email)],
                'Signature' => ['nullable','max:1000']
            ];
        }
        else {
            $rules = [
                'LoginAccount' => ['required', 'string', 'max:50', Rule::unique('users', 'LoginAccount')->whereNull('deleted_at')->whereNot('LoginAccount', $request->LoginAccount)],
                'UserNameJp' => ['required', 'string', 'max:100'],
                'password' => ['required', 'string', 'min:8'],
                'Tel' => ['nullable','string', 'max:20'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->whereNull('deleted_at')->whereNot('email', $request->email)],
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
        //$encrypted= Crypt::encrypt($request->password);
        //$User->password = $encrypted;
        $User->UserAuth = $calc;
        $User->BuiltinUser = false;
        $User->Signature = $request->Signature;

        $User->save();

        $Users = User::where('Status','=', 1)->get();
        $editUser = new User();

        return view('User/index',compact('Users','editUser'));
    }

    public function update(Request $request,$id)
    {
        $rules = [
            'LoginAccount' => ['required', 'string', 'max:50'],
            'UserNameJp' => ['required', 'string', 'max:100'],
            'Tel' => ['string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'Signature' => ['max:1000']
        ];
        $this->validate($request, $rules);

        $calc = 0;
        if (is_array($request->chkAuthor)) {
            foreach($request->chkAuthor as $chkAuth)
            {
                $calc = $calc + $chkAuth;
            }
        }

        $User = User::findOrFail($id);
        $User->LoginAccount = $request->LoginAccount;
        $User->UserNameJp = $request->UserNameJp;
        $User->Tel = $request->Tel;
        $User->email = $request->email;
        $User->UserAuth = $calc;
        $User->Signature = $request->Signature;
        $User->save();

        $editUser = new User();

        return view('User/index',compact('Users','editUser'));
    }

    public function destroy($id)
    {
        $User = User::findOrFail($id);
        $User->delete();

        return redirect()->route('User.index');
    }

}
