<?php

use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('App.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

// 推播 接單Order 至交易員私有頻道
Broadcast::channel('order.trader.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

//推播系統訊息至所有人
Broadcast::channel('system.news', function ($user) {
    return true;
});


//推播交易員回報(申訴)至後端
Broadcast::channel('order.trader.feedback', function ($user) {
    return true;
});


//推播交易員申請驗證至後端
Broadcast::channel('verify_request.trader', function ($user) {
    return true;
});

//推播交易員儲值狀態至交易員私有頻道
Broadcast::channel('payment.trader.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

//推播交易員撮合過期通知
Broadcast::channel('order.trader.expired.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
