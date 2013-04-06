<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo (!empty($title) ? $title . ' | ' : ''); ?>Internal.makerspace.se</title>
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
	<?php if(is_loggedin()) $this->load->view('navbar'); ?>
	<div class="container<?php echo (!empty($fullscreen) ? '-fluid' : ''); ?> <?php echo $this->router->fetch_class(); ?>">
		<?php echo get_flashdata(); ?>
		<?php echo get_errors(); ?>
