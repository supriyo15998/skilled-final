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

Route::middleware('auth:api')->get('/me', function (Request $request) {
    return \App\User::with('role')->findOrFail($request->user()->id);
});

Route::post('/auth/register', 'Api\\AuthController@register');
Route::post('/auth/login', 'Api\\AuthController@login');
Route::get('/auth/register/generic', 'Api\\AuthController@generic');
Route::get('/companies', 'Api\\AuthController@getCompanies');
Route::get('/question/{level}', 'Api\\AuthController@getQuestions');
Route::get('/profile/{userId}', 'Api\\AuthController@getUser');
Route::get('/subjects', 'Api\\AuthController@getSubjects');
Route::middleware('auth:api')->get('/tests', 'Api\\AuthController@getMyTestsWithSubjects');

