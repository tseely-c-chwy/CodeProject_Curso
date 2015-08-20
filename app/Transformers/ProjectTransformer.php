<?php

namespace CodeProject\Transformers;

use CodeProject\Entities\Project;
use League\Fractal\TransformerAbstract;

/**
 * Description of ProjectTransformer
 *
 * @author thiago
 */
class ProjectTransformer extends TransformerAbstract {
    
    protected $defaultIncludes = ['members'];
    
    public function transform(Project $project) {
        return [
            'project' => $project->name,
            'client' => $project->client->name,
            'owner' => $project->owner_id,
            'project_id' => $project->id,
            'description' => $project->description,
            'progress' => $project->progress,
            'status'    => $project->status,
            'due_date' => $project->due_date,
        ];
    }
    
    public function includeMembers(Project $project) {
        return $this->collection($project->members, new ProjectMemberTransformer());
    }
    
}
