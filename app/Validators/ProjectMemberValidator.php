<?php

namespace CodeProject\Validators;

use \Prettus\Validator\LaravelValidator;

/**
 * Description of ClientValidator
 *
 * @author thiago
 */
class ProjectMemberValidator extends LaravelValidator {
    protected $rules = [
        'member_id'     => 'required',
        'project_id'    => 'required',
    ];
}