<?php

namespace App\Entities;

use Laratrust\Models\LaratrustRole;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Role.
 *
 * @package namespace App\Entities;
 */
class Role extends LaratrustRole implements Transformable
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
        'use_for',
    ];

    // protected $hidden = ['pivot'];

    /**
     * 追加到模型數組表單的訪問器。
     *
     * @var array
     */
    protected $appends = ['use_for_trans'];

    /**
     * 追加資料
     *
     * @return void
     */

    public function getUseForTransAttribute()
    {
        switch ($this->use_for) {
            case 'B':
                return __('contents.general.use_for_back');
                break;
            case 'T':
                return __('contents.general.use_for_trader');
                break;
            case 'A':
                return __('contents.general.use_for_agent');
                break;
            case 'C':
                return __('contents.general.use_for_customer');
                break;

            default:
                return __('contents.general.undefined');
                break;
        }

    }

    // 資料庫關聯 Laratrust 已經寫好了
    // public function permissions()
    // {
    //     return $this->belongsToMany('App\Entities\Permission');
    // }

    // public function users()
    // {
    //     return $this->belongsToMany('App\Entities\User');
    // }

}
