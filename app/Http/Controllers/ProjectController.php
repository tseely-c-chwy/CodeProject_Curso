<?php

namespace CodeProject\Http\Controllers;

use Illuminate\Http\Request;

use CodeProject\Repositories\ProjectRepository;
use CodeProject\Services\ProjectService;

class ProjectController extends Controller
{
    private $repository;
    private $service;
    
    public function __construct(ProjectRepository $repository, ProjectService $service) {
        $this->service = $service;
        $this->repository = $repository;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //return $this->repository->with(['owner','client'])->findWhere(['owner_id' => \Authorizer::getResourceOwnerId()]);
        return $this->repository->with(['owner','client'])->skipPresenter()->all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        return $this->service->create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {   
        //if(!$this->checkProjectPermissions($id)) {
            //return ['error' => 'Access Denied'];
        //}

        return $this->service->find($id);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    { 
        /*if(!$this->checkProjectOwner($id)) {
            return ['error' => 'Access Denied'];
        }*/
        
        return $this->service->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        /*if(!$this->checkProjectOwner($id)) {
            return ['error' => 'Access Denied'];
        }*/
        
        return $this->service->delete($id);
    }
    
    public function listMembers($id) {
        $project = $this->repository->with(['members'])->skipPresenter()->find($id);
        return $project->members;
    }
    
    public function addMember($projectId, $memberId) {
        return $this->service->addMember($projectId, $memberId);
    }
    
    public function removeMember($projectId, $memberId) {
        return $this->service->removeMember($projectId, $memberId);
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
