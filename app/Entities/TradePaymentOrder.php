<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class TradePaymentOrder.
 *
 * @package namespace App\Entities;
 */
class TradePaymentOrder extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'user_name',
        'user_nickname',
        'trader_id',
        'payment_order_sn',
        'amount',
        'bank_name',
        'bank_branch',
        'bank_account_name',
        'bank_account_number',
        'to_bank_name',
        'to_bank_branch',
        'to_bank_account_name',
        'to_bank_account_number',
        'bank_order_sn',
        'bank_slip',
        'inspector_id',
        'status',
        'checked_at',
        'note',
    ];

    protected $hidden = [
        'updated_at',
    ];

    // 擁有者
    public function user()
    {
        return $this->belongsTo('App\Entities\User', 'user_id', 'id');
    }

    // 擁有者資料(交易員)
    public function trader()
    {
        return $this->belongsTo('App\Entities\Trader', 'trader_id', 'id');
    }

    // 審核人員
    public function inspector()
    {
        return $this->belongsTo('App\Entities\User', 'inspector_id', 'id');
    }

}
