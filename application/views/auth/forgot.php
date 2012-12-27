<?php echo form_open('auth/forgot', array('class' => 'form-signin')); ?>
	<h3>Stockholm Makerspace</h3>
	
	<p>Forgot your password? Just enter your e-mail below and further instructions will be sent to you shortly.</p>

	<br>
	
	<?php echo form_email('email', set_value('email'), 'class="input-block-level" placeholder="E-mail address" required'); ?>

	<br><br>
	
	<button class="btn btn-medium btn-primary" type="submit">Reset password</button>
	<a href="/auth/login" class="btn btn-medium pull-right">Return to login</a>
	
<?php echo form_close(); ?>