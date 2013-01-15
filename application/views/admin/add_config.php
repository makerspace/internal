<div class="row">
	<div class="span12">
		<h2 class="pull-left">Add new config</h2>
	</div>
	<div class="span12">
		<p>Use this function to create a new config key/value. Make sure that the key is unique.</p><br>
		<?php echo form_open(); ?>
			<?php echo form_label('Config Key:', 'key'); ?>
			<?php echo form_input('key', set_value('key'), 'id="key" class="input-xlarge"'); ?><br>
			
			<?php echo form_label('Config Value:', 'value'); ?>
			<textarea name="value" rows="5" class="span6"><?php echo set_value('value'); ?></textarea><br>
			
			<?php echo form_label('Config Description:', 'desc'); ?>
			<?php echo form_input('desc', set_value('desc'), 'id="desc" class="input-xxlarge"'); ?><br>
			
			<?php echo form_submit('submit', 'Create', 'class="btn btn-large btn-primary"'); ?>
		<?php echo form_close(); ?>
	</div>
</div>