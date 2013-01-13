<?php echo form_open('', array('class' => 'form-signin')); ?>

	<h3>Stockholm Makerspace</h3>
	
	<p>To finialize the password reset, please fill in your <strong>new password</strong> twice in the fields below.</p>

	<br>
	<label for="new_password">New password</label>
	<?php echo form_password('password', '', 'class="input-block-level" required pattern=".{8,}" title="Minimum 8 characters"'); ?>
	
	<br>
	<label for="new_password">Repeat new password</label>
	<?php echo form_password('password2', '', 'class="input-block-level" required pattern=".{8,}" title="Minimum 8 characters"'); ?>

	<br><br>
	
	<button class="btn btn-medium btn-primary" type="submit">Reset password</button>
	<a href="/auth/login" class="btn btn-medium pull-right">Return to login</a>
	
<?php echo form_close(); ?>