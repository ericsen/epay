<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserLocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        /**
         * 如果帳號遭鎖定(禁止登入)時，阻擋。
         */
        if (Auth::check()) {
            if (Auth::user()->enable === 'off') {
                Auth::logout();

                if ($request->wantsJson()) {
                    return response()->json([
                        'error'   => true,
                        'message' => ['permission_denied' => __('auth.permission_denied')],
                    ]);
                }
                return redirect()->route('login')->withErrors(['permission_denied' => __('auth.permission_denied')])->withInput();
            }
        }

        return $next($request);
    }
}
