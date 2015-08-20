<?php

namespace CodeProject\Transformers;

use CodeProject\Entities\User;
use League\Fractal\TransformerAbstract;

/**
 * Description of ProjectTransformer
 *
 * @author thiago
 */
class ProjectMemberTransformer extends TransformerAbstract {
    
    public function transform(User $member) {
        return [
            'member_id' => $member->id,
            'name'  => $member->name,
        ];
    }
    
}