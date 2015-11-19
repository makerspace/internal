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

	Route::   get("members/get",  "V2\Member@getMember");  // Get collection
	Route::   get("members/list", "V2\Member@listMembers"); // Get model

	Route::group(array("prefix" => "economy"), function()
	{
		Route::   get("import",       "V2\Economy@importSeb");

		// Collection: transaction
		Route::   get("transaction",       "V2\Economy@getTransactions"); // Get collection
		// Model: transaction
		Route::   get("transaction/{id}",  "V2\Economy@getTransaction");  // Get model

		// Collection: instruction
		Route::   get("instruction",       "V2\Economy@getInstructions"); // Get collection
		// Model: instruction
		Route::   get("instruction/{id}",  "V2\Economy@getInstruction");  // Get model

		// Collection: account
		Route::   get("account",           "V2\Economy@getAccounts");     // Get collection
		// Model: account
		Route::  post("account",           "V2\Economy@accountCreate");   // Create
		Route::   get("account/{id}",      "V2\Economy@accountRead");     // Read
		Route::   put("account/{id}",      "V2\Economy@accountUpdate");   // Update
		Route::delete("account/{id}",      "V2\Economy@accountDelete");   // Delete

		// Collection: invoice
		Route::   get("invoice",         "V2\InvoiceController@getInvoices");     // Get collection
		// Model: invoice
		Route::   get("invoice/{id}",    "V2\InvoiceController@getInvoice");      // Get model
		Route::   get("invoice/export",  "V2\InvoiceController@exportInvoice");   // Export *.odt

		Route::   get("costcenters",    "V2\Economy@getCostCenters");     // Get collection
		Route::   get("costcenter",     "V2\Economy@getCostCenter");      // Get model

	});

	Route::   get("labaccess", "V2\Labaccess@get");
});

// Everything else should load the single page app
Route::get("/{wildcard}", function ()
{
    return view("app");
})->where("wildcard", ".*");
