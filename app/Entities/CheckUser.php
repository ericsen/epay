<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class CheckUser.
 *
 * @package namespace App\Entities;
 */
class CheckUser extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'type',
        'qrcode_data',
        'qrcode_nickname',
        'is_checked',
        'inspector_id',
        'checked_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'checked_at'         => 'datetime',
    ];


    // 擁有者
    public function user()
    {
        return $this->belongsTo('App\Entities\User', 'user_id', 'id');
    }

    // 審核人員
    public function inspector()
    {
        return $this->belongsTo('App\Entities\User', 'inspector_id', 'id');
    }





}
