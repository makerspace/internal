<h2>Finance</h2>
<p class="well"><strong>Warning:</strong> This section is HIGHLY beta and may or may not contain correct data.</p>

<table class="table table-striped table-condensed table-bordered tablesorter">
	<thead>
		<tr>
			<th>ID</th>
			<th>Member ID</th>
			<th>Created</th>
			<th>Type</th>
			<th>Status</th>
			<th>Amount</th>
			<th>Months</th>
			<th>Data (raw)</th>
			<th class="{sorter: false}">Manage</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($transactions as $trans) { ?>
		<tr>
			<td><?php echo $trans->id; ?></td>
			<td><a href="/members/view/<?php echo $trans->member_id; ?>"><?php echo $trans->member_id; ?></a></td>
			<td><?php echo date('Y-m-d H:i:s', $trans->timestamp); ?></td>
			<td><?php echo $trans->type; ?></td>
			<td><?php echo $trans->status; ?></td>
			<td><?php echo $trans->amount; ?> SEK</td>
			<td><?php echo ((!empty($trans->data) && $trans->data != 'null') ? json_decode($trans->data)->months : ''); ?></td>
			<td><?php echo ((!empty($trans->data) && $trans->data != 'null') ? $trans->data : ''); ?></td>
			<td><a href="#" class="btn btn-mini disabled">N/A</a></td>
		</tr>
	<?php } if(empty($transactions)) { ?>
		<tr>
			<td colspan="10">Sorry, couldn't find any transactions in the database.</td>
		</tr>
	<?php } ?>
	</tbody>
</table>
