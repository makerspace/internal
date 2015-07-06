<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get("/", function ()
{
    return view("app");
});

// TODO: All URL's except those below should be routed to the index page

Route::group(array("prefix" => "api/v2"), function()
{
	Route::resource("member",  "V2\Member@get");
	
	Route::group(array("prefix" => "accounting"), function()
	{
		Route::resource("import",       "V2\Economy@importSeb");
		Route::resource("instructions", "V2\Economy@getInstructions");
	});

	Route::resource("labaccess", "V2\Labaccess@get");
});