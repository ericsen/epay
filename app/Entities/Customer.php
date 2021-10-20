<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\UtilityTrait;

/**
 * Class Customer.
 *
 * @package namespace App\Entities;
 */
class Customer extends Model implements Transformable
{
    use TransformableTrait;
    use UtilityTrait;

    protected $fillable = [
        'user_id',
        'company_name',
        'total_amount_limit',
        'api_secret_key',
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
