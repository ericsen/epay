<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class UserValidator.
 *
 * @package namespace App\Validators;
 */
class UserValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'name'                  => 'bail|required|unique:users,name',
            'email'                 => 'bail|required|email',
            // 'password'              => 'bail|required|min:6|confirmed',  //寫於Request中
            // 'password_confirmation' => 'bail|min:6|same:password',       //寫於Request中
            // 'display_name' => 'required',
            // 'description'  => 'required',
        ],
        ValidatorInterface::RULE_UPDATE => [
            // 'name'                  => 'bail|required|unique:users,name',
            'email'                 => 'bail|required|email',
            // 'password'              => 'bail|min:6|confirmed',               //寫於Request中
            // 'password_confirmation' => 'bail|required|min:6|same:password',  //寫於Request中
            // 'display_name' => 'required',
            // 'description'  => 'required',
        ],
    ];
}
