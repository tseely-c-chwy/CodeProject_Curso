<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CodeProject\Repositories;

use CodeProject\Entities\Client;
use \Prettus\Repository\Eloquent\BaseRepository;
use CodeProject\Presenters\ClientPresenter;

/**
 * Description of ClientRepositoryEloquent
 *
 * @author thiago
 */
class ClientRepositoryEloquent extends BaseRepository implements ClientRepository {
    
    protected $fieldSearchable = ['name'];
    
    public function model() {
        return Client::class;
    }
    
    public function exists($id) {
        if (count($this->findWhere(['id'=>$id]))) {
            return true;
        }
        
        return false;
    }
    
    public function isAssociatedWithProject($id) {
        $client = $this->find($id);

        if (count($client->projects)) {
            return true;
        }
        
        return false;
    }
    
    public function presenter() {
        return ClientPresenter::class;
    }

}
