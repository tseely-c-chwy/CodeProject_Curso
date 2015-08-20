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
    
    public function __construct(ProjectRepository $repository, ProjectValidator $validator, Filesystem $filesystem, Storage $storage) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->filesystem = $filesystem;
        $this->storage = $storage;
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
    
    public function createFile(array $data) {
        $project = $this->repository->skipPresenter()->find($data['project_id']);
        
        $projectFile = $project->files()->create($data);
        
        $this->storage->put($projectFile->id.'.'.$data['extension'], $this->filesystem->get($data['file']));
    }
}