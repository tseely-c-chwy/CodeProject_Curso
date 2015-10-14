<?php

namespace CodeProject\Services;
use CodeProject\Repositories\ProjectTaskRepository;
use CodeProject\Validators\ProjectTaskValidator;
use \Prettus\Validator\Exceptions\ValidatorException;

/**
 * Description of ProjectTaskService
 *
 * @author thiago
 */
class ProjectTaskService {
    
    protected $repository;
    protected $validator;
    
    public function __construct(ProjectTaskRepository $repository, ProjectTaskValidator $validator) {
        $this->repository = $repository;
        $this->validator = $validator;
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
    
    public function update($data, $id) {
        
        try {
            $this->validator->with($data)->passesOrFail();
            
            $projectTask = $this->repository->skipPresenter()->find($id);
            
            $projectTask->project_id = $data['project_id'];
            $projectTask->name = $data['name'];
            if (isset($data['start_date'])) {
                $projectTask->start_date = $data['start_date'];
            }
            if (isset($data['due_date'])) {
                $projectTask->due_date = $data['due_date'];
            }
            $projectTask->status = $data['status'];

            $projectTask->save();

            return $projectTask;
        } catch (ValidatorException $e) {
            return [
                'error' => true,
                'message' => $e->getMessageBag()
            ];
        }
        
    }
}