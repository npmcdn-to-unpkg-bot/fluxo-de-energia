<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

//No Cache filter for technopedia.
Route::filter('no-cache',function($route, $request, $response){
    $response->headers->set('Cache-Control','nocache, no-store, max-age=0, must-revalidate');
    $response->headers->set('Pragma','no-cache');
    $response->headers->set('Expires','Fri, 01 Jan 1990 00:00:00 GMT');
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

//GOod-
Route::filter('user', function() {
    $user= Auth::user()->get();
    if (!$user || !$user->category) {
        return "Please Log in ";
    }
});

Route::filter('god', function() {
    $user= Auth::user()->get();
    if (!($user->god && $user->category=='god')) {
        return "Not allowed. ".$user->username." ".$user->category ;
    }
    $total=User::all()->sum('le');
    $facGI = Config::get('game.facGI');
    if($user->le < $facGI* $total)return "Low LE, should transform.".$user->le." < ".$facGI* $total ;


});

Route::filter('investor', function() {
    $user= Auth::user()->get();
    if (!($user->investor && $user->category=='investor')) {
        return "Not allowed. ".$user->username." ".$user->category ;
    }
    $total=User::all()->sum('le');
    $facFI = Config::get('game.facFI');
    if($user->le < $facFI* $total)return "Low LE, should transform.";

});

Route::filter('farmer', function() {
    $user= Auth::user()->get();
    if (!($user->farmer && $user->category=='farmer')) {
        return "Not allowed. ".$user->username." ".$user->category ;
    }
    $total=User::all()->sum('le');
    $facF = Config::get('game.facF');
    if($user->le < $facF* $total)return "Low LE, should wait.";
});


/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
    if (Auth::check()) return Redirect::to('/');
});

Route::filter('auth.user', function() {
    if (Auth::user()->guest()) {
        return Redirect::guest('misc/login');
    }
});
/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
    if (Session::token() != Input::get('_token'))
    {
      throw new Illuminate\Session\TokenMismatchException;
  }
});
