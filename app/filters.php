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
    Log::info(Request::fullUrl());
});


App::after(function($request, $response)
{
	//
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

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
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


/**
 * Directs the user to login with reddit if their account is not active.
 */
Route::filter('auth.reddit', function()
{
   if (!Auth::user()->active) {
       return Redirect::action('SessionController@create');
   }
});

/**
 * Handles Hootsuite SSO login and logging the user in.
 */
Route::filter('auth.hootsuite', function()
{
    if (!Auth::check()) {
        if (App::environment('local')) {
            $theme = 'magnum';
        } else {
            $theme = Input::get('theme');
            if(sha1(Input::get('i') . Input::get('ts') . Config::get('hootsuite.auth_secret')) != Input::get('token')){
                Log::info('hootsuite.sso filter failed for PID: ' . Input::get('pid'));
                return Redirect::to('https://hootsuite.com/dashboard');
            }
        }

        $user = User::firstOrNew(Input::only(['pid', 'uid']));
        $user->theme = $theme;
        $user->save();
        Auth::login($user);
    }
});


/*
|--------------------------------------------------------------------------
| Event Listeners
|--------------------------------------------------------------------------
*/
Event::listen('auth.login', function($user)
{
    Log::info("Logged in PID: {$user->pid} UID: {$user->uid}");
});

Event::listen('auth.logout', function($user)
{
    Log::info("Logged out PID: {$user->pid} UID: {$user->uid}");
});


