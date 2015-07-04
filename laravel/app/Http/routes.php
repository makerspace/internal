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

Route::group(array("prefix" => "api/v2"), function()
{
	Route::resource("member",  "V2\Member@get");
	Route::resource("economy", "V2\Economy@get");
});