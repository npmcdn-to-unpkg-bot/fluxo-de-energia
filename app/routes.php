

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


Route::get('/testfn', ['as' => 'testfn', 'uses' => 'IC@testfn']);
Route::get('/testBid', ['as' => 'testBid', 'uses' => 'IC@testBid']);
Route::get('/testAMG', ['as' => 'testAMG', 'uses' => 'AmoghController@testAMG']);
Route::get('/buyFruit', ['as' => 'buyFruit', 'uses' => 'AmoghController@buyFruit']);

Route::get ('/boost', ['as' => 'boostLE', 'uses' => 'admin@boostLE']);
Route::get ('/updateUsers', ['as' => 'updateUsers', 'uses' => 'admin@updateUsers']);

Route::get('/login/{id?}', ['as' => 'login', 'uses' => 'UC@login']);

//in product controller
Route::post('/bidHandle', ['as' => 'bidHandle', 'uses' => 'IC@bidHandle']);


Route::group(array('before' => 'user'), function()
{
Route::get('/', function(){return View::make('home');});
Route::post('/', ['as' => 'home', 'uses' => 'UC@home']);
Route::post('/decayHandle', ['as' => 'decayHandle', 'uses' => 'UC@decayHandle']);
Route::post('/thresholdHandle', ['as' => 'thresholdHandle', 'uses' => 'UC@thresholdHandle']);
Route::get('/testUserData', ['as' => 'testUserData', 'uses' => 'UC@testUserData']);
Route::get('/logout', ['as' => 'logout', 'uses' => 'UC@logout']);
});

Route::group(array('before' => 'investor'), function()
{
	Route::get('/makeInvestment', function(){return View::make('invest')->with('products',Product::where('being_funded',1)
		->where('avl_shares','>',0)
		->get());});
	Route::post('/makeInvestment', ['as' => 'makeInvestment', 'uses' => 'IC@makeInvestment']);
	Route::get('godProducts', ['as' => 'godProducts', 'uses' => 'IC@godProducts']);
});

Route::group(array('before' => 'god'), function()
{
Route::get('listFunds','PC@listFunds'); //may remove this later

Route::get('selfProducts', ['as' => 'selfProducts', 'uses' => 'GC@selfProducts']); //this gives FUNDING BAR
Route::get('/createProduct', function(){return View::make('createProd')
	->with('c1',Config::get('game.baseC1'))
	->with('c2',Config::get('game.baseC2'))
	->with('c3',Config::get('game.baseC3'))
	->with('c4',Config::get('game.baseC4'))
	->with('k',Config::get('game.basePrices'));
	});

 // ['as' => 'createProductForm', 'uses' => 'GC@createProductForm']);
Route::post('/createProduct', ['as' => 'createProduct', 'uses' => 'GC@createProduct']);
});
Route::group(array('before' => 'farmer'), function()
{
	//add sellfruit url soon
Route::get('/buyProduct', function(){return View::make('buyProduct');});
Route::post('/buyProduct', ['as' => 'buyProduct', 'uses' => 'FC@buyProduct'] );
});

?>


