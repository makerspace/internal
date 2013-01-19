<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo $heading; ?> | Internal.makerspace.se</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Stockholm Makerspace">

	<link href="/assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="/assets/css/makerspace.css" rel="stylesheet">

	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>
	<div class="container-fluid error-container">
		
		<a href="/auth/login"><img src="/assets/img/logo.png" alt="Stockholm Makerspace" class="logo"></a>
		
		<div class="well">
		
			<h1><?php echo $heading; ?></h1>
			<big><?php echo $message; ?></big>
		
		</div>
		
	</div>
</body>
