<?php echo form_open('auth/login', array('class' => 'form-signin')); ?>

	<a href="/auth/login"><img src="/assets/img/logo.png" alt="Stockholm Makerspace" class="logo"></a>
	
	<p>This is a <strong>restricted area</strong> for members only. If you're not a member of Stockholm Makerspace, 
	please return to our main website at <a href="http://www.makerspace.se">www.makerspace.se</a> to sign up.</p>
	
	<br>
	<?php echo form_email('email', set_value('email'), 'class="input-block-level" placeholder="E-mail address" required'); ?>
	<?php echo form_password('password', '', 'class="input-block-level" placeholder="Your password" required pattern=".{8,}" title="Minimum 8 characters"'); ?>
	<label class="checkbox">
	  <?php echo form_checkbox('remember', '1', false);?> Remember me
	</label>
	
	<br>
	<button class="btn btn-medium btn-primary" type="submit">Sign in</button>
	<a href="/auth/forgot" class="btn btn-medium pull-right">Forgot your password?</a>
	
<?php echo form_close(); ?>