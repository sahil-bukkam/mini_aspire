<?php


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

//Auth
Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::post('logout', 'AuthController@logout');

// Loans
Route::apiResource('loans', 'LoanController',  [
    'only' => ['index', 'show', 'store', 'update']
  ]);
// Installments
Route::post('loans/{loan}/installments', 'InstallmentController@store')->middleware('auth:api');

