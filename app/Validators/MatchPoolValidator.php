<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class MatchPoolValidator.
 *
 * @package namespace App\Validators;
 */
class MatchPoolValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'pool_display_name' => 'required',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'pool_display_name' => 'required',
        ],
    ];
}
