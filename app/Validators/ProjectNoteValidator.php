<?php

namespace CodeProject\Validators;

use \Prettus\Validator\LaravelValidator;

/**
 * Description of ClientValidator
 *
 * @author thiago
 */
class ProjectNoteValidator extends LaravelValidator {
    protected $rules = [
        'project_id'    => 'required|integer',
        'title'         => 'required',
        'note'          => 'required'
    ];
}