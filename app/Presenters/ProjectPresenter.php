<?php

namespace CodeProject\Presenters;

use Prettus\Repository\Presenter\FractalPresenter;
use CodeProject\Transformers\ProjectTransformer;

/**
 * Description of ProjectPresenter
 *
 * @author thiago
 */
class ProjectPresenter extends FractalPresenter {

    public function getTransformer() {
        return new ProjectTransformer();
    }
    
}