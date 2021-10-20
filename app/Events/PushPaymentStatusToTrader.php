<?php

namespace App\Events;

use App\Entities\TradePaymentOrder;
use App\Entities\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PushPaymentStatusToTrader implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $paymentOrder;
    public $message;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message, User $user, TradePaymentOrder $tradePaymentOrder)
    {
        $this->message      = $message;
        $this->user         = $user;
        $this->paymentOrder = $tradePaymentOrder;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('payment.trader.' . $this->user->id);
    }

    public function broadcastAs()
    {
        return 'push_payment_status_to_trader';
    }
}
