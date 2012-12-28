<h2 class="pull-left">List of members</h2>

<a href="/members/add" class="btn btn-primary pull-right">Add new member</a>

<table class="table table-striped table-condensed table-bordered">
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th>#</th>
			<th>Name</th>
			<th>E-mail</th>
			<th>Phone</th>
			<th>Mobile</th>
			<th>Registered</th>
			<th>Membership Due</th>
			<th>Active</th>
			<th>Lab Access</th>
			<th>Fee Paid</th>
			<th>Manage</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($members as $member) { ?>
		<tr>
			<td class="avatar"><img src="<?php echo gravatar($member->email, 22); ?>" alt="<?php echo $member->email; ?> gravatar"></td>
			<td><a href="/members/view/<?php echo $member->id; ?>"><?php echo $member->id; ?></a></td>
			<td><?php echo $member->firstname . ' ' . $member->lastname; ?></td>
			<td><?php echo $member->email; ?></td>
			<td><?php echo (!empty($member->phone) ? '<a href="callto:'.$member->phone.'">'.$member->phone.'</a>' : '<em>N/A</em>'); ?></td>
			<td><?php echo (!empty($member->mobile) ? '<a href="callto:'.$member->mobile.'">'.$member->mobile.'</a>' : '<em>N/A</em>'); ?></td>
			<td><?php echo date('Y-m-d', $member->registered); ?></td>
			<td><?php echo (!empty($member->membership) ? $member->membership : '<em>N/A</em>'); ?></td>
			<td><span class="badge<?php echo ((int)$member->active ? ' badge-success">Yes' : '">No'); ?></span></td>
			<td><span class="badge<?php echo ((int)$member->labaccess ? ' badge-success">Yes' : '">No'); ?></span></td>
			<td><span class="badge<?php echo ((int)$member->feepaid ? ' badge-success">Yes' : '">No'); ?></span></td>
			<td><a href="/members/view/<?php echo $member->id; ?>" class="btn btn-mini">View</a> <a href="/members/edit/<?php echo $member->id; ?>" class="btn btn-primary btn-mini">Edit</a></td>
		</tr>
	<?php } ?>
	</tbody>
</table>

<a href="/members/add" class="btn btn-primary pull-right">Add new member</a>
