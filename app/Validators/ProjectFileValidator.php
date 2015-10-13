<?php

namespace CodeProject\Validators;

use \Prettus\Validator\LaravelValidator;
use Prettus\Validator\Contracts\ValidatorInterface;
/**
 * Description of ClientValidator
 *
 * @author thiago
 */
class ProjectFileValidator extends LaravelValidator {
    protected $rules = [
        ValidatorInterface::RULE_CREATE=>[
            'name'          => 'required',
            'file'          => 'required|mimes:jpg,jpeg,png,gif,pdf,zip',
            'description'   => 'required'            
        ],
        ValidatorInterface::RULE_UPDATE=>[
            'name'          => 'required',
            'description'   => 'required'            
        ]
    ];
}