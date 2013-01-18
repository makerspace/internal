<?php echo form_open('auth/forgot', array('class' => 'form-signin')); ?>

	<a href="/auth/login"><img src="/assets/img/logo.png" alt="Stockholm Makerspace" class="logo"></a>
	
	<p><strong>Forgot your password?</strong> No worries. Just enter your e-mail address below to initialize a password reset.</p>

	<br>
	
	<?php echo form_email('email', set_value('email'), 'class="input-block-level" placeholder="E-mail address" required'); ?>

	<br><br>
	
	<button class="btn btn-medium btn-primary" type="submit">Send password reset</button>
	<a href="/auth/login" class="btn btn-medium pull-right">Return to login</a>
	
<?php echo form_close(); ?>