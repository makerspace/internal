<div class="container">
	
	<?php echo form_open('auth/login', array('class' => 'form-signin')); ?>
		<h3>Stockholm Makerspace</h3>
		
		<p>This area is restricted for members only. Visit our frontpage at <a href="http://www.makerspace.se">www.makerspace.se</a> to register.</p>
		
		<?php echo form_email('email', set_value('email'), 'class="input-block-level" placeholder="E-mail address"'); ?>
		<?php echo form_password('password', '', 'class="input-block-level" placeholder="Enter password"'); ?>
		<label class="checkbox">
          <?php echo form_checkbox('remember', '1', false);?> Remember me
        </label>
		
		<button class="btn btn-medium btn-primary" type="submit">Sign in</button>
		<a href="/auth/forgot_password" class="btn btn-medium pull-right">Forgot your password?</button>
		
	<?php echo form_close(); ?>

</div>
