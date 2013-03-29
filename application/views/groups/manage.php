<h2>List of Groups</h2>

<table class="table table-striped table-condensed table-bordered">
	<thead>
		<tr>
			<th>Name <small>(Click to edit)</small></th>
			<th>Group ID</th>
			<th>Description</th>
			<th>Access Levels (ACL)</th>
			<th>Members</th>
			<th>View</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($groups as $group) { ?>
		<tr>
			<td><a href="/groups/edit/<?php echo $group->id; ?>"><?php echo $group->name; ?></a></td>
			<td><?php echo $group->id; ?></td>
			<td><?php echo $group->description; ?></td>
			<td>Not implemented</td>
			<td><?php echo $this->Group_model->member_count($group->id); ?></td>
			<td><?php 
				echo form_open('/members/search', 'class="nomargin"'); 
				echo form_hidden('search', 'group:'.$group->name);
				echo form_submit('submit', 'View Members', 'class="btn btn-mini btn-primary"');
				echo form_close();
			?></td>
		</tr>
	<?php } if(empty($groups)) { ?>
		<tr>
			<td colspan="13">Sorry, couldn't find any groups in the database.</td>
		</tr>
	<?php } ?>
	</tbody>
</table>

<h2>Create new group</h2>
<strong>
ToDo: Add "Create Group" form here.
</strong>