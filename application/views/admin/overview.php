<div class="row">
	<div class="span12">
		<h2 class="pull-left">System Configuration</h2>
		<a href="/admin/add_config" class="btn btn-primary pull-right">Add new config</a>
	</div>
	<div class="span12">
		<?php foreach($this->dbconfig as $key => $value) { ?>
		
		<?php
		if(is_object($value) || is_array($value)) { 
			$value = json_encode($value);
		} else {
			$value = htmlspecialchars($value);
		}
		?>
		
		<?php echo form_open(); ?>
			<?php echo form_hidden('key', $key); ?>
			<label>Config key: <strong><?php echo $key; ?></strong></label>
			<textarea name="value" rows="5" class="span6"><?php echo $value; ?></textarea>
			<?php echo form_submit('submit', 'Update'); ?>
		<?php echo form_close(); ?>

		<?php } ?>
	</div>
</div>