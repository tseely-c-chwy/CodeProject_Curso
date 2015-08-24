<?php

namespace CodeProject\Services;
use CodeProject\Repositories\ClientRepository;
use CodeProject\Validators\ClientValidator;
use \Prettus\Validator\Exceptions\ValidatorException;

/**
 * Description of ClientService
 *
 * @author thiago
 */
class ClientService {
    
    protected $repository;
    protected $validator;
    
    public function __construct(ClientRepository $repository, ClientValidator $validator) {
        $this->repository = $repository;
        $this->validator = $validator;
    }
    
    public function find($id) {
        
        if ($this->repository->exists($id)) {
            return $this->repository->find($id);
        }
        
        return [
            'error' => true,
            'message' => 'Client not found.',
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
                    'message' => 'Client does not exist.',
                ];          
            }
            
            $this->validator->with($request->all())->passesOrFail();
            
            $client = $this->repository->find($id);

            $client->name = $request->get('name');
            $client->responsible = $request->get('responsible');
            $client->email = $request->get('email');
            $client->address = $request->get('address');
            $client->phone = $request->get('phone');
            $client->obs = $request->get('obs');

            $client->save();

            return $client;
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
                'message' => 'Client does not exist.',
            ];          
        }
        
        return $this->repository->find($id)->delete();
    }
    
}