<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserPassed
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
         * 如果是新申請帳號在末驗證之前，阻擋。
         */
        if (Auth::check()) {
            if (empty(Auth::user()->inspector_id) || empty(Auth::user()->passed_at)) {
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
