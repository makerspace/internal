<?php echo form_open('', 'class="form-signin" autocomplete="off"'); ?>

	<a href="/auth/login"><img src="/assets/img/logo.png" alt="Stockholm Makerspace" class="logo"></a>
	
	<p>To finialize your password reset, please enter a <strong>new password</strong> in both the fields below.</p>

	<br>
	<label for="new_password">New password</label>
	<?php echo form_password('password', '', 'class="input-block-level" required pattern=".{8,}" title="Minimum 8 characters" autocompletion="off"'); ?>
	
	<br>
	<label for="new_password">Repeat new password</label>
	<?php echo form_password('password2', '', 'class="input-block-level" required pattern=".{8,}" title="Minimum 8 characters" autocompletion="off"'); ?>

	<br><br>
	
	<button class="btn btn-medium btn-primary" type="submit">Reset password</button>
	<a href="/auth/login" class="btn btn-medium pull-right">Return to login</a>
	
<?php echo form_close(); ?>