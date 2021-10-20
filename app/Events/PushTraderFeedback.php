<?php

namespace App\Events;

use App\Entities\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PushTraderFeedback implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $order;
    public $user;


    /**
     * 發送交易員申請推播通知
     *
     * @param [type] $message
     * @param [type] $order
     * @param User $user
     */
    public function __construct($message, $order, User $user)
    {
        $this->message = $message;
        $this->order   = $order;
        $this->user    = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('order.trader.feedback');
    }

    public function broadcastAs()
    {
        return 'push_trader_feedback';
    }
}
