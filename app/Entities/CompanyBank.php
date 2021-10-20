<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class CompanyBank.
 *
 * @package namespace App\Entities;
 */
class CompanyBank extends Model implements Transformable
{
    use TransformableTrait;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bank_name',
        'bank_branch',
        'bank_account_name',
        'bank_account_number',
        'enable',
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
                return __('contents.company_banks.enable_on');
                break;
            case 'off':
                return __('contents.company_banks.enable_off');
                break;
            default:
                return __('contents.general.undefined');;
                break;
        }

    }
}