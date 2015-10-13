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
        'title'         => 'required',
        'note'          => 'required'
    ];
}