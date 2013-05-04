<h2>List of Groups</h2>

<table class="table table-striped table-condensed table-bordered tablesorter">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name <!--<small>(Click to edit)</small>--></th>
			<th>Description</th>
			<!--<th class="{sorter: false}">Access Levels (ACL)</th>-->
			<th>Members</th>
			<th class="{sorter: false}">View</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($groups as $group) { ?>
		<tr>
			<td><?php echo $group->id; ?></td>
			<!--<td><a href="/groups/edit/<?php echo $group->id; ?>"><?php echo $group->name; ?></a></td>-->
			<td><?php echo $group->name; ?></td>
			<td><?php echo $group->description; ?></td>
			<!--<td><small>Not implemented (yet)</small></td>-->
			<td><?php echo $this->Group_model->member_count($group->id); ?></td>
			<td><?php 
				echo form_open('/members/search', 'class="nomargin"'); 
				echo form_hidden('search', 'group:'.$group->name);
				echo form_submit('submit', 'View Members', 'class="btn btn-small btn-primary"');
			?>
			<!--<a href="/groups/edit/<?php echo $group->id; ?>" class="btn btn-small btn-mini">Edit</a>-->
			<?php
				echo form_close();
			?>
			</td>
		</tr>
	<?php } if(empty($groups)) { ?>
		<tr>
			<td colspan="13">Sorry, couldn't find any groups in the database.</td>
		</tr>
	<?php } ?>
	</tbody>
</table>

<h2>Add new group</h2>
<p>Fill in the fields below to add a new group to the system.</p>
<?php echo form_open('groups/add_group'); ?>
	
	<h4>Group Name <small>(Must be unique, use a lowercase, machine-readable name)</small></h4>
	<?php echo form_input('name', set_value('name'), 'class="input-xlarge" required'); ?>
	
	<h4>Group Description <small>(A brief description of the group)</small></h4>
	<?php echo form_input('description', set_value('name'), 'class="input-xlarge" required'); ?>
	<br>
	<?php echo form_submit('add', 'Add Group', 'class="btn btn-primary"'); ?>
	
<?php echo form_close(); ?>