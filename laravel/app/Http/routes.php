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


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With");
//header("Access-Control-Allow-Credentials: true");

// API version 2
Route::group(["prefix" => "v1", "before" => "oauth"], function()
{
	// OAuth 2.0
	Route::post("oauth/access_token", function() {
		return Response::json(Authorizer::issueAccessToken());
	});
	// TODO: Logout?

	// Debugging
	Route::   get("debug/test",                      "V2\Debug@test");
	Route::   get("debug/updateinstructionnumbers",  "V2\Debug@UpdateInstructionNumbers");
	Route::   get("debug/unbalanced",                "V2\Debug@Unbalanced");
	Route::   get("debug/cleardatabase",             "V2\Debug@ClearDatabase");

	// Members
	Route::   get("member",        "V2\Member@list");   // Get collection
	Route::  post("member",        "V2\Member@create"); // Model: Create
	Route::   get("member/{id}",   "V2\Member@read");   // Model: Read
	Route::   put("member/{id}",   "V2\Member@update"); // Model: Update
	Route::delete("member/{id}",   "V2\Member@delete"); // Model: Delete

	// Groups
	Route::   get("group",      "V2\Group@list");   // Get collection
	Route::  post("group",      "V2\Group@create"); // Model: Create
	Route::   get("group/{id}", "V2\Group@read");   // Model: Read
	Route::   put("group/{id}", "V2\Group@update"); // Model: Update
	Route::delete("group/{id}", "V2\Group@delete"); // Model: Delete

	// Products
	Route::   get("product",      "V2\Product@list");   // Get collection
	Route::  post("product",      "V2\Product@create"); // Model: Create
	Route::   get("product/{id}", "V2\Product@read");   // Model: Read
	Route::   put("product/{id}", "V2\Product@update"); // Model: Update
	Route::delete("product/{id}", "V2\Product@delete"); // Model: Delete

	// Mail
	Route::   get("mail",      "V2\Mail@list");   // Get collection
	Route::  post("mail",      "V2\Mail@create"); // Model: Create
	Route::   get("mail/{id}", "V2\Mail@read");   // Model: Read
	Route::   put("mail/{id}", "V2\Mail@update"); // Model: Update
	Route::delete("mail/{id}", "V2\Mail@delete"); // Model: Delete
	Route::  post("mail/send", "V2\Mail@send");   // Add E-mail to send queue

	// Subscriptions
	Route::   get("subscription",      "V2\Subscription@list");   // Get collection
	Route::  post("subscription",      "V2\Subscription@create"); // Model: Create
	Route::   get("subscription/{id}", "V2\Subscription@read");   // Model: Read
	Route::   put("subscription/{id}", "V2\Subscription@update"); // Model: Update
	Route::delete("subscription/{id}", "V2\Subscription@delete"); // Model: Delete

	// RFID keys
	Route::   get("rfid",      "V2\Rfid@list");   // Get collection
	Route::  post("rfid",      "V2\Rfid@create"); // Model: Create
	Route::   get("rfid/{id}", "V2\Rfid@read");   // Model: Read
	Route::   put("rfid/{id}", "V2\Rfid@update"); // Model: Update
	Route::delete("rfid/{id}", "V2\Rfid@delete"); // Model: Delete

	// Sales
	Route::group(array("prefix" => "sales"), function()
	{
		// History
		Route::   get("history",      "V2\Sales@list");   // Get collection
		Route::  post("history",      "V2\Sales@create"); // Model: Create
		Route::   get("history/{id}", "V2\Sales@read");   // Model: Read
		Route::   put("history/{id}", "V2\Sales@update"); // Model: Update
		Route::delete("history/{id}", "V2\Sales@delete"); // Model: Delete
	});

	// Import
	Route::group(array("prefix" => "import"), function()
	{
		Route::   get("seb",       "V2\Economy@importSeb");
		Route::   get("tictail",   "V2\ImportTictail@import");
	});

	// Economy
	Route::group(array("prefix" => "economy/{accountingperiod}"), function()
	{
		// Invoices
		Route::   get("invoice",             "V2\InvoiceController@list");   // Get collection
		Route::  post("invoice",             "V2\InvoiceController@create"); // Model: Create
		Route::   get("invoice/{id}",        "V2\InvoiceController@read");   // Model: Read
		Route::   put("invoice/{id}",        "V2\InvoiceController@update"); // Model: Update
		Route::delete("invoice/{id}",        "V2\InvoiceController@delete"); // Model: Delete
		Route::   get("invoice/{id}/export", "V2\InvoiceController@export"); // Export *.odt
	});

	// Relations
	// History
//	Route::   get("relation",      "V2\Sales@list");   // Get collection
	Route::  post("relation",      "V2\Relation@create"); // Create relation
//	Route::   get("relation/{id}", "V2\Sales@read");   // Model: Read
//	Route::   put("relation/{id}", "V2\Sales@update"); // Model: Update
	Route::delete("relation/{id}", "V2\Relation@delete"); // Delete relation
});

// Everything else should give an error
Route::get("/{wildcard}", function ()
{
	return Response()->json([
		"status"  => "error",
		"message" => "Unknown API request",
	], 400);
})->where("wildcard", ".*");