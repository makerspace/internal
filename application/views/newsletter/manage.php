<h2>Manage Newsletters</h2>
<p>To create and send a new newsletter, select member groups below (based upon their access levels) to send a newsletter to.
<br>The next few steps will then guide you trough the process of creating and sending a new newsletter.</p>
<p>If you want to view statistics about a previously sent newsletter, modify or resend a previously created one - Just click on the newsletter title below.</p>


<h3>Create a new newsletter</h3>
<p>Select recipients access levels below. Leave empty to send to all members.<br>
Please note that you can't change the recipients or recipient groups after you've created a newsletter!</p>
<?php echo form_open('/newsletter/create'); ?>
	<?php echo form_multiselect('groups[]', $groups, '', 'class="span4" size="12"'); ?><br>
	<?php echo form_submit('continue', 'Continue to next step', 'class="btn btn-large btn-primary"'); ?>
<?php echo form_close(); ?>
<br>

<h3>Previous Newsletters</h3>
<p>Use the buttons to the right to Edit, View and (Re)send previous newsletters.</p>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Title</th>
			<th>Author</th>
			<th>Created</th>
			<th>Last Updated</th>
			<th>Status</th>
			<th>Sent <small>(Timestamp)</small></th>
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
			<td><?php echo (!empty($nl->sent_timestamp) ? when($nl->sent_timestamp) : 'N/A'); ?></td>
			<td><?php echo count(json_decode($nl->recipients)); ?></td>
			<td><?php echo $nl->bounces; ?></td>
			<td>
				<?php echo anchor('newsletter/edit/'.$nl->id, 'Edit', 'class="btn btn-mini"'); ?>
				<?php echo anchor('newsletter/view/'.$nl->id, 'View and Send', 'class="btn btn-primary btn-mini"'); ?>
				<?php echo anchor('newsletter/delete/'.$nl->id, 'Delete', 'class="btn btn-inverse btn-mini"'); ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>