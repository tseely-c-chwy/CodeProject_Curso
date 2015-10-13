<?php

namespace CodeProject\Validators;

use \Prettus\Validator\LaravelValidator;

/**
 * Description of ClientValidator
 *
 * @author thiago
 */
class ProjectTaskValidator extends LaravelValidator {
    protected $rules = [
        'name'          => 'required',
    ];
}