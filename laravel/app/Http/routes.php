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
Route::group(["prefix" => "api/v2", "before" => "oauth"], function()
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
	Route::   get("member",      "V2\Member@list");   // Get collection
	Route::  post("member",      "V2\Member@create"); // Model: Create
	Route::   get("member/{id}", "V2\Member@read");   // Model: Read
	Route::   put("member/{id}", "V2\Member@update"); // Model: Update
	Route::delete("member/{id}", "V2\Member@delete"); // Model: Delete

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

	// Import
	Route::group(array("prefix" => "import"), function()
	{
		Route::   get("seb",       "V2\Economy@importSeb");
		Route::   get("tictail",   "V2\ImportTictail@import");
	});

	// Economy
	Route::group(array("prefix" => "economy/{accountingperiod}"), function()
	{
		// Files / vouchers
		Route::   get("file/{external_id}/{filename}", "V2\Economy@file"); // Get collection

		// Transactions
		Route::   get("transaction",      "V2\EconomyTransactions@list");   // Get collection
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

		// Reports
		Route::   get("valuationsheet",   "V2\EconomyReport@valuationSheet"); // Get collection
		Route::   get("resultreport",     "V2\EconomyReport@resultReport");   // Get collection
	});
});

// Everything else should load the single page app
Route::get("/{wildcard}", function ()
{
    return view("app");
})->where("wildcard", ".*");
