<?php

namespace CodeProject\Services;
use CodeProject\Repositories\ProjectRepository;
use CodeProject\Validators\ProjectValidator;
use \Prettus\Validator\Exceptions\ValidatorException;

use Illuminate\Contracts\Filesystem\Factory as Storage;

/**
 * Description of ProjectService
 *
 * @author thiago
 */
class ProjectService {
    
    protected $repository;
    protected $validator;
    
    public function __construct(ProjectRepository $repository, ProjectValidator $validator) {
        $this->repository = $repository;
        $this->validator = $validator;
    }
    
    public function find($id) {
        
        if(!$this->checkProjectPermissions($id)) {
            return ['error' => 'Access Denied'];
        }

        if ($this->repository->exists($id)) {
            return $this->repository->with('client')->skipPresenter()->find($id);
        }
        
        return [
            'error' => true,
            'message' => 'Project not found.',
        ];
    }
    
    public function create(array $data) {
        
        try {
            $this->validator->with($data)->passesOrFail();
            return $this->repository->create($data);
        } catch (ValidatorException $e) {
            return [
                'error' => true,
                'message' => $e->getMessageBag()
            ];
        }
        
    }
    
    public function update($request, $id) {
        
        if(!$this->checkProjectOwner($id)) {
            return ['error' => 'Access Denied'];
        }
        
        try {
            
            if (!$this->repository->exists($id)) {
                return [
                    'error' => true,
                    'message' => 'Project does not exist.',
                ];          
            }
            
            $this->validator->with($request->all())->passesOrFail();
            
            $project = $this->repository->skipPresenter()->find($id);
            
            $project->owner_id = $request->get('owner_id');
            $project->client_id = $request->get('client_id');
            $project->name = $request->get('name');
            $project->description = $request->get('description');
            $project->progress = $request->get('progress');
            $project->due_date = $request->get('due_date');
            $project->status = $request->get('status');

            $project->save();

            return $project;
        } catch (ValidatorException $e) {
            return [
                'error' => true,
                'message' => $e->getMessageBag()
            ];
        }
        
    }
    
    public function delete($id) {
        
        if(!$this->checkProjectOwner($id)) {
            return ['error' => true, 'message' => 'Access Denied'];
        }
        
        if (!$this->repository->exists($id)) {
            return [
                'error' => true,
                'message' => 'Project does not exist.',
            ];          
        }
        
        
        return (string)$this->repository->skipPresenter()->find($id)->delete();
    }
    
    public function addMember($projectId, $memberId) {
        
        if (!$this->repository->exists($projectId)) {
            return [
                'error' => true,
                'message' => 'Project does not exist.',
            ];          
        }
        
        if ($this->repository->hasMember($projectId, $memberId)) {
            return [
                'error' => true,
                'message' => 'Member already belongs to project.',
            ];          
        }
        
        $project = $this->repository->skipPresenter()->find($projectId);
        
        $project->members()->attach($memberId);
        
        return ['error' => false, 'message' => 'Member added to project.'];
    }
    
    public function removeMember($projectId, $memberId) {
        
        if (!$this->repository->exists($projectId)) {
            return [
                'error' => true,
                'message' => 'Project does not exist.',
            ];          
        }
        
        if (!$this->repository->hasMember($projectId, $memberId)) {
            return [
                'error' => true,
                'message' => 'Project does not have this member.',
            ];          
        }
        
        $project = $this->repository->skipPresenter()->find($projectId);
        
        $project->members()->detach($memberId);
        
        return ['error' => false, 'message' => 'Member removed from project.'];
    }
    
    public function listMembers($id) {
        $project = $this->repository->with(['members'])->find($id);
        return $project->members;
    }
    
    public function isMember($projectId, $memberId) {
        if ($this->repository->hasMember($projectId, $memberId)) {
            return ['message' => 'User is member.'];
        }
        
        return ['message' => 'User is not a member'];
    }
    
    private function checkProjectOwner($projectId) {
        
        $userId = \Authorizer::getResourceOwnerId();
        return $this->repository->isOwner($projectId, $userId);
     
    }
    
    private function checkProjectMember($projectId) {
        
        $userId = \Authorizer::getResourceOwnerId();
        return $this->repository->hasMember($projectId, $userId);
     
    }
    
    private function checkProjectPermissions($projectId) {
        
        if ($this->checkProjectOwner($projectId) || $this->checkProjectMember($projectId)) {
            return true;
        }
        
        return false;
    }
}