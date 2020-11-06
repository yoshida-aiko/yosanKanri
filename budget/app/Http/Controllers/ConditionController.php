<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Request as PostRequest;
use App;
use App\Condition;


class ConditionController extends Controller
{
    // POSTで送信された場合に、処理を分岐
    public function judge(Request $request){
        if ($request->has('send')) {
            // 保存ボタン
            if($request->action === 'back') {
                return redirect()->route('Condition.index');
            }  
            list($Condition,$mode) = $this->store($request);
            $msg = App::getLocale()=='en' ? 'It registered' : '登録完了しました';
            $request->session()->flash('completeMessage', $msg);

        }else if ($request->has('delete')) {
            // クリアボタン
            list($Condition,$mode) = $this->destroy($request);
        }      
        return view('Condition/index',compact('Condition','mode'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // セッション削除
        $request->session()->forget('completeMessage');

       $Condition = Condition::first();
        if ($Condition == null) {
            $Condition = $this->initialize();
            $mode = 'new';
        }        
        else{
            /* // SMTP項目は　.envに設定するためコメント化
            $Condition->SMTPPassword = Crypt::decryptString($Condition->SMTPPassword); */
            $mode = 'edit';
        } 

        return view('Condition/index',compact('Condition','mode'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $Condition = new Condition();

        return view('Condition\create',compact('Condition'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $isUpdate = false;

        $Condition = Condition::first();
        if ($Condition != null) {
            $isUpdate = true;
        }

        if (!$isUpdate){
            $Condition = new Condition(); 
        }

        $rules = [
            'SystemNameJp' => ['required', 'string', 'max:50'],
            'FiscalStartMonth' => ['required', 'integer'],
            'BulletinTerm' => ['required', 'integer'],
            'NewBulletinTerm' => ['required', 'integer'],
            'email' => ['required', 'string', 'email', 'max:100'],
            // SMTP項目は　.envに設定するためコメント化
            /* 'SMTPServerId' => ['required', 'string', 'max:100'],
            'SMTPServerPort' => ['required', 'integer'], */
        ];
        if ($request['bilingual'] == "1") {
            $rules['SystemNameEn'] = ['required', 'string', 'max:50'];
        }
        // SMTP項目は　.envに設定するためコメント化
        /* if ($request['SMTPAuthFlag'] == "1") {
            $rules['SMTPAccount'] = ['required', 'string', 'max:100'];
            $rules['SMTPPassword'] = ['required', 'string', 'min:50'];
        } */
        $sorter = ['SystemNameJp','SystemNameEn','FiscalStartMonth','BulletinTerm','NewBulletinTerm','email'];
        $rules = array_merge(array_flip($sorter),$rules);
        $this->validate($request, $rules);

        $Condition->VersionNo = 0;
        $Condition->bilingual = $request->bilingual;
        $Condition->SystemNameJp = $request->SystemNameJp;
        if ($request->SystemNameEn == NULL) {
            $request->SystemNameEn  = '';
        }
        $Condition->SystemNameEn = $request->SystemNameEn;
        $Condition->FiscalStartMonth = $request->FiscalStartMonth;
        $Condition->NewBulletinTerm = $request->NewBulletinTerm;
        $Condition->BulletinTerm = $request->BulletinTerm;
        /* SMTP項目は　.envに設定するためコメント化
        $Condition->SMTPServerId = $request->SMTPServerId;
        $Condition->SMTPServerPort = $request->SMTPServerPort;
        $Condition->SMTPAccount = $request->SMTPAccount;
        $Condition->SMTPPassword =  Crypt::encryptString($request->SMTPPassword);
        if ($request->SMTPAuthFlag =="") {
            $Condition->SMTPAuthFlag = 0;
        }else {
            $Condition->SMTPAuthFlag = 1;
        } 
        */
        
        // $Condition->SMTPConnectMethod = 2;
        $Condition->Organization = "インフォグラム";
        $Condition->Department = "福岡本社";
        $Condition->EMail = $request->email;
        $Condition->ExecutionBasis = $request->ExecutionBasis;
       
        $Condition->save();

        $query = Condition::first();
        // SMTP項目は　.envに設定するためコメント化
        // $Condition->SMTPPassword = Crypt::decryptString($query->SMTPPassword);
        $mode = 'edit';

        return [$Condition,$mode];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $exists  = Condition::where('id',$request->id)->exists();
        if (!$exists) {
            $Condition = $this->initialize();
            $mode = 'new'; 
        }else{
            $Condition = Condition::findOrFail($request->id);
            // SMTP項目は　.envに設定するためコメント化
            // $Condition->SMTPPassword = Crypt::decryptString($Condition->SMTPPassword);
            $mode = 'edit'; 
        }
        $completeMessage = $request->session()->get('completeMessage');
        if ($completeMessage) {
            $request->session()->forget('completeMessage');
        }
        return [$Condition,$mode];
    }

    /* 初期値 */
    public function initialize(){
        $Condition = new Condition();
        $Condition->FiscalStartMonth = 4;
        $Condition->NewBulletinTerm = 5;
        $Condition->BulletinTerm = 30;
        $Condition->bilingual = 0;
        /* SMTP項目は　.envに設定するためコメント化
        $Condition->SMTPServerPort = 25;
        $Condition->SMTPAuthFlag = 0;
         */
        $Condition->ExecutionBasis = 1;
        return $Condition;
    }
}
