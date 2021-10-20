<?php

namespace App\Listeners;

use App\Entities\User;
use Illuminate\Support\Facades\Log;
use App\Events\OrderMatchingSucceeded;
use App\Events\PushSystemNewsToEveryOne;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateOrderInformation
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderMatchingSucceeded  $event
     * @return void
     */
    public function handle(OrderMatchingSucceeded $event)
    {
        Log::info('Order is a =>');
        Log::info($event->order);

        /**
         * @todo 撰寫訂單更新邏輯
         */

        //暫時測試公頻推播
        event(new PushSystemNewsToEveryOne('News form event'));

        return 'Order updated.';
    }
}
