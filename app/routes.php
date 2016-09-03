

<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
// Route::post('queue/offmail', function() { return Queue::marshal(); });

//any url will do like thresholdHandle as we are accessing by name
Route::post('/decayHandle', ['as' => 'decayHandle', 'uses' => 'UC@decayHandle']);
Route::post('/thresholdHandle', ['as' => 'thresholdHandle', 'uses' => 'UC@thresholdHandle']);
Route::get ('/', ['as' => 'homeView', 'uses' => 'GC@homeView']);
Route::post('/', ['as' => 'home', 'uses' => 'GC@home']);
Route::get('/testfn', ['as' => 'testfn', 'uses' => 'FC@testfn']);

Route::get ('/boost', ['as' => 'boostLE', 'uses' => 'admin@boostLE']);


Route::get('/login/{id?}', ['as' => 'login', 'uses' => 'UC@login']);
Route::get('/logout', ['as' => 'logout', 'uses' => 'UC@logout']);
Route::get('/testUserData', ['as' => 'testUserData', 'uses' => 'UC@testUserData']);

Route::get('/createProduct', ['as' => 'createProductForm', 'uses' => 'GC@createProductForm']);
Route::post('/createProduct', ['as' => 'createProduct', 'uses' => 'GC@createProduct']);

Route::get('/makeInvestment', ['as' => 'makeInvestmentForm', 'uses' => 'IC@makeInvestmentForm']);
Route::post('/makeInvestment', ['as' => 'makeInvestment', 'uses' => 'IC@makeInvestment']);

Route::get('/buyProduct', ['as' => 'buyProduct', 'uses' => 'FC@buyProduct']);



Route::get('selfProducts', ['as' => 'selfProducts', 'uses' => 'GC@selfProducts']);
Route::get('godProducts', ['as' => 'godProducts', 'uses' => 'IC@godProducts']);

Route::get('listFunds','PC@listFunds');

?>


