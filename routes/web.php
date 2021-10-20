<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

/**
 *
 * Route、語系檔(lang/XXX/contents.php) 設計原則
 * 會有 middleware 轉換 route name，用來驗證權限上的控管。
 * 因在設計路由的時候，常常會需要兩條路由，
 * 例如：更新資料會取名成 edit(view) or update(request)
 * 所以設計權限的時候，要將兩者都列入控管，所以 middleware 會將 edit 轉換為 update
 * 而設計權限時，僅需保持 C(create)、R(read)、U(update)、D(delete) 為原則，
 *
 * 設計原則：
 * 以.做為項目區隔；例：系統.子系統.功能
 * 轉換內容
 * 'show'    => 'read'
 * 'store'   => 'create'
 * 'edit'    => 'update'
 * 'destroy' => 'delete'
 *
 * 將視圖(View) 或 請求(Request) 統一轉換成請求(Request)
 * 其他特別功能不轉換
 *
 */

// Auth::loginUsingId(6);

// Auth::loginUsingId(2);

// Change language
Route::get('/set_lang/{locale?}', 'ChangeLangController@changeLang');

//Redirect to login page
Route::get('/', 'Auth\LoginController@showLoginForm');

//Add logout get method route
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

//laravel native Auth
Auth::routes();

//以下系統皆需登入後才能進行操作
Route::middleware(['auth'])->group(function () {

    //歡迎頁面 home
    Route::prefix('home')->group(function () {
        Route::get('/', 'HomeController@index')->name('home.read');
        Route::get('aaa', 'HomeController@aaa')->name('home.aaa'); //測試資料
        Route::get('bbb', 'HomeController@bbb')->name('home.bbb'); //測試資料
    });

    //權限系統
    Route::prefix('permissions')->group(function () {
        Route::get('/', 'PermissionsController@index')->name('permissions.read');
        Route::patch('/{id}', 'PermissionsController@update')->name('permissions.update');
        Route::delete('/{id}', 'PermissionsController@destroy')->name('permissions.delete');
        Route::post('/store', 'PermissionsController@store')->name('permissions.create');
    });

    //角色系統
    Route::prefix('roles')->group(function () {
        Route::get('/', 'RolesController@index')->name('roles.read');
        Route::patch('/{id}', 'RolesController@update')->name('roles.update');
        Route::delete('/{id}', 'RolesController@destroy')->name('roles.delete');
        Route::post('/store', 'RolesController@store')->name('roles.create');
    });

    //角色權限系統
    Route::prefix('permissions_roles')->group(function () {
        Route::get('/', 'RolesController@roles')->name('permissions_roles.read');
        Route::get('/edit/{id}', 'RolesController@role_permissions_edit')->name('permissions_roles.edit');
        Route::patch('/{id}', 'RolesController@role_permissions_update')->name('permissions_roles.update');
        // Route::delete('/{id}', 'RolesController@role_permissions_to_empty')->name('permissions_roles.to_empty');

    });

    //帳號管理
    Route::prefix('accounts')->group(function () {
        Route::get('/', 'UsersController@index');

        Route::prefix('admins')->group(function () {
            Route::get('/', 'UsersController@index')->name('accounts.admins.read');
            Route::get('/show/{id}', 'UsersController@show')->name('accounts.admins.show');
            Route::get('/edit/{id}', 'UsersController@edit')->name('accounts.admins.edit');
            Route::patch('/{id}', 'UsersController@update')->name('accounts.admins.update');
            Route::get('/create', 'UsersController@create')->name('accounts.admins.create');
            Route::post('/create', 'UsersController@store')->name('accounts.admins.store');
            Route::delete('/{id}', 'UsersController@destroy')->name('accounts.admins.delete');
        });

        Route::prefix('traders')->group(function () {
            Route::get('/', 'UsersController@index')->name('accounts.traders.read');
            Route::get('/show/{id}', 'UsersController@show')->name('accounts.traders.show');
            Route::get('/edit/{id}', 'UsersController@edit')->name('accounts.traders.edit');
            Route::patch('/{id}', 'UsersController@update')->name('accounts.traders.update');
            Route::get('/create', 'UsersController@create')->name('accounts.traders.create');
            Route::post('/create', 'UsersController@store')->name('accounts.traders.store');
            Route::delete('/{id}', 'UsersController@destroy')->name('accounts.traders.delete');
            Route::patch('/check/{id}', 'UsersController@check')->name('accounts.traders.check');
        });

        Route::prefix('agents')->group(function () {
            Route::get('/', 'UsersController@index')->name('accounts.agents.read');
            Route::get('/show/{id}', 'UsersController@show')->name('accounts.agents.show');
            Route::get('/edit/{id}', 'UsersController@edit')->name('accounts.agents.edit');
            Route::patch('/{id}', 'UsersController@update')->name('accounts.agents.update');
            Route::get('/create', 'UsersController@create')->name('accounts.agents.create');
            Route::post('/create', 'UsersController@store')->name('accounts.agents.store');
            Route::delete('/{id}', 'UsersController@destroy')->name('accounts.agents.delete');
        });

        Route::prefix('customers')->group(function () {
            Route::get('/', 'UsersController@index')->name('accounts.customers.read');
            Route::get('/show/{id}', 'UsersController@show')->name('accounts.customers.show');
            Route::get('/edit/{id}', 'UsersController@edit')->name('accounts.customers.edit');
            Route::patch('/{id}', 'UsersController@update')->name('accounts.customers.update');
            Route::get('/create', 'UsersController@create')->name('accounts.customers.create');
            Route::post('/create', 'UsersController@store')->name('accounts.customers.store');
            Route::delete('/{id}', 'UsersController@destroy')->name('accounts.customers.delete');
        });
    });

    //交易員項目管理
    Route::prefix('traders')->group(function () {
        // Route::get('/', 'TradePaymentOrdersController@index');

        Route::prefix('payment')->group(function () {
            Route::get('/', 'TradePaymentOrdersController@index')->name('traders.payment.read');
            Route::get('/show/{id}', 'TradePaymentOrdersController@show')->name('traders.payment.show');
            Route::get('/edit/{id}', 'TradePaymentOrdersController@edit')->name('traders.payment.edit');
            Route::patch('/{id}', 'TradePaymentOrdersController@update')->name('traders.payment.update');

            //前端API 用於交易員儲值
            Route::get('/create', 'TradePaymentOrdersController@create')->name('traders.payment.create');
            Route::post('/store', 'TradePaymentOrdersController@store')->name('traders.payment.store');
        });

    });


    //撮合池管理
    Route::prefix('match_pools')->group(function () {

        //撮合池建立
        Route::prefix('manager')->group(function () {
            Route::get('/', 'MatchPoolsController@index')->name('match_pools.manager.read');
            Route::patch('/{id}', 'MatchPoolsController@update')->name('match_pools.manager.update');
            Route::delete('/{id}', 'MatchPoolsController@destroy')->name('match_pools.manager.delete');
            Route::post('/store', 'MatchPoolsController@store')->name('match_pools.manager.create');
        });
        
        //撮合池註冊
        Route::prefix('mapping')->group(function () {
            Route::get('/', 'MatchPoolsController@traderInMatchPoolShow')->name('match_pools.mapping.read');
            Route::get('/edit/{id}', 'MatchPoolsController@traderInMatchPoolEdit')->name('match_pools.mapping.edit');
            Route::patch('/{id}', 'MatchPoolsController@traderInMatchPoolUpdate')->name('match_pools.mapping.update');
        });
    });

    //銀行帳戶管理
    Route::prefix('company_banks')->group(function () {

        //銀行帳戶建立
        Route::get('/', 'CompanyBanksController@index')->name('company_banks.manager.read');
        Route::patch('/{id}', 'CompanyBanksController@update')->name('company_banks.manager.update');
        Route::delete('/{id}', 'CompanyBanksController@destroy')->name('company_banks.manager.delete');
        Route::post('/store', 'CompanyBanksController@store')->name('company_banks.manager.create');
    });
});
