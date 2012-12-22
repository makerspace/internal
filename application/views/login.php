<div class="container">

	
	<?php echo form_open('auth/login', array('class' => 'form-signin')); ?>
		<h3>Stockholm Makerspace</h3>
		
		<input type="text" class="input-block-level" placeholder="E-postadress">
		<input type="password" class="input-block-level" placeholder="Ange lösenord">
		
		<button class="btn btn-medium btn-primary" type="submit">Logga in</button>
		
	<?php echo form_close(); ?>

</div>
