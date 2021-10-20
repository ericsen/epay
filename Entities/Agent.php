<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\UtilityTrait;

/**
 * Class Agent.
 *
 * @package namespace App\Entities;
 */
class Agent extends Model implements Transformable
{
    use TransformableTrait;
    use UtilityTrait;

    protected $fillable = [
        'user_id',
        'contact_person',
        'contact_phone',
        'note',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    // 擁有者
    public function user()
    {
        return $this->belongsTo('App\Entities\User', 'id', 'user_id');
    }

}
