<?php

namespace CodeProject\Validators;

use \Prettus\Validator\LaravelValidator;

/**
 * Description of ClientValidator
 *
 * @author thiago
 */
class ProjectFileValidator extends LaravelValidator {
    protected $rules = [
        'project_id'    => 'required|integer',
        'name'          => 'required',
        'file'          => 'required|mimes:jpg,jpeg,png,gif,pdf,zip',
        'description'   => 'required'
    ];
}