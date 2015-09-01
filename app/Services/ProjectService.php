<?php

namespace CodeProject\Services;
use CodeProject\Repositories\ProjectRepository;
use CodeProject\Validators\ProjectValidator;
use \Prettus\Validator\Exceptions\ValidatorException;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\Factory as Storage;

/**
 * Description of ProjectService
 *
 * @author thiago
 */
class ProjectService {
    
    protected $repository;
    protected $validator;
    protected $filesystem;
    protected $storage;
    protected $fileValidator;
    
    public function __construct(ProjectRepository $repository, ProjectValidator $validator, Filesystem $filesystem, Storage $storage) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->filesystem = $filesystem;
        $this->storage = $storage;
    }
    
    public function find($id) {

        if ($this->repository->exists($id)) {
            return $this->repository->with(['owner','client'])->skipPresenter()->find($id);
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
        
        try {
            
            if (!$this->repository->exists($id)) {
                return [
                    'error' => true,
                    'message' => 'Project does not exist.',
                ];          
            }
            
            $this->validator->with($request->all())->passesOrFail();
            
            $project = $this->repository->find($id);
            
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
        
        if (!$this->repository->exists($id)) {
            return [
                'error' => true,
                'message' => 'Project does not exist.',
            ];          
        }
        
        
        return $this->repository->find($id)->delete();
    }
    
    public function createFile(array $data) {
        
        if (!$this->repository->exists($data['project_id'])) {
            return [
                'error' => true,
                'message' => 'Project does not exist.',
            ];          
        }
        
        if (empty($data['name']) || empty($data['description'])) {
            return [
                'error' => true,
                'message' => 'File name and description are required.',
            ];            
        }

        if (empty($data['extension'])) {
            return [
                'error' => true,
                'message' => 'System could not get the file extension.',
            ];            
        }

        $project = $this->repository->skipPresenter()->find($data['project_id']);

        $projectFile = $project->files()->create($data);

        return (string)$this->storage->put($projectFile->id.'.'.$data['extension'], $this->filesystem->get($data['file']));
        
    }
    
    public function removeFile($projectId, $fileId) {
        
        if (!$this->repository->exists($projectId)) {
            return [
                'error' => true,
                'message' => 'Project does not exist.',
            ];          
        }
        
        $project = $this->repository->skipPresenter()->with(['files'])->find($projectId);
        
        if (!count($project->files)) {
            return [
                'error' => true,
                'message' => 'Project has no files.',
            ];           
        }
        
        foreach($project->files as $file) {
            if ($file->id == $fileId) {
                $filename = $file->id.'.'.$file->extension;
                $project->files()->where(['id' => $fileId])->delete();
                $this->storage->delete($filename);
                return [
                    'error' => false,
                    'message' => 'File deleted.',
                ]; 
            }
        }
        
        return [
            'error' => true,
            'message' => 'File not found.',
        ]; 
        
    }
    
    public function isMember($projectId, $memberId) {
        return $this->repository->hasMember($projectId, $memberId);
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
}