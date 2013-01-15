<?php echo form_open('auth/forgot', array('class' => 'form-signin')); ?>

	<img src="/assets/img/logo.png" alt="Stockholm Makerspace">
	
	<p>Forgot your password? Just enter your e-mail below and further instructions will be sent to you shortly.</p>

	<br>
	
	<?php echo form_email('email', set_value('email'), 'class="input-block-level" placeholder="E-mail address" required'); ?>

	<br><br>
	
	<button class="btn btn-medium btn-primary" type="submit">Send password reminder</button>
	<a href="/auth/login" class="btn btn-medium pull-right">Return to login</a>
	
<?php echo form_close(); ?>