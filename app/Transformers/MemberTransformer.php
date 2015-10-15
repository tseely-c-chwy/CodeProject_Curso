<?php

namespace CodeProject\Transformers;

use CodeProject\Entities\User;
use League\Fractal\TransformerAbstract;

/**
 * Class MemberTransformer
 * @package namespace CodeProject\Transformers;
 */
class MemberTransformer extends TransformerAbstract
{

    /**
     * Transform the User entity
     * @param \User $member
     *
     * @return array
     */
    public function transform(User $member) {
        return [
            'member_id'     => $member->id,
            'name'          => $member->name
        ];
    }
}