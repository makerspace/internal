<h2>Manage Newsletters</h2>
<p>To create and send a new newsletter, select member groups below (based upon their access levels) to send a newsletter to.
<br>The next few steps will then guide you trough the process of creating and sending a new newsletter.</p>
<p>If you want to view statistics about a previously sent newsletter, modify or resend a previously created one - Just click on the newsletter title below.</p>


<h3>Create a new newsletter</h3>
<p>Select recipients access levels below. Leave empty to send to all members.<br>
Please note that you can't change the recipients or recipient groups after you've created a newsletter!</p>
<?php echo form_open('/newsletter/create'); ?>
	<?php echo form_multiselect('groups[]', $this->dbconfig->acl, '', 'class="span4" size="8"'); // $this->dbconfig->acl ?><br>
	<?php echo form_submit('continue', 'Continue to next step', 'class="btn btn-large btn-primary"'); ?>
<?php echo form_close(); ?>
<br>

<h3>Previous newsletter</h3>
<p>Use the Manage button to the right to Edit, View and Send stored newsletters.</p>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Title</th>
			<th>Owner</th>
			<th>Created</th>
			<th>Last Updated</th>
			<th>Status</th>
			<th>Sent <small>(Date/Time)</small></th>
			<th>Recipients</th>
			<th>Bounces</th>
			<th>Manage</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($newsletters as $nl) { ?>
		<tr>
			<td><?php echo anchor('newsletter/view/'.$nl->id, htmlspecialchars($nl->subject)); ?></td>
			<td><?php $member = $this->Member_model->get_member($nl->created_by); echo '<a href="/members/view/'.$member->id.'">'.$member->firstname.' '.$member->lastname.'</a>'; ?></td>
			<td><?php echo when($nl->created); ?></td>
			<td><?php echo ($nl->created == $nl->last_updated ? 'Never' : when($nl->last_updated)); ?></td>
			<td><?php echo ($nl->sent ? '<span class="label label-success">Sent</span>' : '<span class="label">Unsent</label>'); ?></td>
			<td><?php echo (!empty($nl->sent_timestamp) ? when($nl->sent_timestamp) : '<span class="label">Unsent</label>'); ?></td>
			<td><?php echo count(json_decode($nl->recipients)); ?></td>
			<td><?php echo $nl->bounces; ?></td>
			<td>
				<?php echo anchor('newsletter/edit/'.$nl->id, 'Edit', 'class="btn btn-mini"'); ?>
				<?php echo anchor('newsletter/view/'.$nl->id, 'View and Send', 'class="btn btn-primary btn-mini"'); ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>