<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Cache;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        $key = $request->session()->getId().'_cache_token';
        $input_key = $request->session()->getId().'_input_token';

        $cache_token = Cache::get($key);
        $input_token = Cache::get($input_key);

        $token = $this->getTokenFromRequest($request);

        // キャッシュに載せたリフレッシュしたトークン（POST時の初回のみ）とリフレッシュしたトークンが同じ場合は
        // 初回アクセスなのでチェックを通過させる
        if ($cache_token === $request->session()->token())
        {
            // フォーム画面からきたトークンと同じトークンか判定し、同じ場合はチェックを通過させる
            if ($token === $input_token)
            {
                // 強制的に通過させる為にsessionのトークンをinputのものと同じにする。
                $request->session()->put('_token', $token);
            }
        }

        $result_flag = is_string($request->session()->token()) &&
               is_string($token) &&
               hash_equals($request->session()->token(), $token);

        return $result_flag;
    }
}
