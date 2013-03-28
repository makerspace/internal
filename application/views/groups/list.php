<h2 class="pull-left">List of Groups</h2>

<div class="span6 pull-right">
	<a href="/groups/add" class="btn btn-primary pull-right">Create new group</a>
</div>

<table class="table table-striped table-condensed table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Group ID</th>
			<th>Description</th>
			<th>Access Levels (ACL)</th>
			<th>Members</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($groups as $group) { ?>
		<tr>
			<td><a href="/groups/edit/<?php echo $group->id; ?>"><?php echo $group->name; ?></a></td>
			<td><?php echo $group->id; ?></td>
			<td><?php echo $group->description; ?></td>
			<td>Not implemented</td>
			<td>Not implemented</td>
		</tr>
	<?php } if(empty($groups)) { ?>
		<tr>
			<td colspan="13">Sorry, couldn't find any groups in the database.</td>
		</tr>
	<?php } ?>
	</tbody>
</table>

<a href="/groups/add" class="btn btn-primary pull-right">Create new group</a>
