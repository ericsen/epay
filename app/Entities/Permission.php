<?php

namespace App\Entities;

use Laratrust\Models\LaratrustPermission;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Permission.
 *
 * @package namespace App\Entities;
 */
class Permission extends LaratrustPermission implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'use_in',
    ];

    // protected $hidden = ['pivot'];

    /**
     * 追加到模型數組表單的訪問器。
     *
     * @var array
     */
    protected $appends = ['use_in_trans'];


    /**
     * 追加資料
     *
     * @return void
     */

    public function getUseInTransAttribute()
    {
        switch ($this->use_in) {
            case 'B':
                return __('contents.general.use_in_back');
                break;
            case 'F':
                return __('contents.general.use_in_front');
                break;
            default:
                return __('contents.general.undefined');;
                break;
        }

    }

    // 資料庫關聯 Laratrust 已經寫好了
    // public function roles()
    // {
    //     return $this->belongsToMany('App\Entities\Role');
    // }

    // public function users()
    // {
    //     return $this->belongsToMany('App\Entities\User');
    // }

}
