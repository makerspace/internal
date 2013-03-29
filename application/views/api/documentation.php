<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>API Documentation | internal.makerspace.se</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Stockholm Makerspace">

	<link href="/assets/css/bootstrap.min.css" rel="stylesheet">
	<style>.logo {margin: 40px 60px;}</style>
	
	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>
<div class="container">

<div class="row">

	<div class="span10" style="text-align: center">	
		<a href="/"><img src="/assets/img/logo.png" alt="Stockholm Makerspace" class="logo"></a>
	</div>
	
	<div class="span6">
		
		<h1>API Documentation</h1>
		<p>REST API for Internal - Version 0.2</p>

		<h3>Information</h3>
		<p>Resources response-data is returned as JSON.<br>
		Standard HTTP Status Codes are used for all responses:</p>
		<ul>
			<li>200 = OK</li>
			<li>201 = Resource created</li>
			<li>400 = Bad Request</li>
			<li>403 = Forbidden</li>
			<li>404 = Not found</li>
			<li>405 = Method Not Allowed</li>
			<li>501 = Not Implemented</li>
			<li>503 = Service Unavailable</li>
		</ul>

	</div>
	
	<div class="span6">

		<h3>Public resources</h3>
		<ul>
			<!--<li>GET /api/newsletter/*id* - Get newsletter by id</li>-->
			<li>GET /api/coffee</li>
		</ul>

		<h3>Protected resources (Requires authentication)</h3>
		<p>Use the HTTP Headers <strong>X-Username</strong> (your e-mail address) and <strong>X-Password</strong> to authenticate.<br>
		<em>Note: These resources also requires your account to be in the the "API" group.</em></p>

		<ul>
			<li>POST /api/auth - Try authentication for member (email, password)</li>
			<br>
			<li>GET /api/get_member/*uid* - Get member by id</li>
			<li>GET /api/get_member/*key*/*value* - Get member where key=value</li>
			<li>GET /api/get_member_groups/*uid* - Get groups for member id</li>
			<br>
			<li>POST /api/add_member - Add a new member</li>
			<li>POST /api/update_member/*uid* - Update member by id</li>
			<br>
			<li>GET /api/get_groups - Get all groups</li>
			<li>GET /api/get_group_members/*gid* - Get members in group by group id</li>
			
		</ul>
		
	</div>
</div>
</div>
</body>
</html>