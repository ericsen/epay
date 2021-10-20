<?php

namespace App\Listeners;

use App\Entities\User;
use App\Events\PushOrderToTrader;
use Illuminate\Support\Facades\Log;
use App\Events\OrderMatchingSucceeded;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTraderOrderNotification
{

    /**
     * The name of the connection the job should be sent to.
     *
     * @var string|null
     */
    // public $connection = 'redis';

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    // public $queue = 'send_order_to_trader';

    /**
     * The time (seconds) before the job should be processed.
     *
     * @var int
     */
    // public $delay = 1;

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
        Log::info('Trader is a =>');
        Log::info($event->trader);

        //推送私頻至交易員身上
        event(new PushOrderToTrader('Order form event', User::find($event->trader->id)));
        return 'Push Order to Trader Completed.';
    }

    /**
     * Handle the event.
     *
     * @param  OrderMatchingSucceeded  $event
     * @return void
     */
    public function failed(OrderMatchingSucceeded $event, $exception)
    {
        //
    }
}
