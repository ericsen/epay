<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class TradePaymentOrderValidator.
 *
 * @package namespace App\Validators;
 */
class TradePaymentOrderValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'user_id'                => 'bail|required',
            // 'user_name'              => 'bail|required',
            // 'user_nickname'          => 'bail|required',
            'amount'                 => 'bail|required',
            'bank_name'              => 'bail|required',
            'bank_branch'            => 'bail|required',
            'bank_account_name'      => 'bail|required',
            'bank_account_number'    => 'bail|required',
            'to_bank_name'           => 'bail|required',
            'to_bank_branch'         => 'bail|required',
            'to_bank_account_name'   => 'bail|required',
            'to_bank_account_number' => 'bail|required',
            'bank_order_sn'          => 'bail|required',
            'bank_slip'              => 'bail|required',
        ],
        ValidatorInterface::RULE_UPDATE => [
            // 'status' => 'bail|required'
        ],
    ];
}
