<?php

namespace CodeProject\Services;

use CodeProject\Repositories\ProjectFileRepository;
use CodeProject\Services\ProjectService;
use CodeProject\Validators\ProjectFileValidator;
use CodeProject\Repositories\ProjectRepository;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use Prettus\Validator\Contracts\ValidatorInterface;

/**
 * Description of ProjectFileService
 *
 * @author thiago
 */
class ProjectFileService {
    
    protected $repository;
    protected $validator;
    protected $projectRepository;
    protected $projectService;
    private $filesystem;
    private $storage;
    
    public function __construct(ProjectFileRepository $repository, ProjectFileValidator $validator,
            ProjectRepository $projectRepository, ProjectService $projectService, Filesystem $filesystem,
            Storage $storage) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->projectRepository = $projectRepository;
        $this->filesystem = $filesystem;
        $this->storage = $storage;
        $this->projectService = $projectService;
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

        $this->storage->put($projectFile->getFileName(), $this->filesystem->get($data['file']));
        
        return $projectFile;
    }
    
    public function find($id, $fileId) {
        if(!$this->projectService->checkProjectPermissions($id)) {
            return ['error' => 'Access Denied'];
        }
        
        return $this->repository->find($fileId);        
    }
    
    public function showFile($id, $fileId) {
        if(!$this->projectService->checkProjectPermissions($id)) {
            return ['error' => 'Access Denied'];
        }
        
        $filePath = $this->getFilePath($fileId);
        $fileContent = file_get_contents($filePath);
        $file64 = base64_encode($fileContent);
        
        return [
            'file' => $file64,
            'size' => filesize($filePath),
            'name' => $this->getFileName($fileId),
        ];
    }
    
    public function getFilePath($id) {
        $projectFile = $this->repository->skipPresenter()->find($id);
        return $this->getBaseURL($projectFile);
    }
    
    public function getFileName($id) {
        $projectFile = $this->repository->skipPresenter()->find($id);
        return $projectFile->getFileName();
    }
    
    public function getBaseURL($projectFile) {
        switch($this->storage->getDefaultDriver()) {
            case 'local':
                return $this->storage->getDriver()->getAdapter()->getPathPrefix()
                    .'/'.$projectFile->getFileName();
        }
    }
    
    public function update($data, $id) {
        if(!$this->projectService->checkProjectOwner($data['project_id'])) {
            return ['error' => 'Access Denied'];
        }
        
        try {
            
            $this->validator->with($data)->passesOrFail(ValidatorInterface::RULE_UPDATE);
            
            $file = $this->repository->skipPresenter()->find($id);
            
            $file->name = $data['name'];
            $file->description = $data['description'];

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
        
        if(!$this->projectService->checkProjectOwner($id)) {
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
                if ($this->storage->exists($filename)) {
                    $this->storage->delete($filename);
                }
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
    
}