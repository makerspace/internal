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
	Route::   get("member",      "V2\Member@list");   // Get collection
	Route::  post("member/{id}", "V2\Member@create"); // Model: Create
	Route::   get("member/{id}", "V2\Member@read");   // Model: Read
	Route::   put("member/{id}", "V2\Member@update"); // Model: Update
	Route::delete("member/{id}", "V2\Member@delete"); // Model: Delete

	// Economy
	Route::group(array("prefix" => "economy"), function()
	{
		// Import / export
		Route::   get("import",       "V2\Economy@importSeb");

		// Transactions
		Route::   get("transaction",      "V2\Economy@transactionList");   // Get collection
		Route::  post("transaction/{id}", "V2\Economy@transactionCreate"); // Model: Create
		Route::   get("transaction/{id}", "V2\Economy@transactionRead");   // Model: Read
		Route::   put("transaction/{id}", "V2\Economy@transactionUpdate"); // Model: Update
		Route::delete("transaction/{id}", "V2\Economy@transactionDelete"); // Model: Delete

		// Instructions
		Route::   get("instruction",      "V2\Economy@instructionList"); // Get collection
		Route::  post("instruction/{id}", "V2\Economy@instructionRead"); // Model: Create
		Route::   get("instruction/{id}", "V2\Economy@instructionRead"); // Model: Read
		Route::   put("instruction/{id}", "V2\Economy@instructionRead"); // Model: Update
		Route::delete("instruction/{id}", "V2\Economy@instructionRead"); // Model: Delete

		// Accounts
		Route::   get("account",          "V2\Economy@accountList");   // Get collection
		Route::  post("account",          "V2\Economy@accountCreate"); // Model: Create
		Route::   get("account/{id}",     "V2\Economy@accountRead");   // Model: Read
		Route::   put("account/{id}",     "V2\Economy@accountUpdate"); // Model: Update
		Route::delete("account/{id}",     "V2\Economy@accountDelete"); // Model: Delete

		// Invoices
		Route::   get("invoice",          "V2\InvoiceController@invoiceList");   // Get collection
		Route::  post("invoice/{id}",     "V2\InvoiceController@invoiceCreate"); // Model: Create
		Route::   get("invoice/{id}",     "V2\InvoiceController@invoiceRead");   // Model: Read
		Route::   put("invoice/{id}",     "V2\InvoiceController@invoiceUpdate"); // Model: Update
		Route::delete("invoice/{id}",     "V2\InvoiceController@invoiceDelete"); // Model: Delete
		Route::   get("invoice/export",   "V2\InvoiceController@exportInvoice"); // Export *.odt

		// Cost centers
		Route::   get("costcenter",       "V2\Economy@getCostcenterList");       // Get collection
		Route::  post("costcenter/{id}",  "V2\Economy@getCostcenterCreate");     // Model: Create
		Route::   get("costcenter/{id}",  "V2\Economy@getCostcenterRead");       // Model: Read
		Route::   put("costcenter/{id}",  "V2\Economy@getCostcenterUpdate");     // Model: Update
		Route::delete("costcenter/{id}",  "V2\Economy@getCostcenterDelete");     // Model: Delete
	});

	// Subscriptions
	Route::   get("subscription", "V2\Subscription@get");    // Get collection
	Route::  post("subscription", "V2\Subscription@create"); // Model: Create
	Route::   get("subscription", "V2\Subscription@read");   // Model: Read
	Route::   put("subscription", "V2\Subscription@update"); // Model: Update
	Route::delete("subscription", "V2\Subscription@delete"); // Model: Delete
});

// Everything else should load the single page app
Route::get("/{wildcard}", function ()
{
    return view("app");
})->where("wildcard", ".*");
