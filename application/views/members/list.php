<h2 class="pull-left">List of Members <small>(<?php echo $this->db->count_all('members'); ?> members)</small></h2>

<div class="span6 pull-right">
	<a href="/members/add" class="btn btn-primary pull-right">Add new member</a>
	<?php echo form_open('members/search', 'class="form-search"'); ?>
		<input type="text" name="search" class="input-xlarge" required placeholder="Enter search string..." pattern=".{2,}" title="Minimum 2 characters">
		<button type="submit" class="btn">Search for members</button>
	<?php echo form_close(); ?>
</div>

<table class="table table-striped table-condensed table-bordered tablesorter">
	<thead>
		<tr>
			<th class="{sorter: false}">&nbsp;</th>
			<th>ID</th>
			<th>Name</th>
			<th>E-mail</th>
			<th>Phone</th>
			<th class="{sorter: false}">Sex</th>
			<th>Member Since</th>
			<th>Active Member</th>
			<th>Lab Access</th>
			<th class="{sorter: false}">Manage</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($members as $member) { ?>
		<tr>
			<td class="avatar"><img src="<?php echo gravatar($member->email, 22); ?>"></td>
			<td><a href="/members/view/<?php echo $member->id; ?>"><?php echo $member->id; ?></a></td>
			<td><?php echo $member->firstname . ' ' . $member->lastname; ?></td>
			<td><a href="mailto:<?php echo $member->email; ?>"><?php echo $member->email; ?></a></td>
			<td><?php echo (!empty($member->phone) ? '<a href="callto:'.$member->phone.'">'.$member->phone.'</a>' : '<em>N/A</em>'); ?></td>
			<td><?php echo ((!empty($member->civicregno) && substr($member->civicregno, -2) != 00) ? (substr($member->civicregno, -2, 1) % 2 ? '<i class="icon-male"></i>' : '<i class="icon-female"></i>') : ''); ?></td>
			<td><?php echo date('Y-m-d', $member->registered); ?></td>
			<td><span class="badge<?php echo (!empty($member->groups['member'.date('Y')]) ? ' badge-success">Yes' : '">No'); ?></span></td>
			<td><span class="badge<?php echo (!empty($member->groups['labaccess']) ? ' badge-success">Yes' : '">No'); ?></span></td>
			<td><a href="/members/view/<?php echo $member->id; ?>" class="btn btn-mini">View</a> <a href="/members/edit/<?php echo $member->id; ?>" class="btn btn-primary btn-mini">Edit</a></td>
		</tr>
	<?php } if(empty($members)) { ?>
		<tr>
			<td colspan="10">Sorry, couldn't find any members in the database.</td>
		</tr>
	<?php } ?>
	</tbody>
</table>
