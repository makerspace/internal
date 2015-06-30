<div class="row">
	<div class="span12">
		<h2 class="pull-left">System Configuration</h2>
		<a href="/admin/add_config" class="btn btn-primary pull-right">Add new config</a>
	</div>
	<div class="span12">
		<?php foreach($dbconfig as $item) { ?>
		
		<?php
		if(is_object($item->value) || is_array($item->value)) { 
			$value = json_encode($item->value);
		} else {
			$value = form_prep($item->value);
		}
		?>
		
		<?php echo form_open(); ?>
			<?php echo form_hidden('key', $item->key); ?>
			<label>Key: <strong><?php echo $item->key; ?></strong> - <?php echo $item->desc; ?></label>
			<textarea name="value" rows="5" class="span6"><?php echo $value; ?></textarea>
			<?php echo form_submit('submit', 'Update', 'class="btn"'); ?>
		<?php echo form_close(); ?>

		<?php } ?>
	</div>
</div>