<?php

namespace App\Entities;

use Illuminate\Support\Str;
use App\Traits\UtilityTrait;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User.
 *
 * @package namespace App\Entities;
 */
class User extends Authenticatable implements Transformable
{
    use TransformableTrait;
    use LaratrustUserTrait;
    use Notifiable;
    use SoftDeletes;
    use UtilityTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'enable',
        'note',
        'nickname',
        'identity',
        'total_deposit',
        'total_brokerage',
        'parent_id',
        'hierarchical_path',
        'hierarchical_level',
        'inspector_id',
        'passed_at',

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'passed_at'         => 'datetime',
    ];


    protected $appends = ['extra_data'];

    /**
     * 提供給 laravel-debugbar show_name 使用
     * debugbar Auth status 預設抓 username 欄位，當username不存在，則抓取 email 欄位
     *
     * @return string
     */
    public function getUsernameAttribute()
    {
        return $this->name;
    }

    public function getParentIdAttribute()
    {
        return $this->attributes['parent_id'] = $this->attributes['parent_id'] === 0 ? null : $this->attributes['parent_id'];
    }

    public function setParentIdAttribute($value)
    {
        return $this->attributes['parent_id'] = empty($value) ? 0 : $value;
    }

    /**
     * 增加額外的帳號資料
     */
    public function getExtraDataAttribute()
    {
        switch ($this->identity) {
            case 'B':
                return null;
                break;

            case 'T':
                if (empty($this->trader)) {
                    $extraData = collect(\App\Entities\Trader::getAllFields())->flip()->map(function ($name) {
                        return '';
                    });
                    $extraData['user_id'] = $this->id;
                    return $extraData;
                }
                return $this->trader;
                break;

            case 'A':
                if (empty($this->agent)) {
                    $extraData =  collect(\App\Entities\Agent::getAllFields())->flip()->map(function ($name) {
                        return '';
                    });
                    $extraData['user_id'] = $this->id;
                    return $extraData;
                }
                return $this->agent;
                break;

            case 'C':
                if (empty($this->customer)) {
                    $extraData = collect(\App\Entities\Customer::getAllFields())->flip()->map(function ($name) {
                        return '';
                    });
                    $extraData['user_id'] = $this->id;
                    $extraData['api_secret_key'] = Str::uuid();
                    return $extraData;
                }
                return $this->customer;
                break;

            default:
                return null;
                break;
        }
    }




    // 審核人員
    public function inspector()
    {
        return $this->hasOne($this, $this->getKeyName(), 'inspector_id');
    }

    // 上一代帳號編號
    public function parent()
    {
        return $this->hasOne($this, $this->getKeyName(), 'parent_id');
    }

    // 下代帳號
    public function children()
    {
        return $this->hasMany($this, 'parent_id', $this->getKeyName());
    }

    // 帳號驗證資訊
    public function checks()
    {
        return $this->hasMany('App\Entities\CheckUser', 'user_id', $this->getKeyName());
    }

    // 帳號額外資訊(交易員)
    public function trader()
    {
        return $this->hasOne('App\Entities\Trader', 'user_id', 'id');
    }

    // 帳號額外資訊(代理商)
    public function agent()
    {
        return $this->hasOne('App\Entities\Agent', 'user_id', 'id');
    }

    // 帳號額外資訊(商戶)
    public function customer()
    {
        return $this->hasOne('App\Entities\Customer', 'user_id', 'id');
    }

    // 撮合池額外資訊(交易員)
    public function traderPool(){
        return $this->belongsToMany('App\Entities\matchPool', 'trader_pool', 'user_id', 'match_pool_id')
                ->using('App\Entities\TraderPool');
    }

    // 資料庫關聯 Laratrust 已經寫好了
    // public function roles()
    // {
    //     return $this->belongsToMany('App\Entities\Role');
    // }

    // public function permissions()
    // {
    //     return $this->belongsToMany('App\Entities\Permission');
    // }

}
