<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class CompanyBankValidator.
 *
 * @package namespace App\Validators;
 */
class CompanyBankValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'bank_name'           => 'required',
            'bank_branch'         => 'required',
            'bank_account_name'   => 'required',
            'bank_account_number' => 'required',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'bank_name'           => 'required',
            'bank_branch'         => 'required',
            'bank_account_name'   => 'required',
            'bank_account_number' => 'required',
        ],
    ];
}
