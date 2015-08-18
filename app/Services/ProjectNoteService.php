<?php

namespace CodeProject\Services;
use CodeProject\Repositories\ProjectNoteRepository;
use CodeProject\Validators\ProjectNoteValidator;
use \Prettus\Validator\Exceptions\ValidatorException;

/**
 * Description of ProjectNoteService
 *
 * @author thiago
 */
class ProjectNoteService {
    
    protected $repository;
    protected $validator;
    
    public function __construct(ProjectNoteRepository $repository, ProjectNoteValidator $validator) {
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
            
            $projecyNote = $this->repository->find($id);
            
            $projecyNote->project_id = $request->get('owner_id');
            $projecyNote->title = $request->get('title');
            $projecyNote->note = $request->get('note');

            $projecyNote->save();

            return $projecyNote;
        } catch (ValidatorException $e) {
            return [
                'error' => true,
                'message' => $e->getMessageBag()
            ];
        }
        
    }
}