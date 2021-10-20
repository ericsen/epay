<?php

namespace App\Events;

use App\Entities\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TraderWaitingExpiredEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $_user;
    private $_info;

    /**
     * 通知trader等候過期
     *
     * @param User  $user user data
     * @param array $info parameters for waiting job
     */
    public function __construct(User $user, array $info)
    {
        $this->_user = $user;
        $this->_info = $info;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel(
        //     config('pool_config.channels.expired') . '.' . $this->_user->id
        // );
        return new PrivateChannel('order.trader.expired.' . $this->_user->id);
    }

    /**
     * 頻道別名
     *
     * @return string
     */
    public function broadcastAs() 
    {
        // return config('pool_config.events.expired');
        return 'trader_waiting_expired';
    }

    public function broadcastWith()
    {
        return [
            'info' => $this->_info,
            'time' => date('Y-m-d H:i:s'),
            'user' => $this->_user
        ];
    }
}
