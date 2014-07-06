<?php
Route::group(['before' => 'auth.hootsuite'], function ()
{
    Route::get('/session/store', 'SessionController@store');
    Route::resource('session', 'SessionController');
});

Route::group(['before' => ['auth.hootsuite', 'auth.reddit']], function()
{
    Route::post('/', 'StreamController@show');
    Route::get('/', 'StreamController@show');
});

Route::get('my_receiver.html', function() { return View::make('receiver'); });
Route::controller('ajax', 'AjaxController');

Route::any('imgproxy', function()
{
    $hash = Input::get('hash');
    $expectedHash = md5(URL::to('imgproxy') . '?url=' . Input::get('url') . Config::get('auth.hash_secret'));
    if ($hash == $expectedHash) {
        return Response::make(file_get_contents(Input::get('url')), 200, array("Content-Type" => "image/jpeg"));
    } else {
        return Response::make('', 403);
    }
});



