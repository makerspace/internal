<div class="container">

	<?php echo form_open('auth/login', array('class' => 'form-signin')); ?>
		<h2 class="form-signin-heading">Please sign in</h2>
		
		<input type="text" class="input-block-level" placeholder="Email address">
		<input type="password" class="input-block-level" placeholder="Password">
		
		<button class="btn btn-large btn-primary" type="submit">Sign in</button>
	<?php echo form_close(); ?>

</div>
