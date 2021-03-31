<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
//END ROUTES PASSPORT

//USER ROUTES
Route::resource('user','User\UserController',['except' => ['create','edit']]);
Route::get('getUserLogged','User\UserController@getUserLogged');
//END USER ROUTES

//CANDIDATE RUTE
Route::resource('candidate','Candidate\CandidateController',['except' => ['create','edit']]);
//END CANDIDATE RUTE
