<h2>Export members</h2>

<div class="row">

	<div class="span12">
		<p>Below you can select individual member groups and types of data to export. If a member exists in two groups, only one records will be in the export.</p>
		<p><big>If you don't select a group, all members will be exported.</big></p>
	</div>
	
	<?php echo form_open(); ?>
	
		<div class="span6">
			<legend>Only export these groups <small>(Select <a href="#" class="checkall" data-selector="groups">All</a> / <a href="#" class="checknone" data-selector="groups">None</a>)</legend>
			
			<?php foreach($groups as $group) { ?>
				<label class="span2"><?php echo form_checkbox('groups[]', $group->id, false) . ' ' . $group->description; ?></label>
			<?php } ?>
		</div>
		
		<div class="span6">
			<legend>Fields to include in the export <small>(Select <a href="#" class="checkall" data-selector="fields">All</a> / <a href="#" class="checknone" data-selector="fields">None</a>)</legend>
			
			<?php foreach($export_fields as $field) { ?>
				<label class="span2"><?php echo form_checkbox('fields[]', $field, true) . ' <span class="muted">Field:</span> ' . $field; ?></label>
			<?php } ?>
		</div>
		
		<br>
			
		<div class="span12">
			<legend>Select export filetype</legend>
				<?php echo form_submit('export[csv]', 'Download as CSV', 'class="btn btn-large"'); ?> &nbsp; 
				<?php echo form_submit('export[json]', 'Download as JSON', 'class="btn btn-large"'); ?> &nbsp; 
				<?php echo form_submit('export[xml]', 'Download as XML', 'class="btn btn-large"'); ?>
		</div>
			
	</div>
	<?php echo form_close(); ?>
	
</div>