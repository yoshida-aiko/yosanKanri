<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Exceptions\ExclusiveLockException;
use App;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // CSRFトークンのエラーの場合
        if (get_class($exception) == 'Illuminate\Session\TokenMismatchException') {
            return redirect()->to(url()->previous());
        }
        // 排他エラー
        if($exception instanceof ExclusiveLockException) {
            $msg = '';
            if (App::getLocale()=='en') {
                $msg ='Data deleted from other users.';
            }else{
                $msg ='他のユーザーから削除されたデータです。';
            }
            return redirect()->back()->with('exclusiveError', $msg);
        } 

        // DBエラー　QueryException
        if(get_class($exception) == 'Illuminate\Database\QueryException') {
            return response(view('Error/systemError'));
        } 

        
        return parent::render($request, $exception);
    }
   
}
