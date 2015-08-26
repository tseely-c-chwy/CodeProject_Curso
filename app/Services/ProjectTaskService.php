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
    
    public function update($request, $id) {
        
        try {
            $this->validator->with($request->all())->passesOrFail();
            
            $projectTask = $this->repository->find($id);
            
            $projectTask->project_id = $request->get('project_id');
            $projectTask->name = $request->get('name');
            $projectTask->start_date = $request->get('start_date');
            $projectTask->due_date = $request->get('due_date');
            $projectTask->status = $request->get('status');

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