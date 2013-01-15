<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>API Documentation |internal.makerspace.se</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Stockholm Makerspace">

	<link href="/assets/css/bootstrap.min.css" rel="stylesheet">
	<style>.container-narrow {margin: 40px auto;max-width: 600px;}</style>
	
	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>
<div class="container-narrow">
	<div class="row-fluid">
	
		<h1>API Documentation</h1>
		<p>REST API for Internal - Version 0.1.</p>

		<h3>Information</h3>
		<p>Resources response-data is returned as JSON.<br>
		Standard HTTP Status Codes are used for all responses:</p>
		<ul>
			<li>200 = OK</li>
			<li>201 = Resource created</li>
			<li>401 = Unauthorized</li>
			<li>404 = Not found</li>
			<li>405 = Method Not Allowed</li>
			<li>501 = Not Implemented</li>
			<li>503 = Service Unavailable</li>
		</ul>


		<h3>Public resources</h3>
		<ul>
			<li>POST /api/member - Add a new member</li>
			<li>GET /api/newsletter/*id* - Get newsletter by id</li>
			<li>GET /api/coffee</li>
		</ul>


		<h3>Protected resources (Requires authentication)</h3>
		<p>Use the HTTP Headers <strong>X-Username</strong> and <strong>X-Password</strong> to authenticate.<br>
		<em>Note: These resources might also require the "API" ACL on your account.</em></p>

		<ul>
			<li>GET /api/member/*id* - Get member by id</li>
			<li>GET /api/member/*key*/*value* - Get member by key</li>
			<li>POST /api/member/*id* - Update member with id</li>
		</ul>
		
	</div>
</div>
</body>
</html>