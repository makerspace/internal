<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		HttpException::class,
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
		// TODO: Log the error somewhere (Except for EntityValidationException)
		return parent::report($e);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e)
	{
		if($e instanceof \App\Models\EntityValidationException)
		{
			return Response()->json([
				"status"  => "error",
				"column"  => $e->getColumn(),
				"message" => $e->getMessage(),
			], 422);
		}
		else if($e instanceof \App\Traits\FilterNotFoundException)
		{
			return Response()->json([
				"status"  => "error",
				"column"  => $e->getColumn(),
				"data"    => $e->getData(),
				"message" => $e->getMessage(),
			], 404);
		}
		else
		{
/*
			// TODO: When we're in production we want a generic error message via the API, and not a rendered HTML page
			return Response()->json([
				"status" => "error",
				"message" => "Caught unknown exception: {$e->getMessage()}",
			], 500);
*/
			return parent::render($request, $e);
		}
	}
}