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
        return $this->repository->with(['owner','client'])->all();
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
        return $this->repository->with(['owner','client'])->find($id);
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
       $this->repository->find($id)->delete();
    }
}
