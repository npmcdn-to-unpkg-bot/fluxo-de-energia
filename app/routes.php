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

Route::get('/', ['as' => 'home', 'uses' => 'PC@home']);
Route::get('user', ['as' => 'test', 'uses' => '']);
Route::get('products/{id?}', ['as' => 'test1', 'uses' => 'PC@godProduct']);
Route::get('make','PC@makeUsers');
Route::get('makeFunds/{id?}','PC@makeFunds');

?>
