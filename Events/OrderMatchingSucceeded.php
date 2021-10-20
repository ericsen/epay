<?php

namespace App\Events;

use App\Entities\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Facades\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class OrderMatchingSucceeded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $trader;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($order, User $trader)
    {
        $this->order  = $order;
        $this->trader = $trader;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    // public function broadcastOn()
    // {
    //     // return new PrivateChannel('channel-name');
    //     return [];
    // }
}
