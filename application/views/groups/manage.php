<h2>List of Groups</h2>

<table class="table table-striped table-condensed table-bordered tablesorter">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name <!--<small>(Click to edit)</small>--></th>
			<th>Description</th>
			<th class="{sorter: false}">Access Levels (ACL)</th>
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
			<td><small>Not implemented (yet)</small></td>
			<td><?php echo $this->Group_model->member_count($group->id); ?></td>
			<td><?php 
				echo form_open('/members/search', 'class="nomargin"'); 
				echo form_hidden('search', 'group:'.$group->name);
				echo form_submit('submit', 'View Members', 'class="btn btn-small btn-primary"');
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