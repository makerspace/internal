<h2>Manage Newsletters</h2>
<p>To create and send a new newsletter, select member groups below (based upon their access levels) to send a newsletter to. <br>The next few steps will then guide you trough the process of creating and sending a new newsletter.</p>
<p>If you want to view statistics about a previously sent newsletter, modify or resend a previously created one - Just click on the newsletter title below.</p>

<br>
<h3>Create a new newsletter</h3>
<p>Select recipients access levels below. Leave empty to send to all members.</p>
<?php echo form_open('/newsletter/new'); ?>
	<?php echo form_multiselect('access_levels', $this->dbconfig->acl, '', 'class="span4" size="8"'); ?><br>
	<?php echo form_submit('continue', 'Continue to next step', 'class="btn btn-primary"'); ?>
<?php echo form_close(); ?>

<br>
<h3>Previous newsletter</h3>
<table class="table">
	<thead>
		<tr>
			<th>Title</th>
			<th>Created</th>
			<th>Status</th>
			<th>Sent <small>(Date/Time)</small></th>
			<th>Recipients</th>
			<th>Bounces</th>
			<th>Manage</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td> ToDo: Nothing here yet.</td>
		</tr>
	</tbody>
</table>