<?php

namespace CodeProject\Services;

use CodeProject\Repositories\ProjectFileRepository;
use CodeProject\Validators\ProjectFileValidator;
use CodeProject\Repositories\ProjectRepository;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\Factory as Storage;

/**
 * Description of ProjectFileService
 *
 * @author thiago
 */
class ProjectFileService {
    
    protected $repository;
    protected $validator;
    protected $projectRepository;
    private $filesystem;
    private $storage;
    
    public function __construct(ProjectFileRepository $repository, ProjectFileValidator $validator,
            ProjectRepository $projectRepository, Filesystem $filesystem,
            Storage $storage) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->projectRepository = $projectRepository;
        $this->filesystem = $filesystem;
        $this->storage = $storage;
    }
    
    public function createFile(array $data) {
        
        if (empty($data['name']) || empty($data['name']) || empty($data['description'])) {
            return [
                'error' => true,
                'message' => '"project_id", "name" and "description" are required fields.',
            ];            
        }
        
        if (!$this->projectRepository->exists($data['project_id'])) {
            return [
                'error' => true,
                'message' => 'Project does not exist, please enter a correct prject_id param.',
            ];          
        }

        if (empty($data['extension'])) {
            return [
                'error' => true,
                'message' => 'System could not get the file extension.',
            ];            
        }

        $project = $this->projectRepository->skipPresenter()->find($data['project_id']);

        $projectFile = $project->files()->create($data);

        $this->storage->put($projectFile->id.'.'.$data['extension'], $this->filesystem->get($data['file']));
        
        return $projectFile;
    }
    
    public function find($id) {
        if(!$this->checkProjectPermissions($id)) {
            return ['error' => 'Access Denied'];
        }
        
        return $this->projectRepository->with(['owner','client'])->find($id);        
    }
    
    public function showFile($id) {
        if(!$this->checkProjectPermissions($id)) {
            return ['error' => 'Access Denied'];
        }
        
        return response()->download($this->getFilePath($id));
    }
    
    public function getFilePath($id) {
        $projectFile = $this->repository->skipPresenter()->find($id);
        return $this->getBaseURL($projectFile);
    }
    
    public function getBaseURL($projectFile) {
        switch($this->storage->getDefaultDriver()) {
            case 'local':
                return $this->storage->getDriver()->getAdapter()->getPathPrefix()
                    .'/'.$projectFile->id.'.'.$projectFile->extension;
        }
    }
    
    public function update($request, $id) {
        if(!$this->checkProjectOwner($id)) {
            return ['error' => 'Access Denied'];
        }
        
        try {
            
            $this->validator->with($request->all())->passesOrFail();
            
            $file = $this->projectRepository->skipPresenter()->find($id);
            
            $file->name = $request->get('name');
            $file->description = $request->get('description');

            $file->save();

            return $file;
        } catch (ValidatorException $e) {
            return [
                'error' => true,
                'message' => $e->getMessageBag()
            ];
        }        
    }
    
    public function delete($id, $fileId) {
        
        if(!$this->checkProjectOwner($id)) {
            return ['error' => 'Access Denied'];
        }
        
        return $this->removeFile($id, $fileId);        
    }
    
    public function removeFile($projectId, $fileId) {
        
        if (!$this->projectRepository->exists($projectId)) {
            return [
                'error' => true,
                'message' => 'Project does not exist.',
            ];          
        }
        
        $project = $this->projectRepository->with(['files'])->skipPresenter()->find($projectId);
        
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
    
    private function checkProjectOwner($projectFileId) {
        
        $userId = \Authorizer::getResourceOwnerId();
        $projectId = $this->repository->skipPresenter()->find($projectFileId)->project_id;
        
        return $this->projectRepository->isOwner($projectId, $userId);
     
    }
    
    private function checkProjectMember($projectFileId) {
        
        $userId = \Authorizer::getResourceOwnerId();
        $projectId = $this->repository->skipPresenter()->find($projectFileId)->project_id;
        
        return $this->projectRepository->hasMember($projectId, $userId);
     
    }
    
    private function checkProjectPermissions($projectFileId) {
        
        if ($this->checkProjectOwner($projectFileId) || $this->checkProjectMember($projectFileId)) {
            return true;
        }
        
        return false;
    }
    
}