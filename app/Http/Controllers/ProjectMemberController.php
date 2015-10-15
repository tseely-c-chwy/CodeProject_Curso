<?php

namespace CodeProject\Http\Controllers;

use Illuminate\Http\Request;

use CodeProject\Http\Requests;
use CodeProject\Http\Controllers\Controller;
use CodeProject\Repositories\ProjectMemberRepository;
use CodeProject\Services\ProjectMemberService;

class ProjectMemberController extends Controller
{
    
    private $repository;
    private $service;
    
    public function __construct(ProjectMemberRepository $repository, ProjectMemberService $service) {
        $this->service = $service;
        $this->repository = $repository;
        $this->middleware('check.project.owner', ['except' => ['show','index']]);
        $this->middleware('check.project.permission', ['except' => ['store','destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($id)
    {
        return $this->repository->findWhere(['project_id' => $id]);
    }
    
    public function show($id, $projectMemberId) {
        return $this->repository->find($projectMemberId);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request, $id)
    {
        $data = $request->all();
        $data['project_id'] = $id;
        return $this->service->create($data);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id, $projectMemberId)
    {
       return $this->service->delete($projectMemberId);
    }
}
