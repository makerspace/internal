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


header('Access-Control-Allow-Origin: *');
//header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
//header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
//header('Access-Control-Allow-Credentials: true');

// API version 2
Route::group(array("prefix" => "api/v2"), function()
{
	Route::   get("test",  "V2\Economy@test");  // Get model

	// Members
	Route::   get("member",      "V2\Member@getAll"); // Get collection
	Route::  post("member",      "V2\Member@create"); // Model: Create
	Route::   get("member/{id}", "V2\Member@read");   // Model: Read
	Route::   put("member/{id}", "V2\Member@update"); // Model: Update
	Route::delete("member/{id}", "V2\Member@delete"); // Model: Delete

	// Subscriptions
	Route::   get("subscription",      "V2\Subscription@getAll"); // Get collection
	Route::  post("subscription",      "V2\Subscription@create"); // Model: Create
	Route::   get("subscription/{id}", "V2\Subscription@read");   // Model: Read
	Route::   put("subscription/{id}", "V2\Subscription@update"); // Model: Update
	Route::delete("subscription/{id}", "V2\Subscription@delete"); // Model: Delete

	// Import
	Route::group(array("prefix" => "import"), function()
	{
		Route::   get("seb",       "V2\Economy@importSeb");
		Route::   get("tictail",   "V2\ImportTictail@import");
	});

	// Economy
	Route::group(array("prefix" => "economy/{accountingperiod}"), function()
	{
		// Transactions
		Route::   get("transaction",      "V2\EconomyTransactions@list"); // Get collection
		Route::  post("transaction",      "V2\EconomyTransactions@create"); // Model: Create
		Route::   get("transaction/{id}", "V2\EconomyTransactions@read");   // Model: Read
		Route::   put("transaction/{id}", "V2\EconomyTransactions@update"); // Model: Update
		Route::delete("transaction/{id}", "V2\EconomyTransactions@delete"); // Model: Delete

		// Instructions
		Route::   get("instruction",      "V2\EconomyInstruction@list");     // Get collection
		Route::  post("instruction",      "V2\EconomyInstruction@create");   // Model: Create
		Route::   get("instruction/{id}", "V2\EconomyInstruction@read");     // Model: Read
		Route::   put("instruction/{id}", "V2\EconomyInstruction@update");   // Model: Update
		Route::delete("instruction/{id}", "V2\EconomyInstruction@delete");   // Model: Delete

		// Accounts
		Route::   get("masterledger",     "V2\EconomyAccount@masterledger"); // Get collection
		Route::   get("account",          "V2\EconomyAccount@list");         // Get collection
		Route::  post("account",          "V2\EconomyAccount@create");       // Model: Create
		Route::   get("account/{id}",     "V2\EconomyAccount@read");         // Model: Read
		Route::   put("account/{id}",     "V2\EconomyAccount@update");       // Model: Update
		Route::delete("account/{id}",     "V2\EconomyAccount@delete");       // Model: Delete

		// Invoices
		Route::   get("invoice",             "V2\InvoiceController@list");   // Get collection
		Route::  post("invoice",             "V2\InvoiceController@create"); // Model: Create
		Route::   get("invoice/{id}",        "V2\InvoiceController@read");   // Model: Read
		Route::   put("invoice/{id}",        "V2\InvoiceController@update"); // Model: Update
		Route::delete("invoice/{id}",        "V2\InvoiceController@delete"); // Model: Delete
		Route::   get("invoice/{id}/export", "V2\InvoiceController@export"); // Export *.odt

		// Cost centers
		Route::   get("costcenter",       "V2\EconomyCostcenter@list");   // Get collection
		Route::  post("costcenter",       "V2\EconomyCostcenter@create"); // Model: Create
		Route::   get("costcenter/{id}",  "V2\EconomyCostcenter@read");   // Model: Read
		Route::   put("costcenter/{id}",  "V2\EconomyCostcenter@update"); // Model: Update
		Route::delete("costcenter/{id}",  "V2\EconomyCostcenter@delete"); // Model: Delete
	});
});

// Everything else should load the single page app
Route::get("/{wildcard}", function ()
{
    return view("app");
})->where("wildcard", ".*");
