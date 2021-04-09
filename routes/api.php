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
Route::get('createReport', 'Candidate\CandidateController@createReport');
Route::get('createReportByUser', 'Candidate\CandidateController@createReportByUser');
Route::get('validate_elector_key', 'Candidate\CandidateController@validateElectorKey');
Route::get('validate_elector_ocr', 'Candidate\CandidateController@validateOCR');
Route::get('getAyuntamiento/{postulate}', 'Candidate\CandidateController@getAyuntamiento');
Route::post('importLayout', 'Candidate\CandidateController@importLayout');
Route::get('reportCityHall', 'Candidate\CandidateController@getReportCityHall');
//END CANDIDATE RUTE

//POLITIC PARTY ROUTE
Route::resource('politicParty','PoliticParty\PoliticPartyController',['only' => ['index']]);
//END POLITIC PARTY ROUTE

//CANDIDATE INE ROUTE
Route::resource('candidateIne','CantidateIne\CandidateIneController',['only' => ['index','store']]);
Route::get('candidateIne/{candidate}', 'CantidateIne\CandidateIneController@show');
Route::put('candidateIne/{candidate}', 'CantidateIne\CandidateIneController@update');
Route::delete('candidateIne/{candidate}', 'CantidateIne\CandidateIneController@destroy');

Route::get('createReportIne', 'CantidateIne\CandidateIneController@createReportINE');
Route::get('createReportIneByUser', 'CantidateIne\CandidateIneController@createReportINEByUser');
Route::get('createSpecialReport', 'CantidateIne\CandidateIneController@createSpecialReportINE');
//END CANDIDATE INE ROUTE

//POLITIC PARTY ROUTE
Route::resource('postulate','Postulate\PostulateController',['only' => ['show', 'index']]);
Route::get('getMunicipalities', 'Postulate\PostulateController@getMunicipalities');
//END POLITIC PARTY ROUTE
