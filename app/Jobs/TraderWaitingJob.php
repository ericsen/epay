<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\TraderWaitingExpiredEvent;

/**
 * Class TraderWaitingJob
 * 
 * @package namespace App\Jobs
 */
class TraderWaitingJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var integer Job超時預設時間,單位為秒 */
    //public $timeout = 360;

    /** @var string 撮合池名稱 */
    private $_pool;
    /** @var \App\Entities\User trader的使用者資料 */
    private $_user;
    /** @var integer 儲值餘額 */
    private $_amount;

    /**
     * TraderWaitingJob constructor
     *
     * @param integer           $pool   撮合池名稱
     * @param App\Entities\User $user   trader的使用者資料
     * @param integer           $amount 儲值餘額
     */
    public function __construct($pool, $user, $amount)
    {
        $this->_pool = $pool;
        $this->queue = $this->_pool;
        $this->_user = $user;
        $this->_amount = $amount;
    }

    /**
     * Job預設處理動作
     * 執行交易員等候過期的事件
     *
     * @return void
     */
    public function handle()
    {
        $result = [
            'pool' => $this->_pool,
            'amount' => $this->_amount,
        ];
        //event(new TraderWaitingExpiredEvent($this->_user, $result));
    }

    /**
     * 取得Job資訊
     *
     * @return array [
     *                 pool   - 撮合池名稱
     *                 user   - trader的使用者資料
     *                 amount - 儲值餘額
     *               ]
     */
    public function getData() 
    {
        return [
            'pool' => $this->_pool,
            'user' => $this->_user,
            'amount' => $this->_amount,
        ];
    }
}
