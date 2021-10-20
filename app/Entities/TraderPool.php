<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class TraderPool.
 *
 * @package namespace App\Entities;
 */
class TraderPool extends Pivot implements Transformable
{
    use TransformableTrait;


    protected $table = 'trader_pool';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'match_pool_id',
        'user_id'
    ];

    public function matchPool()
    {
        return $this->belongsTo('App\Entities\MatchPool');
    }

    public function user()
    {
        return $this->belongsTo('App\Entities\User');
    }
}
