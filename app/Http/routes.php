<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('app');
});

Route::post('oauth/access_token', function() {
    return Response::json(Authorizer::issueAccessToken());
});

//Route::group(['middleware' => 'oauth'], function() {
    
    Route::resource('client','ClientController', ['except'=>['create','edit']]);
    Route::resource('project','ProjectController', ['except'=>['create','edit']]);
    
    /*Route::group(['middleware'=>'CheckProjectOwner'], function() {
        Route::resource('project','ProjectController', ['except'=>['create','edit']]);
    });*/
    
    Route::group(['prefix'=>'project'], function() {
        
        Route::get('{id}/note','ProjectNoteController@index');
        Route::post('{id}/note','ProjectNoteController@store');
        Route::get('{id}/note/{noteId}','ProjectNoteController@show');
        Route::delete('{id}/note/{noteId}','ProjectNoteController@destroy');
        Route::put('{id}/note/{noteId}', 'ProjectNoteController@update');
       
        Route::get('{id}/tasks','ProjectTaskController@index');
        Route::post('{id}/task','ProjectTaskController@store');
        Route::get('{id}/task/{noteId}','ProjectTaskController@show');
        Route::delete('{id}/task/{noteId}','ProjectTaskController@destroy');
        Route::put('{id}/task/{noteId}', 'ProjectTaskController@update');
        
        Route::get('{id}/members','ProjectController@listMembers');
        
        //Rotas teste para projeto Fase 3
        Route::get('{projectId}/member/{memberId}','ProjectController@isMember');
        Route::post('{projectId}/member/{memberId}/add','ProjectController@addMember');
        Route::post('{projectId}/member/{memberId}/remove','ProjectController@removeMember');
        
        Route::post('{id}/file','ProjectFileController@store');
    });
    
//});