<h2 class="pull-left">List of Members <small>(<?php echo $this->db->count_all('members'); ?> members)</small></h2>

<div class="span6 pull-right">
	<a href="/members/add" class="btn btn-primary pull-right">Add new member</a>
	<?php echo form_open('members/search', 'class="form-search"'); ?>
		<input type="text" class="input-xlarge" required placeholder="Enter search string..." pattern=".{2,}" title="Minimum 2 characters">
		<button type="submit" class="btn">Search for member </button>
	<?php echo form_close(); ?>
</div>
<table class="table table-striped table-condensed table-bordered">
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th>ID</th>
			<th>Name</th>
			<th>E-mail</th>
			<th>Mobile</th>
			<th>Alt. Phone</th>
			<th>Registered</th>
			<th>Membership Due</th>
			<th>Active</th>
			<th>Admin</th>
			<th>Lab Access</th>
			<th>Fee Paid</th>
			<th>Manage</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($members as $member) { ?>
		<tr>
			<td class="avatar"><img src="<?php echo gravatar($member->email, 22); ?>"></td>
			<td><a href="/members/view/<?php echo $member->id; ?>"><?php echo $member->id; ?></a></td>
			<td><?php echo $member->firstname . ' ' . $member->lastname; ?></td>
			<td><a href="mailto:<?php echo $member->email; ?>"><?php echo $member->email; ?></a></td>
			<td><?php echo (!empty($member->mobile) ? '<a href="callto:'.$member->mobile.'">'.$member->mobile.'</a>' : '<em>N/A</em>'); ?></td>
			<td><?php echo (!empty($member->phone) ? '<a href="callto:'.$member->phone.'">'.$member->phone.'</a>' : '<em>N/A</em>'); ?></td>
			<td><?php echo date('Y-m-d', $member->registered); ?></td>
			<td><?php echo (!empty($member->membership) ? $member->membership : '<em>N/A</em>'); ?></td>
			<td><span class="badge<?php echo ($member->acl->active ? ' badge-success">Yes' : '">No'); ?></span></td>
			<td><span class="badge<?php echo ($member->acl->admin ? ' badge-success">Yes' : '">No'); ?></span></td>
			<td><span class="badge<?php echo ($member->acl->labaccess ? ' badge-success">Yes' : '">No'); ?></span></td>
			<td><span class="badge<?php echo ($member->acl->feepaid ? ' badge-success">Yes' : '">No'); ?></span></td>
			<td><a href="/members/view/<?php echo $member->id; ?>" class="btn btn-mini">View</a> <a href="/members/edit/<?php echo $member->id; ?>" class="btn btn-primary btn-mini">Edit</a></td>
		</tr>
	<?php } if(empty($members)) { ?>
		<tr>
			<td colspan="13">Sorry, couldn't find any members in the database.</td>
		</tr>
	<?php } ?>
	</tbody>
</table>

<a href="/members/add" class="btn btn-primary pull-right">Add new member</a>
