<?php

namespace App\Http\Middleware;

use App\Entities\Permission;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class CheckPermission
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
         * 代碼轉換，在設計路由的時候，常常會需要兩條路由，
         * 例如：更新資料會取名成 edit(view) or update(request)
         * 所以設計權限的時候，要將兩者都列入控管，所以 將 edit 轉換為 update
         * 而設計權限時，僅需保持 C(create)、R(read)、U(update)、D(delete) 為原則
         */
        $permissionNameTransformers = [
            'show'    => 'read',
            'store'   => 'create',
            'edit'    => 'update',
            'destroy' => 'delete',
        ];

        /**
         * 檢查使用者權限
         */
        if (Auth::check()) {

            $routeNameAry      = collect(explode('.', Route::currentRouteName()));
            $transferRouteName = $routeNameAry->map(function ($item, $key) use ($permissionNameTransformers) {
                if (!empty($permissionNameTransformers[$item])) {
                    return $permissionNameTransformers[$item];
                }
                return $item;
            })->implode('.');

            //將transferRouteName寫入Session，供樣版、語系檔使用
            Session::put('transferRouteName', $transferRouteName);

            $permissions       = Permission::pluck('display_name', 'name');
            $permissionIsSet   = ($permissions->has($transferRouteName)) ? 'true' : 'false';
            $UserHasPermission = (Auth::user()->can($transferRouteName)) ? 'true' : 'false';

            $trace = [
                'CurrentRouteName'  => Route::currentRouteName(),
                'TransferRouteName' => $transferRouteName,
                'Permissions'       => $permissions,
                'PermissionIsSet'   => $permissionIsSet,
                'UserHasPermission' => $UserHasPermission,
            ];

            debug($trace);

            if (!empty($transferRouteName) && $permissions->has($transferRouteName)) {
                if (!Auth::user()->can($transferRouteName)) {

                    if ($request->wantsJson()) {
                        return response()->json([
                            'error'   => true,
                            'message' => ['permission_denied' => __('auth.permission_denied')],
                        ]);
                    }
                    return redirect()->back()->withErrors(['permission_denied' => __('auth.permission_denied')])->withInput();
                }
            }
        }

        return $next($request);
    }
}
