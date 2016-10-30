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
	// Debugging
	Route::get("debug/test",                     "V2\Debug@test");
	Route::get("debug/updateinstructionnumbers", "V2\Debug@UpdateInstructionNumbers");
	Route::get("debug/unbalanced",               "V2\Debug@Unbalanced");
	Route::get("debug/cleardatabase",            "V2\Debug@ClearDatabase");

	// Mail
	Route::   get("mail",      "V2\Mail@list");   // Get collection
	Route::  post("mail",      "V2\Mail@create"); // Model: Create
	Route::   get("mail/{id}", "V2\Mail@read");   // Model: Read
	Route::   put("mail/{id}", "V2\Mail@update"); // Model: Update
	Route::delete("mail/{id}", "V2\Mail@delete"); // Model: Delete
	Route::  post("mail/send", "V2\Mail@send");   // Add E-mail to send queue

	// RFID keys
	Route::   get("rfid",      "V2\Rfid@list");   // Get collection
	Route::  post("rfid",      "V2\Rfid@create"); // Model: Create
	Route::   get("rfid/{id}", "V2\Rfid@read");   // Model: Read
	Route::   put("rfid/{id}", "V2\Rfid@update"); // Model: Update
	Route::delete("rfid/{id}", "V2\Rfid@delete"); // Model: Delete

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