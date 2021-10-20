<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class MatchPool.
 *
 * @package namespace App\Entities;
 */
class MatchPool extends Model implements Transformable
{
    use TransformableTrait;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pool_name',
        'pool_display_name',
        'enable',
        'note',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    protected $appends = ['enable_trans'];

    public function getEnableTransAttribute()
    {
        switch ($this->enable) {
            case 'on':
                return __('contents.match_pools.enable_on');
                break;
            case 'off':
                return __('contents.match_pools.enable_off');
                break;
            default:
                return __('contents.general.undefined');;
                break;
        }

    }

    /**
     * 與tradr_pool關聯的App\Entities\User
     *
     * @return Eloquent
     */
    public function usersInTraderPool(){
        return $this->belongsToMany('App\Entities\User', 'trader_pool', 'match_pool_id', 'user_id')->using('App\Entities\TraderPool');
    }
}
